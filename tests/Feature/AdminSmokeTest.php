<?php

namespace Tests\Feature;

use App\Models\AboutSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\User;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_render(): void
    {
        $this->seed(PortfolioContentSeeder::class);
        $user = User::factory()->create();

        $urls = [
            '/admin',
            '/admin/site-settings',
            '/admin/resume-file-manager',
            '/admin/resume-items',
            '/admin/hero-sections/'.HeroSection::query()->value('id').'/edit',
            '/admin/about-sections/'.AboutSection::query()->value('id').'/edit',
            '/admin/portfolio-sections/'.PortfolioSection::query()->value('id').'/edit',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertOk();
        }
    }

    public function test_public_endpoints_respond(): void
    {
        $this->seed(PortfolioContentSeeder::class);

        $this->get('/')->assertOk();
        $this->getJson('/api/site')->assertOk()->assertJsonStructure([
            'data' => ['profile', 'skillGroups', 'links', 'appearance'],
        ]);
        $this->getJson('/api/portfolio')->assertOk()->assertJsonStructure([
            'data' => ['title', 'categories', 'projects'],
        ]);
    }
}
