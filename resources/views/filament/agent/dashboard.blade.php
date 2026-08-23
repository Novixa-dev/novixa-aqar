<x-filament-panels::page>
    <div class="grid gap-6">
        <x-filament::card>
            <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'أحدث العملاء المحتملين' : 'Recent Leads' }}</h2>
            @if (empty($this->recentLeads))
                <p class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'لا يوجد عملاء محتملون بعد.' : 'No leads yet.' }}</p>
            @else
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'الاهتمام' : 'Interest' }}</th>
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->recentLeads as $lead)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 font-medium">{{ $lead['name'] }}</td>
                                <td class="py-2">{{ $lead['interest'] ?: '—' }}</td>
                                <td class="py-2">
                                    <span class="fi-badge fi-badge-size-md" style="background-color: rgb(59 130 246 / 0.1); color: rgb(59 130 246);">
                                        {{ \App\Support\StatusLabels::label('lead', $lead['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'أحدث الزيارات' : 'Recent Bookings' }}</h2>
            @if (empty($this->recentBookings))
                <p class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مجدولة بعد.' : 'No bookings scheduled yet.' }}</p>
            @else
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Customer' }}</th>
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'العقار' : 'Property' }}</th>
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                            <th class="py-2 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->recentBookings as $booking)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 font-medium">{{ $booking['name'] }}</td>
                                <td class="py-2">{{ $booking['property'] }}</td>
                                <td class="py-2">{{ $booking['date'] }} {{ $booking['time'] }}</td>
                                <td class="py-2">
                                    <span class="fi-badge fi-badge-size-md" style="background-color: rgb(16 185 129 / 0.1); color: rgb(5 150 105);">
                                        {{ \App\Support\StatusLabels::label('booking', $booking['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::card>
    </div>
</x-filament-panels::page>
