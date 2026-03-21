<?php

namespace App\Actions\Users;

use App\Models\User;

class ListUsers
{
    public function handle(): array
    {
        return [
            'users' => User::query()
                ->orderByRaw("case when role = 'admin' then 0 else 1 end")
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role', 'created_at']),
        ];
    }
}
