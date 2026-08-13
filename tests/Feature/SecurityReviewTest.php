<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Lead;
use App\Models\Property;
use App\Models\RentalApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_routes_require_authentication()
    {
        $response = $this->get('/wishlist');
        $response->assertStatus(302);

        $response = $this->get('/custom-reports');
        $response->assertStatus(302);

        $response = $this->get('/booking-calendar');
        $response->assertStatus(302);
    }

    public function test_authenticated_users_can_access_protected_routes()
    {
        $this->seed(\Database\Seeders\DefaultTeamSeeder::class);
        $this->seed(\Database\Seeders\RolesSeeder::class);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/wishlist');
        $response->assertStatus(200);
    }

    public function test_sensitive_models_enforce_fillable_protection()
    {
        $property = new Property();
        $this->assertNotEmpty($property->getFillable());

        $lead = new Lead();
        $this->assertNotEmpty($lead->getFillable());

        $buyer = new Buyer();
        $this->assertNotEmpty($buyer->getFillable());

        $rental = new RentalApplication();
        $this->assertNotEmpty($rental->getFillable());
    }

    public function test_blade_templates_do_not_contain_unsafe_unescaped_user_input()
    {
        $viewsPath = resource_path('views');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsPath));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                // Ensure no unescaped user input like {!! $request->... !!} or {!! $input !!} exists
                $this->assertDoesNotMatchRegularExpression('/\{\!\!\s*\$(?:input|request|user_input|comment|message)\b/i', $content, "Unsafe unescaped output found in file: " . $file->getFilename());
            }
        }
    }
}
