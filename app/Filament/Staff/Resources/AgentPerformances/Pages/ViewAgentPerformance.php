<?php

namespace App\Filament\Staff\Resources\AgentPerformances\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Filament\Staff\Resources\AgentPerformances\AgentPerformanceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;

class ViewAgentPerformance extends ViewRecord
{
    protected static string $resource = AgentPerformanceResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(app()->getLocale() === 'ar' ? 'مؤشرات الأداء' : 'Performance metrics')
                    ->schema([
                        // None of these are real columns -- the table's
                        // equivalent columns work via Filament's ->counts()/
                        // ->avg() table-column aggregates, which don't apply
                        // on an infolist. Compute each directly instead of
                        // reading a magic _count/average_rating attribute
                        // that's never actually loaded on this record.
                        TextEntry::make('listings_count')
                            ->label(__('filament.fields.total_listings'))
                            ->state(fn ($record) => $record->properties()->count()),
                        TextEntry::make('properties_sold_count')
                            ->label(__('filament.fields.properties_sold'))
                            ->state(fn ($record) => $record->properties()->where('status', 'sold')->count()),
                        TextEntry::make('appointments_count')
                            ->label(__('filament.fields.total_appointments'))
                            ->state(fn ($record) => $record->agentAppointments()->count()),
                        TextEntry::make('average_rating')
                            ->label(__('filament.fields.average_rating'))
                            ->state(fn ($record) => $record->averageRating()),
                    ]),
                Section::make(app()->getLocale() === 'ar' ? 'أحدث التقييمات' : 'Recent reviews')
                    ->schema([
                        TextEntry::make('reviewsReceived.comment')
                            ->label(__('filament.fields.review'))
                            ->listWithLineBreaks()
                            ->limitList(5),
                    ]),
            ]);
    }
}