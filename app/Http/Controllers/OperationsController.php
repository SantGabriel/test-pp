<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use App\Repository\Repository;

class OperationsController extends Controller
{
    public function __construct(private TransferService $transferService)
    {

    }
    public function transfer(TransferRequest $request)
    {
        $userFromId = (int) $request->post('userFrom');

        $userToReceiveId = (int) $request->post('userToReceive');
        $value = (float) $request->post('value');
        try {
            $this->transferService->execute($userFromId, $userToReceiveId, $value);
        }catch (Exception $exception ) {
            return response()->json(['message' => $exception->getMessage()], $exception->getCode() ?: 400);
        }

        return response()->json(['message' => 'Transfer completed'], 200);
    }
}
