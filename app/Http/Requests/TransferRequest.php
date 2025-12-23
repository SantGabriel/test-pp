<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{

    public function rules() {
        return [
            'userToReceive' => ['required', 'integer', 'different:userFrom'],
            'userFrom' => ['required', 'integer'],
            'value' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}

