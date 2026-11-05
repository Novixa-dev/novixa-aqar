
                <h3 class="text-lg font-semibold">{{ $property->title }}</h3>
                <p class="text-gray-600">{{ $property->location }}</p>
                <p class="text-[#8C7869] font-bold">${{ number_format($property->price, 2) }}</p>
                <a href="{{ route('property.detail', $property) }}" class="mt-2 inline-block bg-[#8C7869] hover:bg-[#7A6759] text-white px-4 py-2 rounded">View Details</a>
            </div>
        @endforeach
    </div>
</div>