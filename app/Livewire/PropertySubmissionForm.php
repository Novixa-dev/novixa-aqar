<?php

namespace App\Livewire;

use App\Models\Property;
use App\Services\AIDescriptionService;
use Livewire\Component;
use Livewire\WithFileUploads;

class PropertySubmissionForm extends Component
{
    use WithFileUploads;

    public $title;
    public $description;
    public $location;
    public $price;
    public $bedrooms;
    public $bathrooms;
    public $area_sqft;
    public $year_built;
    public $property_type;
    public $neighborhood_id;
    public $owner_phone;
    public $listing_type = 'sale';
    public $images = [];
    public $video;
    public $customDescription;
    public $aiDescription;
    public $descriptionTone = 'professional';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'bedrooms' => 'required|integer|min:0',
        'bathrooms' => 'required|integer|min:0',
        'area_sqft' => 'required|numeric|min:0',
        'year_built' => 'required|integer|min:1800|max:2099',
        'property_type' => 'required|string|max:255',
        'neighborhood_id' => 'nullable|exists:neighborhoods,id',
        'owner_phone' => 'nullable|string|max:30',
        'images.*' => 'image|max:5120', // 5MB Max
        'video' => 'nullable|mimetypes:video/mp4,video/quicktime|max:102400', // 100MB Max
        'customDescription' => 'nullable|string|max:1000',
        'descriptionTone' => 'required|in:professional,casual,luxury',
    ];

    public function generateAIDescription()
    {
        $this->validate([
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area_sqft' => 'required|numeric|min:0',
            'property_type' => 'required|string|max:255',
            'descriptionTone' => 'required|in:professional,casual,luxury',
        ]);

        $aiService = new AIDescriptionService();
        $this->aiDescription = $aiService->generateDescription([
            'location' => $this->location,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'property_type' => $this->property_type,
        ], $this->descriptionTone);

        $this->description = $this->aiDescription;
    }

    public function updateDescription($newDescription)
    {
        $this->description = $newDescription;
    }

    public function submit()
    {
        $this->validate();

        $teamId = auth()->user()?->currentTeam?->id ?? \App\Models\Team::first()?->id;
    
        $property = Property::create([
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'year_built' => $this->year_built ?? date('Y'),
            'property_type' => $this->property_type,
            'neighborhood_id' => $this->neighborhood_id ?: null,
            'internal_notes' => $this->owner_phone ? 'رقم هاتف المالك: ' . $this->owner_phone : null,
            'status' => 'pending',
            'team_id' => $teamId,
            'user_id' => auth()->id(),
            'custom_description' => $this->customDescription,
        ]);
    
        foreach ($this->images as $image) {
            $property->addMedia($image)
                ->toMediaCollection('images');
        }
    
        if ($this->video) {
            $property->addMedia($this->video)
                ->toMediaCollection('videos');
        }
    
        session()->flash('message', 'تم إرسال طلب إضافة العقار بنجاح وهو قيد المراجعة والموافقة من قبل إدارة المكتب.');
        $this->dispatch('propertySubmitted');
        $this->reset(['title', 'description', 'location', 'price', 'bedrooms', 'bathrooms', 'area_sqft', 'year_built', 'property_type', 'neighborhood_id', 'owner_phone', 'images', 'video', 'customDescription']);
    }

    public function preview()
    {
        $this->validate();
        $this->dispatch('previewProperty', [
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'year_built' => $this->year_built,
            'property_type' => $this->property_type,
            'custom_description' => $this->customDescription,
        ]);
    }
    
    public function render()
    {
        $neighborhoods = \App\Models\Neighborhood::all();
        return view('livewire.property-submission-form', [
            'neighborhoods' => $neighborhoods,
        ]);
    }
}