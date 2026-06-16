<?php

namespace App\Filament\Resources\Experts\Tables;

use App\Filament\Actions\ExportCsvAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpertsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                \Filament\Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('Expérience')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('cv_path')
                    ->label('CV')
                    ->formatStateUsing(fn() => 'Télécharger')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->cv_path))
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->filters([
                \Filament\Tables\Filters\TrashedFilter::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('open_cv')
                    ->label('Ouvrir CV')
                    ->icon('heroicon-m-document')
                    ->url(fn($record) => asset('storage/' . $record->cv_path))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
                ForceDeleteAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                RestoreAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
            ])
            ->toolbarActions([
                ExportCsvAction::make()
                    ->filename('experts.csv')
                    ->columns([
                        'Nom'           => fn($r) => $r->last_name ?? '',
                        'Prénom'        => fn($r) => $r->first_name ?? '',
                        'Email'         => fn($r) => $r->email ?? '',
                        'Téléphone'     => fn($r) => $r->phone ?? '',
                        'Nationalité'   => fn($r) => $r->nationality ?? '',
                        'Expérience'    => fn($r) => $r->years_of_experience ?? '',
                        'Note'          => fn($r) => $r->rating ?? '',
                        'Domaines'      => fn($r) => implode(', ', $r->skills ?? []),
                        'Manifestations gagnées' => fn($r) => $r->won_manifestations_count ?? 0,
                    ]),
                BulkActionGroup::make([
                    BulkAction::make('analyze')
                        ->label('Analyser avec IA')
                        ->icon('heroicon-o-sparkles')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->implode(',');
                            return redirect(\App\Filament\Resources\Experts\Pages\ExpertAnalysis::getUrl(['ids' => $ids]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                    RestoreBulkAction::make()->visible(fn() => Auth::user()?->email === 'franck.b@berd-ing.com'),
                ]),
            ]);
    }
}
