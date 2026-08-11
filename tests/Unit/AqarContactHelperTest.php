<?php

namespace Tests\Unit;

use App\Helpers\AqarContactHelper;
use Tests\TestCase;

class AqarContactHelperTest extends TestCase
{
    public function test_sanitizes_phone_number_for_whatsapp()
    {
        $phone1 = '+967 770 000 000';
        $phone2 = '0770000000';
        $phone3 = '770000000';

        $this->assertEquals('967770000000', AqarContactHelper::sanitizeForWhatsApp($phone1));
        $this->assertEquals('967770000000', AqarContactHelper::sanitizeForWhatsApp($phone2));
        $this->assertEquals('967770000000', AqarContactHelper::sanitizeForWhatsApp($phone3));
    }

    public function test_formats_phone_for_click_to_call()
    {
        $phone = '+967 (770) 000-000';
        $this->assertEquals('+967770000000', AqarContactHelper::formatForCall($phone));
    }

    public function test_generates_whatsapp_inquiry_url()
    {
        $url = AqarContactHelper::getWhatsAppUrl('+967 770 000 000', 'شقة للبيع بالمكلا', 105);

        $this->assertStringStartsWith('https://wa.me/967770000000?text=', $url);
        $this->assertStringContainsString(rawurlencode('شقة للبيع بالمكلا'), $url);
        $this->assertStringContainsString(rawurlencode('#105'), $url);
    }

    public function test_generates_share_urls()
    {
        $shares = AqarContactHelper::getShareUrls('https://aqar.ye/property/105', 'شقة فخمة بالمكلا');

        $this->assertArrayHasKey('whatsapp', $shares);
        $this->assertArrayHasKey('twitter', $shares);
        $this->assertArrayHasKey('facebook', $shares);
        $this->assertArrayHasKey('telegram', $shares);

        $this->assertStringContainsString('https://api.whatsapp.com/send?text=', $shares['whatsapp']);
        $this->assertStringContainsString('https://twitter.com/intent/tweet?', $shares['twitter']);
        $this->assertStringContainsString('https://www.facebook.com/sharer/sharer.php?u=', $shares['facebook']);
    }
}
