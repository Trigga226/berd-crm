<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Collection;

class OfferAlertService
{
    /**
     * Get alerts for a specific user based on their role in offers.
     *
     * @param User $user
     * @return Collection
     */
    public function getAlerts(User $user): Collection
    {
        $alerts = collect();

        // Retrieve offers where the user is 'charge_etude' or 'assistant'
        $offers = Offer::whereHas('users', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                ->whereIn('role', ['charge_etude', 'assistant']);
        })->with(['technicalOffer', 'financialOffer'])->get();

        foreach ($offers as $offer) {
            // Check Technical Offer
            if ($offer->technicalOffer) {
                if ($this->shouldAlert($offer->technicalOffer)) {
                    $alerts->push([
                        'type' => 'Technique',
                        'offer_title' => $offer->title ?? 'Offre sans titre',
                        'deadline' => $offer->technicalOffer->deadline,
                        'internal_control_date' => $offer->technicalOffer->internal_control_date,
                        'status' => 'En cours', // Or actual status if available
                        'url' => \App\Filament\Resources\Offers\OfferResource::getUrl('view', ['record' => $offer->id]),
                    ]);
                }
            }

            // Check Financial Offer
            if ($offer->financialOffer) {
                if ($this->shouldAlert($offer->financialOffer)) {
                    $alerts->push([
                        'type' => 'Financière',
                        'offer_title' => $offer->title ?? 'Offre sans titre',
                        'deadline' => $offer->financialOffer->deadline,
                        'internal_control_date' => $offer->financialOffer->internal_control_date,
                        'status' => 'En cours', // Or actual status if available
                        'url' => \App\Filament\Resources\Offers\OfferResource::getUrl('view', ['record' => $offer->id]),
                    ]);
                }
            }
        }

        return $alerts;
    }

    /**
     * Apply alert filters to an Offer query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyAlertFilters($query)
    {
        return $query
            // Filter out completed offers if applicable, but Offer model doesn't seem to have the same status enum as Manifestation. 
            // Manifestation filters out ['won', 'lost', 'abandoned', 'submitted'].
            // Offer seems to have technical/financial offers which have 'submission_date'.
            // Let's assume we want valid active offers. User didn't specify, but let's stick to date logic for now to fix the "not red" issue first.
            ->where(function ($q) {
                // Check Technical Offer condition
                $q->whereHas('technicalOffer', function ($sub) {
                    $sub->whereDate('deadline', '<=', now()->addDays(4))
                        ->orWhereDate('internal_control_date', '<=', now()->addDays(2));
                })
                    // OR Check Financial Offer condition
                    ->orWhereHas('financialOffer', function ($sub) {
                        $sub->whereDate('deadline', '<=', now()->addDays(4))
                            ->orWhereDate('internal_control_date', '<=', now()->addDays(2));
                    });
            });
    }

    /**
     * Determine if an alert should be triggered for an offer component.
     *
     * @param mixed $offerComponent TechnicalOffer or FinancialOffer
     * @return bool
     */
    public function shouldAlert($offerComponent): bool
    {
        // Alert if deadline is <= 4 days OR internal control date is <= 2 days
        // We include past dates (late) as alerts too.

        $deadlineAlert = $offerComponent->deadline && $offerComponent->deadline <= now()->addDays(4);
        $controlAlert = $offerComponent->internal_control_date && $offerComponent->internal_control_date <= now()->addDays(2);

        return $deadlineAlert || $controlAlert;
    }
}
