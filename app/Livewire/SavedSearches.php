<?php

namespace App\Livewire;

use App\Models\SavedSearch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/** "My saved searches": list, toggle alerts, delete (P33-2). */
class SavedSearches extends Component
{
    public function toggleAlerts(int $id): void
    {
        $search = $this->owned($id);
        $search->update(['alerts_enabled' => ! $search->alerts_enabled]);
    }

    public function delete(int $id): void
    {
        $this->owned($id)->delete();
    }

    /** Always resolved through the signed-in user, so one user can never touch another's. */
    private function owned(int $id): SavedSearch
    {
        return Auth::user()->savedSearches()->findOrFail($id);
    }

    public function render()
    {
        $searches = Auth::user()->savedSearches()->latest()->get()
            ->each(fn (SavedSearch $search) => $search->setAttribute('match_count', $search->matchingProperties()->count()));

        return view('livewire.saved-searches', ['searches' => $searches])
            ->layout('layouts.app', [
                'title' => app()->getLocale() === 'ar' ? 'عمليات البحث المحفوظة' : 'Saved searches',
            ]);
    }
}
