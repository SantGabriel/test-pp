<?php

namespace Tests\Feature;

use App\Enum\UserType;
use Psy\Util\Json;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_allowed_transfer_from_lojista()
    {
        $userFrom = User::factory()->CPFOrCNPJ(UserType::LOJISTA)->create([
            'balance' => 200.00,
            'type' => UserType::LOJISTA,
        ]);

        $userToReceive = User::factory()->CPFOrCNPJ(UserType::COMUM)->create([
            'balance' => 50.00,
            'type' => UserType::COMUM,
        ]);

        $valor = 100.00;

        $response = $this->post('/api/transfer', [
            'userToReceive' => $userFrom->id,
            'userFrom' => $userToReceive->id,
            'value' => $valor,
        ]);
        $response->assertContent(Json::encode([
            "message" => 'Payer is not allowed to send money'
        ]));
    }

    public function test_faz_uma_transferencia_entre_dois_usuarios()
    {
        $userFrom = User::factory()->CPFOrCNPJ(UserType::COMUM)->create([
            'balance' => 200.00,
            'type' => UserType::COMUM,
        ]);

        $userToReceive = User::factory()->CPFOrCNPJ(UserType::LOJISTA)->create([
            'balance' => 50.00,
            'type' => UserType::LOJISTA,
        ]);

        $valor = 100.00;

        $transferData = [
            'userToReceive' => $userToReceive->id,
            'userFrom' => $userFrom->id,
            'value' => $valor,
        ];
        $response = $this->post('api/transfer', $transferData);

        if($response->status() == 200){
            $response->assertStatus(200);
            $this->assertDatabaseHas('transfer', $transferData);
            $this->assertEquals(200 - $valor, $userFrom->fresh()->balance - $valor);
            $this->assertEquals(50 + $valor, $userToReceive->fresh()->balance + $valor);
        }else {
            $this->assertDatabaseMissing('transfer', $transferData);
            $this->assertEquals(200, $userFrom->fresh()->balance);
            $this->assertEquals(50, $userToReceive->fresh()->balance);
        }

    }
}

