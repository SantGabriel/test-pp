<?php

namespace App\Repository;

use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferRepository
{
    public function transfer(User $from, User $to, float $value): bool
    {
        $result = DB::transaction(function () use ($from, $to, $value) {
            if ($from->balance < $value)
                return false;
            $from->balance = round($from->balance - $value,2);
            $to->balance = round($to->balance + $value,2);

            $from->save();
            $to->save();

            return true;
        });
        if($result) {
            Transfer::create([
                'userFrom' => $from->id,
                'userToReceive' => $to->id,
                'value' => $value,
            ]);
        }
        return $result;
    }
}
