<?php

namespace App\Jobs\Mail;

use App\Mail\SendMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject;
    protected string $template;
    protected array $data;

    public function __construct(string $subject, string $template, array $data)
    {
        $this->data = [
            'subject'  => $subject,
            'template' => $template,
            'data'     => $data
        ];
    }

    public function handle()
    {
        /**
         * Делаем возможным отправить письмо сразу многим
         */
        $email = !is_array($this->data['data']['email']) ? [$this->data['data']['email']] : $this->data['data']['email'];
        try {
            Mail::to($email)->queue(new SendMail($this->data));

            if (Mail::failures()) {
                Log::info('Письмо не отправлено на email: ' . $email[0]);
            } else {
                Log::info('Письмо успешно отправлено на email: ' . $email[0]);
            }
        } catch (Exception $exception) {
            Log::info('Письмо не отправлено на email: ' . $email[0]);
        }
    }

}
