<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    public function handle(string $email, string $password, bool $remember = true): void
    {
        if (! Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    }
}
