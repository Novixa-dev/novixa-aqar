<div>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Method' }}</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
            @foreach ($payments as $payment)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ app(\App\Services\CurrencyService::class)->format($payment->amount) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($payment->status) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>