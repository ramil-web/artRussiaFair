<?php

namespace App\Mail;

use App\Mail\Dto\RegistrationMailDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    private RegistrationMailDto $registrationMailDto;

    public function __construct(RegistrationMailDto $registrationMailDto)
    {
        $this->registrationMailDto = $registrationMailDto;
    }

    public function build()
    {
        return $this->view('emails.registration-mail')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->registrationMailDto->getEmail())
            ->subject('Доступ в личный кабинет Art Russia 2024')
            ->with([
                'email' => $this->registrationMailDto->getEmail(),
                'password' => $this->registrationMailDto->getPassword()
            ]);
    }
}
