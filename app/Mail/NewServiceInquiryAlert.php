<?php

namespace App\Mail;

use App\Models\ServiceInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the store about a new IT service quote request. Sent to config('shop.order_alert_email').
 *
 * Implement ShouldQueue to send it in the background once a queue worker is running.
 */
class NewServiceInquiryAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public ServiceInquiry $inquiry) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New quote request {$this->inquiry->reference} – {$this->inquiry->name}",
            replyTo: [$this->inquiry->email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.service-inquiries.alert',
            with: [
                'adminUrl' => route('admin.service-inquiries.show', $this->inquiry),
            ],
        );
    }
}
