<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\News;

class NewsDetail extends Component
{
    public News $news;

    public function mount($slug)
    {
        $this->news = News::where('slug', $slug)
            ->published()
            ->with('author:id,name')
            ->firstOrFail();
    }

    public function getRelatedNewsProperty()
    {
        return News::published()
            ->where('id', '!=', $this->news->id)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.news-detail', [
            'relatedNews' => $this->relatedNews,
        ])->layout('layouts.app', [
            'title' => $this->news->title.' | '.app(\App\Support\Brand::class)->name(),
            'metaDescription' => \Illuminate\Support\Str::limit(strip_tags($this->news->excerpt ?: $this->news->content), 160),
            'ogType' => 'article',
        ]);
    }
}
