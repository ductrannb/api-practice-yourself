<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseTest extends TestCase
{
    use RefreshDatabase;
    protected function getToken(User $user = null)
    {
        if (!$user) {
            $user = User::factory()->create();
        }
        return auth()->login($user);
    }
}
