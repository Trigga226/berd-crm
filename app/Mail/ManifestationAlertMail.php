<?php

namespace App\Mail;

use App\Models\Manifestation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManifestationAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Manifestation $manifestation;

    /**
     * Create a new message instance.
     */
    public function __construct(Manifestation $manifestation)
    {
        $this->manifestation = $manifestation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = $this->manifestation->avisManifestation->title ?? 'Manifestation sans titre';

        return new Envelope(
            subject: '🚨 Alerte Manifestation : ' . $title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alerts.manifestation',
            with: [
                'manifestation' => $this->manifestation,
                'title' => $this->manifestation->avisManifestation->title ?? 'Manifestation sans titre',
                'status' => $this->manifestation->status,
                'deadline' => $this->manifestation->deadline,
                'internalControlDate' => $this->manifestation->internal_control_date,
                'daysUntilDeadline' => $this->manifestation->deadline ? now()->diffInDays($this->manifestation->deadline, false) : null,
                'daysUntilControl' => $this->manifestation->internal_control_date ? now()->diffInDays($this->manifestation->internal_control_date, false) : null,
            ],
        );
    }
}
