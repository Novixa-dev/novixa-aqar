<?php

namespace App\Providers;

use App\Models\Property;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use App\Observers\PropertyObserver;
use App\Policies\RolePolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
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
    }
}
