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

    public function test_project_form_offers_a_cover_and_gallery_per_language(): void
    {
        $this->seed(PortfolioContentSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get('/admin/portfolio-sections/'.PortfolioSection::query()->value('id').'/edit')
            ->assertOk()
            ->assertSee('نسخه‌ی فارسی')
            ->assertSee('نسخه‌ی انگلیسی')
            ->assertSee('image_path_en')
            ->assertSee('gallery_paths_en');
    }

    public function test_public_endpoints_respond(): void
    {
        $this->seed(PortfolioContentSeeder::class);

        $this->get('/')->assertOk();
        $this->getJson('/api/site')->assertOk()->assertJsonStructure([
            'data' => ['profile', 'about', 'skillGroups', 'experience', 'contact', 'links'],
        ]);
        $this->getJson('/api/portfolio')->assertOk()->assertJsonStructure([
            'data' => ['title', 'categories', 'projects'],
        ]);
    }
}
