<?php

namespace App\Mail;

use App\Models\User;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User $username */
    private $username;
    /** @var string $activationUrl */
    private $activationUrl;

    /**
     * Create a new message instance.
     *
     * @param string $username
     * @param string $activationUrl
     */
    public function __construct(string $username, string $activationUrl)
    {
        $this->username = $username;
        $this->activationUrl = $activationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('senhung.net register verification')
            ->from(MailService::EMAIL_FROM)
            ->markdown('emails.auth.verify', [
                'username' => $this->username,
                'activationUrl' => $this->activationUrl,
            ]);
    }
}
