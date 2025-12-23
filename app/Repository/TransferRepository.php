<?php

namespace App\Repository;

use App\Models\Transfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferRepository
{
    public function __construct(private UserRepository $userRepository)
    {

    }
    public function transfer(int $userFromId, int $userToReceiveId, float $value): ?array
    {
        [$userFrom, $userToReceive] = DB::transaction(function () use ($userFromId, $userToReceiveId, $value) {
            $userFrom = $this->userRepository->getAccount($userFromId, true);
            $userToReceive = $this->userRepository->getAccount($userToReceiveId, true);
            if ($userFrom->balance < $value)
                throw new Exception('Insufficient funds', 422);
            $userFrom->balance = round($userFrom->balance - $value,2);
            $userToReceive->balance = round($userToReceive->balance + $value,2);

            $userFrom->save();
            $userToReceive->save();

            return [$userFrom, $userToReceive];
        });
        if(isset($userFrom)) {
            Transfer::create([
                'userFrom' => $userFrom->id,
                'userToReceive' => $userToReceive->id,
                'value' => $value,
            ]);
        }
        return [$userFrom, $userToReceive];
    }
}
