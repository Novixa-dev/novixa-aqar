<x-filament-panels::page>
    {{-- The four headline stats (Total Properties / Active Listings / Total
    Bookings / Total Revenue) are rendered once, above this view, by the
    PropertyStatsOverview header widget (see getHeaderWidgets() in
    Dashboard.php) — it uses properly formatted currency and icons. This
    view previously also hardcoded the same four numbers a second time
    below, scoped to the current month instead of all-time, so the two
    same-labeled blocks could show different figures for "Total
    Properties" etc. side by side. Removed rather than kept in sync. --}}

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'آخر نشاط للموظفين' : 'Staff Recent Activity' }}</h2>
        <!-- Add staff-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>