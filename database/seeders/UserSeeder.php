<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        \Database\Factories\UserFactory::new()->count(5)->create();
    }
}

