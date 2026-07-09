<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data): array;

    public function login(array $credentials): array;

    public function logout(User $user): void;
}
