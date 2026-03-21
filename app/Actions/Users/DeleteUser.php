<?php

namespace App\Actions\Users;

use App\Models\User;

class DeleteUser
{
    public function handle(?User $actingUser, User $user): array
    {
        if ($actingUser?->is($user)) {
            return [
                'ok' => false,
                'message' => 'You cannot delete your own account.',
            ];
        }

        if ($user->role === 'admin' && $this->remainingAdminCountExcluding($user) === 0) {
            return [
                'ok' => false,
                'message' => 'At least one admin user must remain.',
            ];
        }

        $user->delete();

        return [
            'ok' => true,
            'message' => 'User removed successfully.',
        ];
    }

    private function remainingAdminCountExcluding(User $user): int
    {
        return User::query()
            ->where('role', 'admin')
            ->whereKeyNot($user->id)
            ->count();
    }
}
