<?php

namespace App\Providers;

use App\Support\Brand;
use Illuminate\Support\Facades\View;
use App\Models\Property;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use App\Observers\PropertyObserver;
use App\Policies\RolePolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Brand::class);
        // Register the module manager as a singleton
        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager();
        });

        // Register the module service provider
        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Public brand (config/branding.php) for every view: {{ $brand->name() }}.
        // Stateless - it reads the locale per call - so sharing one instance is
        // safe under Octane.
        View::share('brand', $this->app->make(Brand::class));

        // Filament derives a label from the column/field name when none is
        // given ("Monthly rent", "Is signed"), always in English. Passing
        // every label through the translator lets lang/ar.json supply the
        // Arabic for all of them at once; explicit labels are unaffected
        // (an Arabic or already-translated string has no matching key).
        \Filament\Forms\Components\Field::configureUsing(fn ($component) => $component->translateLabel());
        \Filament\Tables\Columns\Column::configureUsing(fn ($column) => $column->translateLabel());
        \Filament\Infolists\Components\Entry::configureUsing(fn ($entry) => $entry->translateLabel());

        // Register Property observer for automatic history tracking
        Property::observe(PropertyObserver::class);

        // Build Filament Shield permission keys as lower_snake_case joined by a
        // single underscore (e.g. "view_any_maintenance_request"), matching the
        // naming convention RolesSeeder::$rolePermissions already assumes. Shield's
        // config-driven separator/case combo can't produce this directly (it
        // refuses "_" as a separator alongside snake-family cases, since it can't
        // tell the affix and subject apart) - this closure builds the string
        // manually instead, using $affix/$subject rather than the configured case.
        FilamentShield::buildPermissionKeyUsing(
            fn (string $entity, string $affix, string $subject): string => Str::snake($affix) . '_' . Str::snake($subject)
        );

        // Spatie's Role model lives outside App\Models, so Laravel's policy
        // auto-discovery convention never finds RolePolicy on its own.
        Gate::policy(Role::class, RolePolicy::class);

        // Filament auto-generates form/table/infolist labels from the field
        // name (e.g. property_type -> "Property Type") but never runs that
        // humanized string through __() unless ->translateLabel() is called
        // on every single field. Field/Column/Entry all extend the same
        // Filament\Support\Components\Component base, and configureUsing()
        // walks the full class hierarchy (see ComponentManager::configure()),
        // so this one call applies to every concrete subclass (TextInput,
        // Select, TextColumn, IconColumn, TextEntry, ...) across all 9
        // panels without editing any of the ~45 resource files. Labels with
        // no matching lang/{ar,en}.json key just render unchanged (Laravel's
        // __() fallback), so this is purely additive - it can only add
        // translations, never break an existing label.
        Field::configureUsing(fn (Field $field) => $field->translateLabel());
        Column::configureUsing(fn (Column $column) => $column->translateLabel());
        Entry::configureUsing(fn (Entry $entry) => $entry->translateLabel());
    }
}
