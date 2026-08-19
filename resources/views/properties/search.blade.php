@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">{{ app()->getLocale() === 'ar' ? 'بحث متقدم عن العقارات' : 'Property Search' }}</h1>
    
    <livewire:advanced-property-search />
</div>
@endsection