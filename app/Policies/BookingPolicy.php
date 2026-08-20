<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['staff', 'admin', 'super_admin']) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_booking');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id
            || ($booking->team_id !== null && $user->teams()->whereKey($booking->team_id)->exists());
    }

    public function create(User $user): bool
    {
        // Not gated in practice - the public booking flow (PropertyBooking
        // Livewire component, BookingController::store()) never calls
        // authorize('create', ...), this only guards a hypothetical future
        // Filament UI entry point.
        return $user->can('create_booking');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->can('delete_booking') && $this->view($user, $booking);
    }
}
