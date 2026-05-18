<?php

namespace App\Notifications;

use App\Models\ProjectInvoice;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification
{
    public function __construct(protected ProjectInvoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $remaining = number_format($this->invoice->remainingAmount(), 0, ',', ' ');

        return FilamentNotification::make()
            ->title("Facture impayée : {$this->invoice->invoice_number}")
            ->body(
                "La facture du projet **{$this->invoice->project?->title}** est en retard de paiement. "
                . "Reste à percevoir : {$remaining} XOF."
            )
            ->color('warning')
            ->icon('heroicon-o-currency-dollar')
            ->getDatabaseMessage();
    }
}
