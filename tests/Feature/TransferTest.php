<?php

namespace Tests\Feature;

use App\Enum\UserType;
use App\Jobs\NotificationJob;
use Illuminate\Support\Facades\Bus;
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
            'userToReceive' => $userToReceive->id,
            'userFrom' => $userFrom->id,
            'value' => $valor,
        ]);
        $response->assertContent(Json::encode([
            "message" => 'Payer is not allowed to send money'
        ]));
    }

    public function test_check_transfer_fail()
    {
        $userFrom = User::factory()->CPFOrCNPJ(UserType::COMUM)->create([
            'name' => 'Fail Checker',
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
        $response->assertContent(Json::encode([
            "message" => 'Check failed'
        ]));
    }

    public function test_faz_uma_transferencia_entre_dois_usuarios()
    {
        Bus::fake();
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
        $response = $this->post('api/transfer?XDEBUG_SESSION_START=PHPSTORM', $transferData);

        if($response->status() == 200){
            $response->assertStatus(200);
            $this->assertDatabaseHas('transfer', $transferData);
            $this->assertEquals(200 - $valor, $userFrom->fresh()->balance);
            $this->assertEquals(50 + $valor, $userToReceive->fresh()->balance);
            Bus::assertDispatched(NotificationJob::class);
            echo("Transferência realizada com sucesso!\n");
        }else {
            $this->assertDatabaseMissing('transfer', $transferData);
            $this->assertEquals(200, $userFrom->fresh()->balance);
            $this->assertEquals(50, $userToReceive->fresh()->balance);
            echo("Falha na transferência!\n");
        }

    }
}

