<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;

/**
 * Reusable CSV export action for Filament tables.
 *
 * Usage:
 *   ExportCsvAction::make('export')
 *       ->filename('my-file.csv')
 *       ->columns([
 *           'Title'  => fn($r) => $r->title,
 *           'Status' => fn($r) => $r->status,
 *       ])
 */
class ExportCsvAction extends Action
{
    protected string $csvFilename = 'export.csv';

    /** @var array<string, callable> */
    protected array $csvColumns = [];

    public static function make(?string $name = null): static
    {
        $instance = parent::make($name ?? 'export_csv');

        return $instance
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray');
    }

    public function filename(string $filename): static
    {
        $this->csvFilename = $filename;
        return $this;
    }

    public function columns(array $columns): static
    {
        $this->csvColumns = $columns;
        return $this;
    }

    public function setUp(): void
    {
        parent::setUp();

        $columns  = &$this->csvColumns;
        $filename = &$this->csvFilename;

        $this->action(function ($livewire) use (&$columns, &$filename) {
            $query = $livewire->getFilteredSortedTableQuery();

            return response()->streamDownload(function () use ($query, $columns) {
                $out = fopen('php://output', 'w');

                // UTF-8 BOM for Excel
                fputs($out, "\xEF\xBB\xBF");

                // Header row
                fputcsv($out, array_keys($columns), ';');

                // Data rows
                $query->chunk(500, function ($records) use ($out, $columns) {
                    foreach ($records as $record) {
                        $row = array_map(fn($resolver) => $resolver($record), $columns);
                        fputcsv($out, $row, ';');
                    }
                });

                fclose($out);
            }, $filename, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        });
    }
}
