<?php

namespace App\Mail;

use App\Models\Container;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContainerProvisioned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Container $container) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Container Siap Digunakan - ' . $this->container->hostname,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.container-provisioned',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
