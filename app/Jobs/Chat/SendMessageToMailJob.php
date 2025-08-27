<?php

namespace App\Jobs\Chat;

use App\Exceptions\CustomException;
use App\Mail\SendMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SendMessageToMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public string $subject;
    protected string $template;
    protected array $data;

    public int $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $template, array $data)
    {
        $this->data = [
            'subject'  => $subject,
            'template' => $template,
            'data'     => $data,
            'email'    => $data['email']
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws CustomException
     */
    public function handle(): void
    {

        try {
            $this->sendMail();
        } catch (Throwable $e) {
            if ($this->attempts() > 5) {
                throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
            $this->release(300);
            return;
        }
    }

    /**
     * @return void
     * @throws CustomException
     */
    private function sendMail(): void
    {
        $email = $this->data['data']['email'];
        try {
            Mail::to($email)->queue(new SendMail($this->data));
            Log::info('Письмо поставлено в очередь для email: ' . $email);
            Log::channel('email-log')->info('Письмо поставлено в очередь для email: ' . $email);
        } catch (Exception $e) {
            Log::error('Ошибка при постановке письма в очередь: ' . $e->getMessage() . ' Email: ' . $email);
            Log::channel('email-log')->error('Ошибка при постановке письма в очередь: ' . $e->getMessage() . ' Email: ' . $email);
            throw new CustomException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
