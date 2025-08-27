<?php

namespace App\Mail;

use App\Mail\Dto\ForgotPasswordMailDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private ForgotPasswordMailDto $forgotPasswordMailDto;

    public function __construct(ForgotPasswordMailDto $forgotPasswordMailDto)
    {
        $this->forgotPasswordMailDto = $forgotPasswordMailDto;
    }

    public function build()
    {
        return $this->view('emails.forgot-password-mail')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->forgotPasswordMailDto->getEmail())
            ->subject('Восстановление пароля учетной записи на сайте artrussiafair.com')
            ->with([
                'link' => $this->forgotPasswordMailDto->getLink(),
            ]);
    }
}
