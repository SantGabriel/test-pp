<?php

namespace Tests\Feature;

use App\Enum\UserType;
use Illuminate\Testing\TestResponse;
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
        $this->assertDatabaseMissing('transfer', $transferData);
        $this->assertEquals(200, $userFrom->fresh()->balance);
        $this->assertEquals(50, $userToReceive->fresh()->balance);
    }

    public function test_succesful_transfer()
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
        $response = $this->succesful_transfer($transferData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transfer', $transferData);
        $this->assertEquals(200 - $valor, $userFrom->fresh()->balance);
        $this->assertEquals(50 + $valor, $userToReceive->fresh()->balance);

    }

    public function succesful_transfer(array $transferData): TestResponse
    {
        $maxAttempts = 10;
        $attempt = 0;
        $response = null;
        while ($attempt < $maxAttempts) {
            $attempt++;

            $response = $this->post('api/transfer?XDEBUG_SESSION_START=PHPSTORM', $transferData);

            if ($response->status() == 200) {
                break;
            }

            sleep(1);
        }
        return $response;
    }
}

