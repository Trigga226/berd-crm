<?php

namespace App\Services;

use App\Mail\ManifestationAlertMail;
use App\Mail\OfferAlertMail;
use App\Mail\ProjectAlertMail;
use App\Models\Manifestation;
use App\Models\Offer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class AlertEmailService
{
    // protected string $recipientEmail = 'personnel@berd-ing.com';
    protected string $recipientEmail = 'franck.b@berd-ing.com';
    protected string $ccEmail = 'lionel.palenfo@gmail.com';

    /**
     * Send email for manifestation alerts
     */
    public function sendManifestationAlerts(Collection $manifestations): void
    {
        if ($manifestations->isEmpty()) {
            return;
        }

        foreach ($manifestations as $manifestation) {
            Mail::to($this->recipientEmail)
                ->cc($this->ccEmail)
                ->send(new ManifestationAlertMail($manifestation));
        }
    }

    /**
     * Send email for offer alerts
     */
    public function sendOfferAlerts(Collection $offers): void
    {
        if ($offers->isEmpty()) {
            return;
        }

        foreach ($offers as $offer) {
            Mail::to($this->recipientEmail)
                ->cc($this->ccEmail)
                ->send(new OfferAlertMail($offer));
        }
    }

    /**
     * Send email for project alerts
     */
    public function sendProjectAlerts(Collection $alerts): void
    {
        if ($alerts->isEmpty()) {
            return;
        }

        // Group alerts by type for better email organization
        $groupedAlerts = $alerts->groupBy('type');

        foreach ($groupedAlerts as $type => $typeAlerts) {
            Mail::to($this->recipientEmail)
                ->cc($this->ccEmail)
                ->send(new ProjectAlertMail($type, $typeAlerts));
        }
    }
}
