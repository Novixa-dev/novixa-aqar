<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['staff', 'admin', 'super_admin']) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_appointment');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id
            || $appointment->agent_id === $user->id
            || $appointment->staff_id === $user->id
            || ($appointment->team_id !== null && $user->teams()->whereKey($appointment->team_id)->exists());
    }

    public function create(User $user): bool
    {
        return $user->can('create_appointment');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->can('update_appointment') && $this->view($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('delete_appointment') && $this->view($user, $appointment);
    }
}
