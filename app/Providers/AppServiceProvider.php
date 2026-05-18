<?php

namespace App\Providers;

use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Injection de dépendances — permet le mocking dans les tests
        $this->app->bind(ProjectService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override the plugin's MediaBrowser to fix missing clearCachedSchemas() in setCurrentFolder()
        Livewire::component('media-browser', \App\Livewire\MediaBrowser::class);

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
            'User',
            'Archive'
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
