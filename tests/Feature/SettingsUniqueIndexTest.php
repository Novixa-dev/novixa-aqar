<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingsUniqueIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_setting_cannot_be_stored_twice(): void
    {
        $row = ['group' => 'general', 'name' => 'site_email', 'locked' => false, 'payload' => json_encode('dup@novixa.dev'), 'created_at' => now(), 'updated_at' => now()];

        $this->expectException(QueryException::class);
        DB::table('settings')->insert($row);
    }

    public function test_spatie_upsert_updates_in_place(): void
    {
        $settings = app(\App\Settings\GeneralSettings::class);
        $settings->site_phone = '+967 777 000 000';
        $settings->save();

        $this->assertSame(1, DB::table('settings')->where('group', 'general')->where('name', 'site_phone')->count());
    }
}
