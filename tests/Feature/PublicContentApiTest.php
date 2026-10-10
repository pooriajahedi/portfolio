<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\PortfolioContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PortfolioContentSeeder::class);
    }

    public function test_site_payload_exposes_about_experience_and_contact_in_both_languages(): void
    {
        $data = $this->getJson('/api/site')->assertOk()->json('data');

        $this->assertArrayNotHasKey('appearance', $data);

        $this->assertNotSame('', $data['about']['headline']['fa']);
        $this->assertStringStartsWith('A decade', $data['about']['headline']['en']);
        $this->assertCount(2, $data['about']['paragraphs']);
        $this->assertCount(4, $data['about']['services']);
        $this->assertSame('Optimising running systems', $data['about']['services'][0]['title']['en']);

        $this->assertStringContainsString('holds up', $data['contact']['headline']['en']);
    }

    public function test_experience_periods_carry_jalali_and_gregorian_years(): void
    {
        $experience = $this->getJson('/api/site')->json('data.experience');

        $this->assertCount(4, $experience);

        $ongoing = $experience[0];
        $this->assertSame(['fa' => 1394, 'en' => 2015], $ongoing['period']['start']);
        $this->assertNull($ongoing['period']['end']);
        $this->assertTrue($ongoing['period']['current']);

        $finished = $experience[1];
        $this->assertSame(['fa' => 1398, 'en' => 2019], $finished['period']['start']);
        $this->assertSame(['fa' => 1400, 'en' => 2022], $finished['period']['end']);
        $this->assertFalse($finished['period']['current']);
    }

    public function test_projects_expose_brand_color_and_cover_type(): void
    {
        $projects = $this->getJson('/api/portfolio')->assertOk()->json('data.projects');

        $this->assertSame('#1d9bf0', $projects[0]['brandColor']);
        $this->assertSame('web', $projects[0]['coverType']);
        $this->assertSame('app', $projects[1]['coverType']);
        $this->assertSame('both', $projects[2]['coverType']);
    }

    public function test_invalid_brand_color_and_cover_type_fall_back_safely(): void
    {
        Project::query()->create([
            'title' => 'پروژه آزمایشی',
            'description' => 'توضیح',
            'brand_color' => 'tomato; background:red',
            'cover_type' => 'hologram',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $projects = $this->getJson('/api/portfolio')->json('data.projects');
        $created = collect($projects)->firstWhere('title.fa', 'پروژه آزمایشی');

        $this->assertNull($created['brandColor']);
        $this->assertSame('web', $created['coverType']);
        $this->assertSame('پروژه آزمایشی', $created['title']['en']);
    }

    public function test_project_media_is_served_per_language_with_fallback(): void
    {
        $make = fn (string $title, array $media): Project => Project::query()->create([
            'title' => $title,
            'description' => 'توضیح',
            'sort_order' => 100,
            'is_active' => true,
            ...$media,
        ]);

        $make('هر دو', [
            'image_path' => 'projects/fa.png',
            'image_path_en' => 'projects/en.png',
            'gallery_paths' => ['projects/gallery/fa-1.png'],
            'gallery_paths_en' => ['projects/gallery/en-1.png', 'projects/gallery/en-2.png'],
        ]);
        $make('فقط فارسی', ['image_path' => 'projects/fa.png', 'gallery_paths' => ['projects/gallery/fa-1.png']]);
        $make('فقط انگلیسی', ['image_path_en' => 'projects/en.png', 'gallery_paths_en' => ['projects/gallery/en-1.png']]);
        $make('بدون تصویر', []);

        $projects = collect($this->getJson('/api/portfolio')->assertOk()->json('data.projects'));
        $byTitle = fn (string $title): array => $projects->firstWhere('title.fa', $title);

        $both = $byTitle('هر دو');
        $this->assertSame(['fa' => '/storage/projects/fa.png', 'en' => '/storage/projects/en.png'], $both['image']);
        $this->assertSame(['/storage/projects/gallery/fa-1.png'], $both['gallery']['fa']);
        $this->assertCount(2, $both['gallery']['en']);

        $faOnly = $byTitle('فقط فارسی');
        $this->assertSame('/storage/projects/fa.png', $faOnly['image']['en']);
        $this->assertSame($faOnly['gallery']['fa'], $faOnly['gallery']['en']);

        $enOnly = $byTitle('فقط انگلیسی');
        $this->assertSame('/storage/projects/en.png', $enOnly['image']['fa']);
        $this->assertSame(['/storage/projects/gallery/en-1.png'], $enOnly['gallery']['fa']);

        $none = $byTitle('بدون تصویر');
        $this->assertSame(['fa' => null, 'en' => null], $none['image']);
        $this->assertSame(['fa' => [], 'en' => []], $none['gallery']);
    }
}
