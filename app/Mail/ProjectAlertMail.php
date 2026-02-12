<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProjectAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $alertType;
    public Collection $alerts;

    /**
     * Create a new message instance.
     */
    public function __construct(string $alertType, Collection $alerts)
    {
        $this->alertType = $alertType;
        $this->alerts = $alerts;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Alerte Projet : ' . $this->alertType . ' (' . $this->alerts->count() . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alerts.project',
            with: [
                'alertType' => $this->alertType,
                'alerts' => $this->alerts,
                'count' => $this->alerts->count(),
            ],
        );
    }
}
