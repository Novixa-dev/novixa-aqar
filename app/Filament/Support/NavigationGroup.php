<?php

namespace App\Filament\Support;

use Filament\Support\Contracts\HasLabel;

/**
 * Every sidebar group used across the panels, in one place.
 *
 * Groups used to be plain strings in four styles (raw English, __(),
 * a locale ternary, double-quoted), so most rendered in English on the
 * Arabic panels and one name could split into two groups. Filament reads
 * an enum's HasLabel for the group heading.
 */
enum NavigationGroup: string implements HasLabel
{
    case Administration = 'administration';
    case Settings = 'settings';
    case UserManagement = 'user_management';
    case PropertyManagement = 'property_management';
    case Content = 'content';
    case Support = 'support';
    case Account = 'account';

    public function getLabel(): string
    {
        return match ($this) {
            self::Administration => __('Administration'),
            self::Settings => __('Settings'),
            self::UserManagement => __('User Management'),
            self::PropertyManagement => __('Property Management'),
            self::Content => __('Content'),
            self::Support => __('Support'),
            self::Account => __('Account'),
        };
    }
}
