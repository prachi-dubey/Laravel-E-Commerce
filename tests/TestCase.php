<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    protected function createCustomer(): User
    {
        return User::factory()->customer()->create([
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    protected function actingAsAdmin(): User
    {
        $user = $this->createAdmin();
        Sanctum::actingAs($user);

        return $user;
    }

    protected function actingAsCustomer(): User
    {
        $user = $this->createCustomer();
        Sanctum::actingAs($user);

        return $user;
    }
}
