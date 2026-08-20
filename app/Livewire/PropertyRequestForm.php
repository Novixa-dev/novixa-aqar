<?php

namespace App\Livewire;

use App\Models\Buyer;
use App\Models\Neighborhood;
use App\Models\Team;
use App\Services\PropertyMatchingService;
use Livewire\Component;

class PropertyRequestForm extends Component
{
    public $name;
    public $phone;
    public $email;
    public $property_type;
    public $neighborhood_id;
    public $min_price;
    public $max_price;
    public $bedrooms;
    public $bathrooms;
    public $notes;

    protected $rules = [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:30',
        'email' => 'nullable|email|max:255',
        'property_type' => 'required|string|max:255',
        'neighborhood_id' => 'nullable|exists:neighborhoods,id',
        'min_price' => 'nullable|numeric|min:0',
        'max_price' => 'nullable|numeric|min:0',
        'bedrooms' => 'nullable|integer|min:0',
        'bathrooms' => 'nullable|integer|min:0',
        'notes' => 'nullable|string|max:1000',
    ];

    public function submit()
    {
        $this->validate();

        $teamId = auth()->user()?->currentTeam?->id ?? Team::first()?->id;

        $searchCriteria = [
            'property_type' => $this->property_type,
            'neighborhood_id' => $this->neighborhood_id ?: null,
            'min_price' => $this->min_price ?: null,
            'max_price' => $this->max_price ?: null,
            'bedrooms' => $this->bedrooms ?: null,
            'bathrooms' => $this->bathrooms ?: null,
            'notes' => $this->notes ?: null,
        ];

        $buyer = Buyer::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'status' => 'active',
            'user_id' => auth()->id(),
            'team_id' => $teamId,
            'search_criteria' => $searchCriteria,
        ]);

        try {
            $matchingService = app(PropertyMatchingService::class);
            $matchingService->generateMatchesForBuyer($buyer);
        } catch (\Throwable $e) {
            // Log matching error gracefully
        }

        session()->flash('message', __('تم تسجيل طلب البحث عن عقارك بنجاح. سيقوم مستشارنا بمطابقة طلبك والتواصل معك قريباً.'));
        $this->dispatch('propertyRequestSubmitted');
        $this->reset();
    }

    public function render()
    {
        $neighborhoods = Neighborhood::all();
        return view('livewire.property-request-form', [
            'neighborhoods' => $neighborhoods,
        ]);
    }
}
