<?php

namespace App\Policies;

use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }
}
