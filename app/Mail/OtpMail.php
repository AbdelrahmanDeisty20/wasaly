<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $name,
        public string $type,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'register' => __('messages.otp_subject_register'),
            'reset_password' => __('messages.otp_subject_reset_password'),
            'resend' => __('messages.otp_subject_resend'),
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? __('messages.otp_subject_register'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}
