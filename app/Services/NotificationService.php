<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function notificate($to, $message) {
        Http::timeout(5)->post('https://util.devi.tools/api/v1/notify', [
            'to' => $to,
            'message' => $message
        ]);
    }
}
