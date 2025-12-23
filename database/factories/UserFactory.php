<?php

namespace Database\Factories;

use App\Enum\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(UserType::cases());
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'cpf_cnpj' => fake()->boolean() ? fake()->regexify('[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}') : fake()->regexify('[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}-[0-9]{2}'),
            'balance' => 1000.0,
            'type' => $this->faker->randomElement(UserType::cases()),
        ];
    }

    public function CPFOrCNPJ(UserType $userType): self
    {
        return $this->state(fn () => [
            'cpf_cnpj' => $userType == UserType::COMUM
                ? fake()->regexify('[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}')
                : fake()->regexify('[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}-[0-9]{2}'),
        ]);
    }
}
