<?php

namespace App\Repository;

use App\Models\User;

class UserRepository
{

    public function getAccount(int $id, bool $lock = false): ?User
    {
        $query = User::where('id',$id);
        if($lock) {
            $query->lockForUpdate();
        }
        return $query->first();
    }
}
