<?php

namespace App\Jobs\Mail;

use App\Mail\Dto\RegistrationMailDto;
use App\Mail\RegistrationMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $password;
    private string $email;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function handle()
    {
        $dto = new RegistrationMailDto($this->email, $this->password);

        try {
            Mail::queue(new RegistrationMail($dto));

            if (Mail::failures()) {
                Log::info('Письмо не отправлено на email: ' . $this->email);
            } else {
                Log::info('Письмо успешно отправлено на email: ' . $this->email);
            }

        } catch (Exception $exception) {
            Log::info('Письмо не отправлено на email: ' . $this->email);
        }
    }
}
