<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Markdown;
use Illuminate\Mail\Mailables\Text;
use Illuminate\Support\HtmlString;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $subject;
    public $message;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $message
     */
    public function __construct(string $subject, string $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('emails.welcome')
            ->with([
                'message' => $this->message,
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'projet de fin d etude',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->view) {
            return Markdown::fromView($this->view, $this->viewData);
        }

        if ($this->textView) {
            return Text::fromView($this->textView, $this->viewData);
        }

        return new Content($this->textView ?: $this->view, $this->viewData['html'] ?? null);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
