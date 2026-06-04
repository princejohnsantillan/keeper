<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Scopes\OrganizationScope;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Number;

final class ChildExporter extends Exporter
{
    protected static ?string $model = Child::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('first_name')
                ->label('First Name'),
            ExportColumn::make('middle_name')
                ->label('Middle Name'),
            ExportColumn::make('last_name')
                ->label('Last Name'),
            ExportColumn::make('full_name')
                ->label('Full Name'),
            ExportColumn::make('nickname')
                ->label('Nickname'),
            ExportColumn::make('birth_date')
                ->label('Birth Date')
                ->formatStateUsing(fn (mixed $state): ?string => self::formatDate($state)),
            ExportColumn::make('age')
                ->label('Age')
                ->state(fn (Child $record): int => $record->birth_date->age),
            ExportColumn::make('gender')
                ->label('Gender')
                ->state(fn (Child $record): string => $record->gender->getLabel()),
            ExportColumn::make('guardians')
                ->label('Guardians')
                ->state(fn (Child $record): string => self::formatGuardians($record->guardians)),
            ExportColumn::make('organization_tags')
                ->label('Organization Tags')
                ->state(fn (Child $record, array $options): string => self::formatOrganizationTags($record, $options)),
            ExportColumn::make('organization_note')
                ->label('Organization Note')
                ->state(fn (Child $record, array $options): string => self::formatOrganizationNote($record, $options)),
            ExportColumn::make('notes')
                ->label('Notes'),
            ExportColumn::make('created_at')
                ->label('Created At')
                ->formatStateUsing(fn (mixed $state): ?string => self::formatDateTime($state)),
            ExportColumn::make('updated_at')
                ->label('Updated At')
                ->formatStateUsing(fn (mixed $state): ?string => self::formatDateTime($state)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your child export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }

    private static function formatDate(mixed $state): ?string
    {
        if ($state instanceof CarbonInterface) {
            return $state->toDateString();
        }

        return filled($state) ? (string) $state : null;
    }

    private static function formatDateTime(mixed $state): ?string
    {
        if ($state instanceof CarbonInterface) {
            return $state->toDateTimeString();
        }

        return filled($state) ? (string) $state : null;
    }

    /**
     * @param  Collection<int, Guardian>  $guardians
     */
    private static function formatGuardians(Collection $guardians): string
    {
        return $guardians
            ->map(function (Guardian $guardian): string {
                $pivot = $guardian->getRelationValue('pivot');
                $relationship = $pivot instanceof Pivot ? $pivot->getAttribute('relationship') : null;
                $relationshipLabel = $relationship instanceof RelationshipEnum
                    ? $relationship->getLabel()
                    : str((string) $relationship)->headline()->toString();

                return filled($relationshipLabel)
                    ? "{$guardian->full_name} ({$relationshipLabel})"
                    : $guardian->full_name;
            })
            ->implode(', ');
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function formatOrganizationTags(Child $child, array $options): string
    {
        $organizationId = self::getOrganizationId($options);

        if ($organizationId === null) {
            return '';
        }

        return $child->organizationTags()
            ->withoutGlobalScope(OrganizationScope::class)
            ->where('organization_id', $organizationId)
            ->pluck('name')
            ->implode(', ');
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function formatOrganizationNote(Child $child, array $options): string
    {
        $organizationId = self::getOrganizationId($options);

        if ($organizationId === null) {
            return '';
        }

        return (string) $child->organizationNote()
            ->withoutGlobalScope(OrganizationScope::class)
            ->where('organization_id', $organizationId)
            ->value('note');
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function getOrganizationId(array $options): ?string
    {
        $organizationId = $options['organization_id'] ?? null;

        return filled($organizationId) ? (string) $organizationId : null;
    }
}
