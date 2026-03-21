<?php

namespace App\Policies;

use App\Models\User;

class RestaurantSettingPolicy
{
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }
}
