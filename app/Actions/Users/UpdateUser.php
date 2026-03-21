<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UpdateUser
{
    public function handle(User $user, array $validated): User
    {
        $this->ensureAdminRoleIsRetained($user, $validated['role']);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return $user->fresh();
    }

    private function ensureAdminRoleIsRetained(User $user, string $newRole): void
    {
        if ($user->role !== 'admin' || $newRole === 'admin') {
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
