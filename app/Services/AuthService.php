<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository,
    ) {}

    public function register(array $data): array
    {
        return $this->authRepository->register($data);
    }

    public function login(array $credentials): array
    {
        $result = $this->authRepository->login($credentials);

        if (! $result['user']) {
            throw new CustomException(__('messages.invalid_credentials'), Response::HTTP_UNAUTHORIZED);
        }

        return $result;
    }

    public function logout(User $user): void
    {
        $this->authRepository->logout($user);
    }
}
