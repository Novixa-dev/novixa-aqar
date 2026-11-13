<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacyPropertySearchRedirectTest extends TestCase
{
    public function test_the_old_advanced_search_url_permanently_redirects_to_the_listing(): void
    {
        $this->get('/properties/search')
            ->assertStatus(301)
            ->assertRedirect('/properties');
    }
}
