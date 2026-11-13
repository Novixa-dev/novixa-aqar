<?php

namespace Tests\Feature;

use App\Livewire\PropertyList;
use App\Livewire\SavedSearches;
use App\Models\Property;
use App\Models\SavedSearch;
use App\Models\User;
use App\Notifications\SavedSearchMatches;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class SavedSearchTest extends TestCase
{
    use RefreshDatabase;

    private function rental(array $overrides = []): Property
    {
        return Property::factory()->create($overrides + [
            'status' => 'for_rent',
            'property_type' => 'villa',
            'bedrooms' => 4,
            'moderation_status' => 'approved',
        ]);
    }

    public function test_a_guest_who_saves_a_search_is_sent_to_login(): void
    {
        Livewire::test(PropertyList::class)
            ->set('status', 'for_rent')
            ->call('saveSearch')
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('saved_searches', 0);
    }

    public function test_a_user_can_save_the_current_filters_once(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(PropertyList::class)
            ->set('status', 'for_rent')
            ->set('propertyType', 'villa')
            ->set('minBedrooms', 3);

        $component->call('saveSearch');
        $component->call('saveSearch');

        $this->assertDatabaseCount('saved_searches', 1);
        $saved = $user->savedSearches()->first();
        $this->assertSame(['status' => 'for_rent', 'property_type' => 'villa', 'min_bedrooms' => 3], $saved->criteria);
        $this->assertTrue($saved->alerts_enabled);
        $this->assertNotNull($saved->last_notified_at);
        $this->assertStringContainsString('status=for_rent', $saved->url());
    }

    public function test_a_search_with_no_filters_is_not_saved(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(PropertyList::class)->call('saveSearch');

        $this->assertDatabaseCount('saved_searches', 0);
    }

    public function test_the_saved_searches_page_only_shows_and_edits_the_users_own_searches(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mine = $user->savedSearches()->create(['criteria' => ['search' => 'المكلا']]);
        $theirs = $other->savedSearches()->create(['criteria' => ['search' => 'عدن']]);

        $this->actingAs($user)->get('/saved-searches')
            ->assertOk()
            ->assertSee('المكلا')
            ->assertDontSee('عدن');

        Livewire::test(SavedSearches::class)->call('toggleAlerts', $mine->id);
        $this->assertFalse($mine->refresh()->alerts_enabled);

        try {
            Livewire::test(SavedSearches::class)->call('delete', $theirs->id);
        } catch (\Throwable) {
            // ModelNotFoundException -> 404: another user's search is out of reach.
        }
        $this->assertModelExists($theirs);

        Livewire::test(SavedSearches::class)->call('delete', $mine->id);
        $this->assertModelMissing($mine);
    }

    public function test_alerts_include_only_new_public_matching_listings_and_are_not_repeated(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $search = $user->savedSearches()->create([
            'criteria' => ['status' => 'for_rent', 'property_type' => 'villa', 'min_bedrooms' => 3],
            'alerts_enabled' => true,
            'last_notified_at' => now()->subDays(2),
        ]);

        $new = $this->rental();
        $this->rental(['created_at' => now()->subDays(3)]);          // before the last alert
        $this->rental(['status' => 'for_sale']);                     // different listing type
        $this->rental(['bedrooms' => 2]);                            // too few bedrooms
        $this->rental(['moderation_status' => 'pending']);           // not public yet

        $this->artisan('saved-searches:send-alerts')->assertSuccessful();

        Notification::assertSentToTimes($user, SavedSearchMatches::class, 1);
        Notification::assertSentTo($user, SavedSearchMatches::class, fn (SavedSearchMatches $n) => $n->properties->pluck('id')->all() === [$new->id]);
        $this->assertTrue($search->refresh()->last_notified_at->isAfter(now()->subMinute()));

        $this->artisan('saved-searches:send-alerts')->assertSuccessful();
        Notification::assertSentToTimes($user, SavedSearchMatches::class, 1);
    }

    public function test_disabled_alerts_send_nothing(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $user->savedSearches()->create([
            'criteria' => ['status' => 'for_rent'],
            'alerts_enabled' => false,
            'last_notified_at' => now()->subDay(),
        ]);
        $this->rental();

        $this->artisan('saved-searches:send-alerts')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_recommendations_read_saved_search_criteria_without_crashing(): void
    {
        $user = User::factory()->create();
        $user->savedSearches()->create(['criteria' => ['search' => 'villa']]);

        $method = new \ReflectionMethod(\App\Services\PropertyRecommendationService::class, 'getSearchHistory');
        $history = $method->invoke(app(\App\Services\PropertyRecommendationService::class), $user);

        $this->assertSame([['search' => 'villa']], $history->all());
    }
}
