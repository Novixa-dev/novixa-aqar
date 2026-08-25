<?php

namespace App\Http\Controllers\API\V1;

use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Services\WorkflowNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends TenantCrudController
{
    protected string $model = MaintenanceRequest::class;

    protected string $routeParameter = 'maintenance';

    protected array $searchable = ['title', 'description'];

    protected array $filterable = ['property_id', 'tenant_id', 'vendor_id', 'status', 'priority'];

    public function __construct(private readonly WorkflowNotifier $notifications) {}

    /**
     * The base team-scoped query lets any member of the team read/write
     * every maintenance request belonging to it — correct for staff/agents
     * who manage the whole agency's requests, but not for a tenant or
     * vendor/contractor account, who should only reach their own records.
     * Same bug class as the previously-fixed Tenant-panel IDOR (P0-2),
     * in this API path instead of Filament.
     */
    protected function teamQuery(Request $request): Builder
    {
        $query = parent::teamQuery($request);
        $user = $request->user();

        if ($user->hasAnyRole(['admin', 'super_admin', 'staff', 'agent'])) {
            return $query;
        }

        $teamId = $this->teamId($request);

        $tenantId = Tenant::where('user_id', $user->id)->where('team_id', $teamId)->value('id');
        if ($tenantId) {
            return $query->where('tenant_id', $tenantId);
        }

        $vendorId = Vendor::where('email', $user->email)->where('team_id', $teamId)->value('id');
        if ($vendorId) {
            return $query->where('vendor_id', $vendorId);
        }

        // Landlord/buyer/seller accounts (or a tenant/vendor with no linked
        // record yet) have no legitimate claim to any maintenance request
        // via this endpoint — deny, matching the Contractor Filament
        // resource's own "no linked record -> empty, not an error" pattern.
        return $query->whereRaw('1 = 0');
    }

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$record ? 'sometimes' : 'required', 'string'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['sometimes', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'requested_date' => [$record ? 'sometimes' : 'required', 'date'],
            'tenant_id' => [$record ? 'sometimes' : 'required', Rule::exists('tenants', 'id')->where('team_id', $teamId)],
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'vendor_id' => ['nullable', Rule::exists('vendors', 'id')->where('team_id', $teamId)],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'max:2048'],
            'quote_references' => ['nullable', 'array'],
            'invoice_reference' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['nullable', Rule::in(['not_applicable', 'pending', 'paid', 'overdue'])],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    protected function afterCreate(Request $request, Model $record): void
    {
        $this->notifications->notify(
            $this->teamId($request),
            $request->user(),
            'maintenance.created',
            'New maintenance request',
            $record->title,
            ['maintenance_id' => $record->getKey(), 'property_id' => $record->property_id],
        );
    }
}
