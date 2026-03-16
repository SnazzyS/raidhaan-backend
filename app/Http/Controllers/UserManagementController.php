<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->orderByRaw("case when role = 'admin' then 0 else 1 end")
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($validated);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $this->ensureAdminRoleIsRetained($user, $validated['role']);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && $this->remainingAdminCountExcluding($user) === 0) {
            return redirect()->back()->with('error', 'At least one admin user must remain.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User removed successfully.');
    }

    private function ensureAdminRoleIsRetained(User $user, string $newRole): void
    {
        if (!$user->isAdmin() || $newRole === 'admin') {
            return;
        }

        if ($this->remainingAdminCountExcluding($user) === 0) {
            throw ValidationException::withMessages([
                'role' => 'At least one admin user must remain.',
            ]);
        }
    }

    private function remainingAdminCountExcluding(User $user): int
    {
        return User::query()
            ->where('role', 'admin')
            ->whereKeyNot($user->id)
            ->count();
    }
}
