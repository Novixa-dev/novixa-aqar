@php
    $isArabic = app()->getLocale() === 'ar';
    $dir = $isArabic ? 'rtl' : 'ltr';
    $labels = $isArabic ? [
        'title' => 'رسالة جديدة من نموذج التواصل',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'message' => 'الرسالة',
        'sentAt' => 'وقت الإرسال',
    ] : [
        'title' => 'New contact form message',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'message' => 'Message',
        'sentAt' => 'Sent at',
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0;padding:24px;background:#f4f5f7;font-family:Tahoma,Arial,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
        <div style="background:#111827;color:#ffffff;padding:16px 24px;font-size:16px;font-weight:bold;">
            {{ $labels['title'] }}
        </div>
        <div style="padding:24px;">
            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-size:14px;line-height:1.6;">
                <tr>
                    <td style="padding:6px 0;font-weight:bold;width:120px;">{{ $labels['name'] }}</td>
                    <td style="padding:6px 0;">{{ $contactMessage->name }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-weight:bold;">{{ $labels['email'] }}</td>
                    <td style="padding:6px 0;">{{ $contactMessage->email }}</td>
                </tr>
                @if (filled($contactMessage->phone))
                    <tr>
                        <td style="padding:6px 0;font-weight:bold;">{{ $labels['phone'] }}</td>
                        <td style="padding:6px 0;">{{ $contactMessage->phone }}</td>
                    </tr>
                @endif
                <tr>
                    <td style="padding:6px 0;font-weight:bold;vertical-align:top;">{{ $labels['message'] }}</td>
                    <td style="padding:6px 0;white-space:pre-wrap;">{{ $contactMessage->message }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-weight:bold;">{{ $labels['sentAt'] }}</td>
                    <td style="padding:6px 0;">{{ $contactMessage->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
