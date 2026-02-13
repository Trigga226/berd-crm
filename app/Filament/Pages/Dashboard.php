<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use App\Utils\Pays;
use App\Utils\Domaines;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function mount()
    {
        // Sanitize filters
        if (is_array($this->filters)) {
            foreach ($this->filters as $key => $value) {
                if (is_string($value)) {
                    $this->filters[$key] = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                }
            }
        }
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'draft' => 'Brouillon',
                                'submitted' => 'Soumis',
                                'negotiation' => 'Négociation',
                                'won' => 'Gagné',
                                'lost' => 'Perdu',
                                'abandoned' => 'Abandonné',
                                'planned' => 'Planifié',
                                'active' => 'En Cours',
                                'completed' => 'Terminé',
                                'on_hold' => 'En Pause',
                                'cancelled' => 'Annulé',
                            ]),
                        Select::make('country')
                            ->label('Pays')
                            ->options(Pays::getOptions())
                            ->searchable(),
                        Select::make('domains')
                            ->label('Domaines')
                            ->options(Domaines::getOptions())
                            ->searchable(),
                        Select::make('period')
                            ->label('Période')
                            ->options([
                                '1_month' => '1 Mois',
                                '3_months' => '3 Mois',
                                '6_months' => '6 Mois',
                                '1_year' => '1 An',
                                '2_years' => '2 Ans',
                                'all' => 'Toutes les années',
                            ])
                            ->default('1_month'),
                        \Filament\Forms\Components\TextInput::make('score_min')
                            ->label('Note Min')
                            ->numeric()
                            ->maxValue(100),
                    ])
                    ->columns(5)->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('dashboard.print-stats', $this->filters))
                ->openUrlInNewTab(),
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}
