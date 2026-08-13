@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
<div class="container mx-auto px-4 pt-24 pb-8">
    <h1 class="text-4xl font-bold mb-8 text-center">{{ __('Welcome to') }} {{ app(\App\Settings\GeneralSettings::class)->site_name }}</h1>

    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-6 text-center">{{ __('Find Your Dream Property') }}</h2>
        <form action="{{ route('property.list') }}" method="GET" class="max-w-2xl mx-auto">
            <div class="flex flex-wrap -mx-2 mb-4">
                <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                    <input type="text" name="search" placeholder="{{ __('Search by location or property name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full md:w-1/4 px-2 mb-4 md:mb-0">
                    <select name="property_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="apartment">{{ __('Apartment') }}</option>
                        <option value="house">{{ __('House') }}</option>
                        <option value="condo">{{ __('Condo') }}</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4 px-2">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">{{ __('Search') }}</button>
                </div>
            </div>
        </form>
    </div>

    <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6 text-center">{{ __('Property Map') }}</h2>
        <x-property-map :properties="$mapProperties" />
    </section>

    <section class="mb-12">
        <h2 class="text-2xl font-semibold mb-6 text-center">{{ __('Featured Properties') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredProperties as $property)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition duration-300 hover:shadow-xl">
                    <img src="{{ $property->getFirstMediaUrl('images') ?: asset('images/property-placeholder.png') }}" alt="{{ $property->title }}" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">{{ $property->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($property->description, 100) }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-bed mr-1"></i> {{ $property->bedrooms }} {{ __('bed') }}</span>
                            <span><i class="fas fa-bath mr-1"></i> {{ $property->bathrooms }} {{ __('bath') }}</span>
                            <span><i class="fas fa-ruler-combined mr-1"></i> {{ $property->area_sqft }} {{ __('sqft') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-green-600">&pound{{ number_format($property->price, 2) }}</span>
                            <a href="{{ route('property.detail', $property->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <p class="text-center text-gray-600">{{ __('No featured properties available at the moment.') }}</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection

@push('scripts')
    @livewireScripts
@endpush
