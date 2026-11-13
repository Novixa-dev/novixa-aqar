<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use App\Models\Vendor;
use App\Services\WorkflowNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MaintenanceController extends TenantCrudController
{
    private const MANAGER_ROLES = ['admin', 'super_admin', 'staff', 'agent'];

    // Office-workflow fields only the agency (or, for job progress, the
    // assigned vendor) may set — never the tenant who raised the request.
    private const TENANT_EDITABLE = ['title', 'description', 'priority', 'photos'];

    private const VENDOR_EDITABLE = ['status', 'completed_at', 'invoice_reference', 'quote_references', 'photos'];

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

        if ($this->isManager($request)) {
            return $query;
        }

        if ($tenantId = $this->linkedTenantId($request)) {
            return $query->where('tenant_id', $tenantId);
        }

        if ($vendorId = $this->linkedVendorId($request)) {
            return $query->where('vendor_id', $vendorId);
        }

        // Landlord/buyer/seller accounts (or a tenant/vendor with no linked
        // record yet) have no legitimate claim to any maintenance request
        // via this endpoint — deny, matching the Contractor Filament
        // resource's own "no linked record -> empty, not an error" pattern.
        return $query->whereRaw('1 = 0');
    }

    public function destroy(Request $request): JsonResponse
    {
        abort_unless($this->isManager($request), 403);

        return parent::destroy($request);
    }

    // Reads were already scoped per role, but writes weren't: a tenant could
    // file a request under another tenant's tenant_id, and any team member
    // with no claim at all could still create one.
    protected function prepareForCreate(Request $request, array $attributes): array
    {
        if ($this->isManager($request)) {
            return $attributes;
        }

        $tenantId = $this->linkedTenantId($request);
        abort_unless($tenantId, 403);

        $leasesProperty = Lease::where('tenant_id', $request->user()->id)
            ->where('property_id', $attributes['property_id'])
            ->exists();

        if (! $leasesProperty) {
            throw ValidationException::withMessages([
                'property_id' => [__('You can only report maintenance for a property you lease.')],
            ]);
        }

        return ['tenant_id' => $tenantId, 'property_id' => $attributes['property_id'], 'team_id' => $attributes['team_id'], 'requested_date' => $attributes['requested_date']]
            + array_intersect_key($attributes, array_flip(self::TENANT_EDITABLE));
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        if ($this->isManager($request)) {
            return $attributes;
        }

        $editable = $this->linkedTenantId($request) ? self::TENANT_EDITABLE : self::VENDOR_EDITABLE;

        return array_intersect_key($attributes, array_flip($editable));
    }

    private function isManager(Request $request): bool
    {
        $user = $request->user();

        if ($user->hasAnyRole(self::MANAGER_ROLES)) {
            return true;
        }

        $team = $user->currentTeam;

        return $team && ($user->ownsTeam($team) || $user->hasTeamRole($team, 'admin') || $user->hasTeamRole($team, 'manager'));
    }

    private function linkedTenantId(Request $request): ?int
    {
        return Tenant::where('user_id', $request->user()->id)
            ->where('team_id', $this->teamId($request))
            ->value('id');
    }

    private function linkedVendorId(Request $request): ?int
    {
        return Vendor::where('email', $request->user()->email)
            ->where('team_id', $this->teamId($request))
            ->value('id');
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
            'tenant_id' => [$record || ! $this->isManager($request) ? 'sometimes' : 'required', Rule::exists('tenants', 'id')->where('team_id', $teamId)],
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
