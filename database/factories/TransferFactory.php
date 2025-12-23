<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transfer;
use App\Models\User;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition()
    {
        // cria dois users distintos automaticamente ao criar a transfer
        return [
            'userFrom' => Transfer::factory(),
            'userToReceive' => Transfer::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'value' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}

