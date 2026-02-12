<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Offer $offer;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Alerte Offre : ' . ($this->offer->title ?? 'Offre sans titre'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $technicalAlert = null;
        $financialAlert = null;

        if ($this->offer->technicalOffer) {
            $technicalAlert = [
                'deadline' => $this->offer->technicalOffer->deadline,
                'internalControlDate' => $this->offer->technicalOffer->internal_control_date,
                'daysUntilDeadline' => $this->offer->technicalOffer->deadline ? now()->diffInDays($this->offer->technicalOffer->deadline, false) : null,
                'daysUntilControl' => $this->offer->technicalOffer->internal_control_date ? now()->diffInDays($this->offer->technicalOffer->internal_control_date, false) : null,
            ];
        }

        if ($this->offer->financialOffer) {
            $financialAlert = [
                'deadline' => $this->offer->financialOffer->deadline,
                'internalControlDate' => $this->offer->financialOffer->internal_control_date,
                'daysUntilDeadline' => $this->offer->financialOffer->deadline ? now()->diffInDays($this->offer->financialOffer->deadline, false) : null,
                'daysUntilControl' => $this->offer->financialOffer->internal_control_date ? now()->diffInDays($this->offer->financialOffer->internal_control_date, false) : null,
            ];
        }

        return new Content(
            view: 'emails.alerts.offer',
            with: [
                'offer' => $this->offer,
                'title' => $this->offer->title ?? 'Offre sans titre',
                'technicalAlert' => $technicalAlert,
                'financialAlert' => $financialAlert,
            ],
        );
    }
}
