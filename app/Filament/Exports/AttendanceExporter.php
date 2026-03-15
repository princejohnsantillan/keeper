<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Attendance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('child.full_name')
                ->label('Child'),
            ExportColumn::make('checkinGatepass.code')
                ->label('Check-in Gatepass'),
            ExportColumn::make('checkinGatepass.guardian.full_name')
                ->label('Check-in Guardian'),
            ExportColumn::make('checked_in_at')
                ->label('Checked In At'),
            ExportColumn::make('checkinKeeper.user.name')
                ->label('Check-in Keeper'),
            ExportColumn::make('checkoutGatepass.code')
                ->label('Check-out Gatepass'),
            ExportColumn::make('checkoutGatepass.guardian.full_name')
                ->label('Check-out Guardian'),
            ExportColumn::make('checked_out_at')
                ->label('Checked Out At'),
            ExportColumn::make('checkoutKeeper.user.name')
                ->label('Check-out Keeper'),
            ExportColumn::make('notes')
                ->label('Notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your attendance export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
