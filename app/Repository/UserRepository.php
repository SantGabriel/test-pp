<?php

namespace App\Repository;

use App\Models\User;

class UserRepository
{

    public function getAccount(int $id): ?User
    {
        return User::find($id);
    }
}
