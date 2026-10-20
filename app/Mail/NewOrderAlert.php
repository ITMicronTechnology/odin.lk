<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the store about a new order. Sent to config('shop.order_alert_email').
 *
 * Implement ShouldQueue to send it in the background once a queue worker is running.
 */
class NewOrderAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Order $order) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New order {$this->order->order_number} – ".config('shop.currency').' '.number_format((float) $this->order->total, 2),
            replyTo: [$this->order->customer_email],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.orders.alert',
            with: [
                'items' => $this->order->items()->get(),
                'adminUrl' => route('admin.orders.show', $this->order),
            ],
        );
    }
}
