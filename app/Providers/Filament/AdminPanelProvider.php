<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages;
// use App\Http\Middleware\TeamsPermission;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use JoelButcher\Socialstream\Filament\SocialstreamPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Support\PanelSwitcher;
use Filament\Navigation\MenuItem;
use Filament\Pages as FilamentPage;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Novixa Aqar | لوحة التحكم الإدارية')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->login([AuthenticatedSessionController::class, 'create'])
            ->loginRouteSlug('login')
            ->homeUrl('/admin')
            ->passwordReset()
            // ->emailVerification()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Edit Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => $this->shouldRegisterMenuItem()
                        ? url(Pages\EditProfile::getUrl())
                        : url($panel->getPath())),
                ...PanelSwitcher::menuItems('admin'),
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverResources(in: app_path('Filament/Staff/Resources'), for: 'App\\Filament\\Staff\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->discoverWidgets(in: app_path('Filament/Staff/Widgets'), for: 'App\\Filament\\Staff\\Widgets')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->pages([
                Pages\EditProfile::class,
            ])->widgets([
                \App\Filament\Staff\Widgets\StatsOverview::class,
                \App\Filament\Staff\Widgets\PropertyStatsOverview::class,
                \App\Filament\App\Widgets\LatestProperties::class,
                \App\Filament\App\Widgets\RecentBookings::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \App\Http\Middleware\SetLocaleMiddleware::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Administration'),
                new SocialstreamPlugin(),
            ]);

        // if (Features::hasApiFeatures()) {
        //     $panel->userMenuItems([
        //         MenuItem::make()
        //             ->label('API Tokens')
        //             ->icon('heroicon-o-key')
        //             ->url(fn () => $this->shouldRegisterMenuItem()
        //                 ? url(Pages\ApiTokenManagerPage::getUrl())
        //                 : url($panel->getPath())),
        //     ]);
        // }

/**
        if (Features::hasTeamFeatures()) {
            $panel
                ->tenant(Team::class, ownershipRelationship: 'team')
                ->tenantRoutePrefix('/{tenant}')
                ->tenantMiddleware([
                    \App\Http\Middleware\AssignDefaultTeam::class,
                ])
                ->tenantRegistration(Pages\CreateTeam::class)
                ->tenantProfile(Pages\EditTeam::class)
                ->userMenuItems([
                    MenuItem::make()
                        ->label('Team Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->url(Pages\EditTeam::getUrl()),
                ]);
        }

**/
        return $panel;
    }

    public function boot()
    {
        /**
         * Disable Fortify routes.
         */
        Fortify::$registersRoutes = false;
    
        /**
         * Disable Jetstream routes.
         */
        Jetstream::$registersRoutes = false;
    
        /**
         * Remove CreatePersonalTeam listener
         */
        // Event::listen(
        //     Registered::class,
        //     CreatePersonalTeam::class,
        // );
    
        /**
         * Add listener to assign user to office team
         */
   //     Event::listen(Registered::class, function ($event) {
  //          $teamManagementService = app(TeamManagementService::class);
  //          $teamManagementService->assignUserToOfficeTeam($event->user);
//        });
    }

    public function shouldRegisterMenuItem(): bool
    {
        return true; //auth()->user()?->hasVerifiedEmail() && Filament::hasTenancy() && Filament::getTenant();
    }
}
