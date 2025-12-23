<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_cria_um_usuario()
    {
        $user = User::factory()->create([
            'balance' => 100.00,
            'type' => 'comum',
        ]);

        $this->assertDatabaseHas('user', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}

