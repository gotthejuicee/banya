<?php

namespace Tests\Concerns;

use App\Models\User;

trait CreatesAdmin
{
    protected function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Тест Адмін',
            'email' => 'admin@test.local',
        ]);
    }
}