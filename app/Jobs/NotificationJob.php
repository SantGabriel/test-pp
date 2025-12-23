<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;
    public int $backoff = 10;
    /**
     * Create a new job instance.
     */
    public function __construct(private string $email, private string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle( NotificationService $notificationService): void
    {
        $currentAttempt = $this->attempts();
        $response = $notificationService->notificate($this->email, $this->message);
        if (!$response) {
            Log::info("Serviço de notificação falhou. Tentaremos na proxima vez ({$currentAttempt}º tentativa) " );
            $this->release($this->backoff);
            return;
        }else {
            Log::info("Notificação enviada com sucesso para $this->email" );
        }
    }

    public function failed(): void
    {
        Log::info("Máximo de tentativas alcançada. Não tentaremos avisa o $this->email da mensagem: $this->message" );
    }
}
