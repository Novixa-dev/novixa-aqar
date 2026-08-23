<?php

namespace App\Filament\Agent\Pages;

use App\Models\Property;
use App\Models\Booking;
use App\Models\Lead;
use Filament\Pages\Page;
use App\Filament\Agent\Widgets\AgentStatsOverview;

class Dashboard extends Page
{
    protected string $view = 'filament.agent.dashboard';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -2;

    public array $recentLeads = [];
    public array $recentBookings = [];

    public function mount(): void
    {
        $agentId = auth()->id();

        $this->recentLeads = Lead::query()
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'interest', 'status', 'created_at'])
            ->toArray();

        $this->recentBookings = Booking::query()
            ->whereHas('property', fn ($q) => $q->where('agent_id', $agentId))
            ->with('property:id,title')
            ->latest('date')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'property' => $b->property?->title,
                'date' => $b->date?->translatedFormat('M j, Y'),
                'time' => $b->time?->format('H:i'),
                'status' => $b->status,
            ])
            ->toArray();
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AgentStatsOverview::class,
        ];
    }
}
