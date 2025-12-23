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

        $this->assertDatabaseHas('account', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function test_factory_cria_dois_usuarios()
    {
        $users = User::factory()->count(2)->create();

        $this->assertCount(2, $users);
        $this->assertDatabaseHas('account', ['id' => $users[0]->id]);
        $this->assertDatabaseHas('account', ['id' => $users[1]->id]);
    }
}

