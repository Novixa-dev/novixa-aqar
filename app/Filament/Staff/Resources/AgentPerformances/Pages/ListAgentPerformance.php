<?php

namespace App\Filament\Staff\Resources\AgentPerformances\Pages;

use Filament\Actions\Action;
use App\Filament\Staff\Resources\AgentPerformances\AgentPerformanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAgentPerformance extends ListRecords
{
    protected static string $resource = AgentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateReport')
                ->label(__('filament.fields.generate_report'))
                ->action(function (): StreamedResponse {
                    $agents = AgentPerformanceResource::getEloquentQuery()
                        ->withCount(['properties as listings_count'])
                        ->withCount(['properties as properties_sold_count' => fn ($query) => $query->where('status', 'sold')])
                        ->withCount('appointments')
                        ->withAvg('reviews', 'rating')
                        ->get();

                    return response()->streamDownload(function () use ($agents) {
                        $handle = fopen('php://output', 'w');
                        // UTF-8 BOM: without it Excel opens Arabic names as mojibake.
                        fwrite($handle, "\xEF\xBB\xBF");
                        fputcsv($handle, [
                            __('filament.fields.name'),
                            __('filament.fields.email'),
                            __('filament.fields.listings'),
                            __('filament.fields.properties_sold'),
                            __('filament.fields.appointments'),
                            __('filament.fields.avg_rating'),
                        ], escape: '');
                        foreach ($agents as $agent) {
                            fputcsv($handle, [
                                $agent->name,
                                $agent->email,
                                $agent->listings_count,
                                $agent->properties_sold_count,
                                $agent->appointments_count,
                                $agent->reviews_avg_rating ? round($agent->reviews_avg_rating, 2) : '—',
                            ], escape: '');
                        }
                        fclose($handle);
                    }, 'agent-performance-'.now()->format('Y-m-d').'.csv');
                })
                ->color('success'),
        ];
    }
}