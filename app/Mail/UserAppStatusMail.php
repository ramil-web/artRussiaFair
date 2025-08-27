<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAppStatusMail extends Mailable
{

    use Queueable, SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): UserAppStatusMail
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->data['data']['email'])
            ->subject($this->data['subject'])
            ->view($this->data['template'], [
                'url' => $this->data['url']
            ]);
    }
}
