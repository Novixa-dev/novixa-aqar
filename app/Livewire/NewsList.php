<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\News;
use Livewire\WithPagination;

class NewsList extends Component
{
    use WithPagination;

    public $search = '';
    public $featuredOnly = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'featuredOnly' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFeaturedOnly()
    {
        $this->resetPage();
    }

    public function getNewsProperty()
    {
        $query = News::published()
            ->with('author:id,name')
            ->orderBy('published_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->featuredOnly) {
            $query->featured();
        }

        // The view only renders the "Featured Articles" section when there's
        // no search/featuredOnly filter active — match that condition here,
        // otherwise the same 3 featured articles shown up top get repeated
        // again as the first results in this grid right below them.
        if (! $this->search && ! $this->featuredOnly) {
            $query->whereNotIn('id', $this->featuredNews->pluck('id'));
        }

        return $query->paginate(12);
    }

    public function getFeaturedNewsProperty()
    {
        return News::published()
            ->featured()
            ->with('author:id,name')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.news-list', [
            'news' => $this->news,
            'featuredNews' => $this->featuredNews,
        ])->layout('layouts.app', [
            'title' => app()->getLocale() === 'ar' ? 'الأخبار العقارية' : 'Real Estate News',
        ]);
    }
}
