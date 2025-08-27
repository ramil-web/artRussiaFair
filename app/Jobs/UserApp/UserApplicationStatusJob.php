<?php

namespace App\Jobs\UserApp;

use App\Mail\SendMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserApplicationStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $subject;
    protected string $template;
    protected array $data;

    public function __construct(string $subject, string $template, array $data)
    {
        $this->data = [
            'subject' => $subject,
            'template' => $template,
            'data' => $data
        ];
    }

    public function handle(): void
    {
        $email =  $this->data['data']['email'];
        try {
            Mail::to($email)->queue(new SendMail($this->data));
            if (Mail::failures()) {
                Log::info('Письмо не отправлено на email: ' . $email);
            } else {
                Log::info('Письмо успешно отправлено на email: ' . $email);
            }
        } catch (Exception $exception) {
            Log::info('Письмо не отправлено на email: ' . $email);
        }
    }
}
