<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transfer;
use App\Models\User;

class TransferSeeder extends Seeder
{
    public function run()
    {
        // cria usuários caso não existam
        if (User::count() < 2) {
            User::factory()->count(5)->create();
        }

        $users = User::all();

        // cria 10 transfers entre usuários existentes (garantindo userFrom != userToReceive)
        for ($i = 0; $i < 10; $i++) {
            $userFrom = $users->random();
            $userToReceive = $users->random();
            while ($userToReceive->id === $userFrom->id) {
                $userToReceive = $users->random();
            }

            Transfer::create([
                'userFrom' => $userFrom->id,
                'userToReceive' => $userToReceive->id,
                'date' => now(),
                'value' => mt_rand(100, 10000) / 100,
            ]);
        }
    }
}

