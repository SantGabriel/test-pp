<?php

namespace App\Services;

use App\Enum\UserType;
use App\Jobs\NotificationJob;
use App\Models\User;
use App\Repository\TransferRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class TransferService
{

    public function __construct(private TransferRepository $transferRepository,
                                private UserRepository $userRepository)
    {
    }

    /**
     * Executa a transferência
     *
     * @throws \Exception
     */
    public function execute(int $userFromId, int $userToReceiveId, float $value): bool
    {
        // busca contas
        $userFrom = $this->userRepository->getAccount($userFromId);
        $userToReceive = $this->userRepository->getAccount($userToReceiveId);

        if(!isset($userFrom) )
            throw new Exception('Payer do not exist', 422);
        if(!isset($userToReceive) )
            throw new Exception('Payee not found', 422);

        if ($userFrom->type != UserType::COMUM)
            throw new Exception('Payer is not allowed to send money', 422);

        if(!$this->check($userFrom))
            throw new Exception('Check failed', 422);

        [$userFrom, $userToReceive] = $this->transferRepository->transfer($userFromId, $userToReceiveId, $value);

        if(!isset($userFrom)) throw new Exception('Transfer failed', 422);

        NotificationJob::dispatch($userToReceive->email, "Você recebeu R$ {$value} de {$userFrom->name}");

        return true;
    }

    private function check(?User $userFrom): bool
    {
        if($userFrom->name == "Fail Checker") return false;
        $authRes = Http::timeout(5)->get('https://util.devi.tools/api/v2/authorize');
        if (!$authRes->successful()) {
            return false;
        }
        $status = $authRes->json('status');

        if ($status !== "success") return false;
        else return true;
    }
}

