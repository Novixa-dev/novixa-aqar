<div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Method' }}</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
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