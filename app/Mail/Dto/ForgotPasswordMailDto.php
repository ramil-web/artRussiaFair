<?php


namespace App\Mail\Dto;


class ForgotPasswordMailDto
{
    private string $link;
    private string $email;

    public function __construct(string $link, string $email)
    {
        $this->email = $email;
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }
}
