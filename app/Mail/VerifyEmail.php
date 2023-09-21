<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationLink;

    /**
     * Create a new message instance.
     *
     * @param string $userId
     */
    public function __construct($userId)
    {
        $this->verificationLink = $this->generateVerificationLink($userId);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.verify_email');
    }

    protected function generateVerificationLink($userId)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $userId]
        );
    }
}
