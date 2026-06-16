<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Offer;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    public function mount(): void
    {
        parent::mount();

        $offerId = request()->query('offer_id');

        if ($offerId) {
            $offer = Offer::find($offerId);

            if ($offer) {
                $this->form->fill([
                    'offer_id'  => $offer->id,
                    'title'     => $offer->title,
                    'client_id' => $offer->client_id,
                    'country'   => $offer->country,
                ]);
            }
        }
    }
}
