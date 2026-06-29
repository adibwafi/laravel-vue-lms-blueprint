<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->details['type']) {
            case 'verify-email':
                $view = 'emails.Verification';
                break;
            case 'reset-password':
                $view = 'emails.ResetPassword';
                break;

            default:
                $view = 'emails.Verification';
                break;
        }

        return $this->subject($this->details['subject'])
            ->view($view);
    }
}
