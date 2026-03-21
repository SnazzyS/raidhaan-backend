<?php

namespace App\Actions\Users;

use App\Models\User;

class CreateUser
{
    public function handle(array $validated): User
    {
        return User::create($validated);
    }
}
