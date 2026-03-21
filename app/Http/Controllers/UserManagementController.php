<?php

namespace App\Http\Controllers;

use App\Actions\Users\CreateUser;
use App\Actions\Users\DeleteUser;
use App\Actions\Users\ListUsers;
use App\Actions\Users\UpdateUser;
use App\Http\Requests\UserManagementRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(ListUsers $listUsers): Response
    {
        return Inertia::render('Users/Index', $listUsers->handle());
    }

    public function store(UserManagementRequest $request, CreateUser $createUser): RedirectResponse
    {
        $createUser->handle($request->validated());

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(UserManagementRequest $request, User $user, UpdateUser $updateUser): RedirectResponse
    {
        $updateUser->handle($user, $request->validated());

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user, DeleteUser $deleteUser): RedirectResponse
    {
        $result = $deleteUser->handle($request->user(), $user);

        if (! $result['ok']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
