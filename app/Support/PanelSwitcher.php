<?php

namespace App\Support;

use App\Models\User;
use Filament\Navigation\MenuItem;
use Filament\Panel;

/**
 * Cross-panel navigation: builds user-menu entries for every panel the
 * current user is allowed to access (per User::canAccessPanel), excluding
 * the panel the menu is rendered in.
 */
class PanelSwitcher
{
    public const PANELS = [
        'admin' => ['path' => 'admin', 'label' => 'Admin', 'label_ar' => 'الإدارة'],
        'staff' => ['path' => 'staff', 'label' => 'Staff', 'label_ar' => 'الموظفون'],
        'agent' => ['path' => 'agent', 'label' => 'Agent', 'label_ar' => 'الوكيل'],
        'app' => ['path' => 'app', 'label' => 'Account', 'label_ar' => 'الحساب'],
        'buyer' => ['path' => 'buyer', 'label' => 'Buyer', 'label_ar' => 'المشتري'],
        'seller' => ['path' => 'seller', 'label' => 'Seller', 'label_ar' => 'البائع'],
        'landlord' => ['path' => 'landlord', 'label' => 'Landlord', 'label_ar' => 'المالك'],
        'tenant' => ['path' => 'tenant', 'label' => 'Tenant', 'label_ar' => 'المستأجر'],
        'contractor' => ['path' => 'contractor', 'label' => 'Contractor', 'label_ar' => 'المقاول'],
    ];

    /**
     * Filament user-menu items for every panel other than $currentId that the
     * current user is allowed to access. Items are registered unconditionally
     * (panel providers boot before authentication); per-user visibility is
     * resolved lazily by each item's visible() closure.
     *
     * @return array<MenuItem>
     */
    public static function menuItems(string $currentId): array
    {
        $isArabic = app()->getLocale() === 'ar';
        $items = [];

        foreach (self::PANELS as $id => $meta) {
            if ($id === $currentId) {
                continue;
            }

            $items[] = MenuItem::make()
                ->label($isArabic ? 'الانتقال إلى: ' . $meta['label_ar'] : 'Switch to: ' . $meta['label'])
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->url(url('/' . $meta['path']))
                ->visible(fn () => auth()->user()?->canAccessPanelById($id) ?? false);
        }

        return $items;
    }

    /**
     * A real AR/EN toggle for the Filament user menu, reusing the same
     * `lang.switch` route the public site's navbar already uses (it sets
     * the session/cookie locale and redirects back to the current page).
     * Every panel previously had PanelSwitcher's cross-*panel* navigator
     * but no way to actually change language without leaving the panel.
     *
     * Label and URL are both deferred to closures (evaluated fresh on every
     * render via MenuItem::getLabel()/getUrl()) rather than computed once
     * here. This method runs from each PanelProvider's panel() registration,
     * which — under Octane (this app's actual server, see docker-compose.yml)
     * — executes once per worker, not once per request. Reading
     * app()->getLocale() eagerly froze the "next" target language at
     * whatever locale was active when the worker booted: the item would
     * always point at the same target locale for the rest of that worker's
     * life, so switching worked exactly once and then offered no way back.
     */
    public static function languageMenuItem(): MenuItem
    {
        return MenuItem::make()
            ->label(fn () => app()->getLocale() === 'ar' ? 'English' : 'العربية')
            ->icon('heroicon-o-language')
            ->url(fn () => route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar'));
    }

    /**
     * A way back to the public marketing site. Every panel's user menu could
     * switch *between* panels (menuItems() above) but had no link back to
     * the public homepage at all — once inside any dashboard, there was no
     * reliable way to return to the public-facing site short of manually
     * editing the URL.
     */
    public static function publicSiteMenuItem(): MenuItem
    {
        return MenuItem::make()
            ->label(fn () => app()->getLocale() === 'ar' ? 'زيارة الموقع' : 'Visit Website')
            ->icon('heroicon-o-globe-alt')
            ->url(url('/'));
    }
}
