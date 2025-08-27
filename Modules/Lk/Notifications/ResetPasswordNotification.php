<?php

namespace Lk\Notifications;

use App\Mail\Dto\ForgotPasswordMailDto;
use App\Mail\ForgotPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $url;
    private string $email;

    public function __construct(string $url, string $email)
    {
        //
        $this->url = $url;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return new ForgotPasswordMail(new ForgotPasswordMailDto($this->url, $this->email));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
