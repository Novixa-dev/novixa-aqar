<?php

namespace Tests\Feature;

use App\Filament\Support\NavigationGroup;
use Tests\TestCase;

class ArabicTranslationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('ar');
    }

    public function test_filament_gaps_are_filled_without_losing_filaments_own_arabic(): void
    {
        $this->assertSame('جارٍ التحميل...', __('filament-tables::table.loading'));
        $this->assertSame('تخطي إلى المحتوى', __('filament-panels::layout.skip_to_content.label'));
        $this->assertSame('نتيجة واحدة', trans_choice('filament-tables::table.result_count', 1));

        // A key Filament already translates must survive the override merge.
        $this->assertNotSame('filament-tables::table.empty.heading', __('filament-tables::table.empty.heading'));
        $this->assertMatchesRegularExpression('/\p{Arabic}/u', __('filament-tables::table.empty.heading'));
    }

    public function test_every_sidebar_group_has_an_arabic_label(): void
    {
        foreach (NavigationGroup::cases() as $group) {
            $this->assertMatchesRegularExpression('/\p{Arabic}/u', $group->getLabel(), "{$group->name} has no Arabic label");
        }
    }

    public function test_auto_generated_filament_labels_are_translated(): void
    {
        $this->assertSame('الإيجار الشهري', (string) \Filament\Tables\Columns\TextColumn::make('monthly_rent')->getLabel());
        $this->assertSame('المستخدم', (string) \Filament\Tables\Columns\TextColumn::make('user.name')->getLabel());
        $this->assertSame('استهلاك الكهرباء', (string) \Filament\Forms\Components\TextInput::make('electricity_usage')->getLabel());

        // An explicit label is left exactly as written.
        $this->assertSame('عنوان مخصص', (string) \Filament\Forms\Components\TextInput::make('x')->label('عنوان مخصص')->getLabel());
    }
}
