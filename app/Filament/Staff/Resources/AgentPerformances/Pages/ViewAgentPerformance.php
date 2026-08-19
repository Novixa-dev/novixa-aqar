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
        return $infolist
            ->schema([
                Section::make('Performance Metrics')
                    ->schema([
                        TextEntry::make('listings_count')
                            ->label(__('filament.fields.total_listings')),
                        TextEntry::make('properties_sold_count')
                            ->label(__('filament.fields.properties_sold')),
                        TextEntry::make('appointments_count')
                            ->label(__('filament.fields.total_appointments')),
                        TextEntry::make('average_rating')
                            ->label(__('filament.fields.average_rating')),
                    ]),
                Section::make('Recent Reviews')
                    ->schema([
                        TextEntry::make('reviews.comment')
                            ->label(__('filament.fields.review'))
                            ->listWithLineBreaks()
                            ->limitList(5),
                    ]),
            ]);
    }
}