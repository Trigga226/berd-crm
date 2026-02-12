<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $models = [
            'AdministrativeDocument',
            'AvisManifestation',
            'Client',
            'Department',
            'Expert',
            'FinancialOffer',
            'Manifestation',
            'ManifestationDocument',
            'ManifestationExpert',
            'ManifestationPartner',
            'ManifestationUser',
            'Offer',
            'OfferDocument',
            'OfferPartner',
            'OfferUser',
            'Partner',
            'PartnerDocument',
            'PartnerReference',
            'Poste',
            'Project',
            'ProjectActivity',
            'ProjectAmendment',
            'ProjectDeliverable',
            'ProjectExpertContract',
            'ProjectInvoice',
            'ProjectReport',
            'ProjectRisk',
            'TechnicalOffer',
            'User'
        ];

        foreach ($models as $model) {
            $modelClass = "App\\Models\\{$model}";
            $observerClass = "App\\Observers\\{$model}Observer";

            if (class_exists($modelClass) && class_exists($observerClass)) {
                $modelClass::observe($observerClass);
            }
        }
    }
}
