<?php

namespace Tests\Feature;

use App\Support\ProjectCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ProjectCatalogTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->directory = sys_get_temp_dir().'/catalog-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    /**
     * Write a catalog folder: one project with a cover and one gallery picture per language.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function catalog(string $version = 'a', array $overrides = []): void
    {
        $paths = [
            'image_path' => "projects/cover-fa-{$version}.webp",
            'image_path_en' => "projects/cover-en-{$version}.webp",
            'gallery_paths' => ["projects/gallery/one-fa-{$version}.webp"],
            'gallery_paths_en' => ["projects/gallery/one-en-{$version}.webp"],
        ];

        foreach (collect($paths)->flatten() as $path) {
            File::ensureDirectoryExists(dirname("{$this->directory}/files/{$path}"));
            File::put("{$this->directory}/files/{$path}", "picture {$path}");
        }

        File::put("{$this->directory}/data.json", json_encode([
            'categories' => [['slug' => 'web-application', 'title' => 'وب‌اپلیکیشن', 'title_en' => 'Web Application']],
            'projects' => [[
                'slug' => 'demo',
                'title' => 'نمونه',
                'title_en' => 'Demo',
                'description' => 'توضیح',
                'description_en' => 'Description',
                'tags' => ['Laravel'],
                'brand_color' => '#112233',
                'cover_type' => 'web',
                'project_url' => 'https://demo.example',
                'category_slug' => 'web-application',
                ...$paths,
                ...$overrides,
            ]],
        ], JSON_UNESCAPED_UNICODE));
    }

    private function publish(): void
    {
        (new ProjectCatalog($this->directory))->publish();
    }

    public function test_publishing_creates_the_project_its_category_and_the_pictures(): void
    {
        $this->catalog();

        $this->publish();

        $project = DB::table('projects')->where('slug', 'demo')->first();

        $this->assertSame('نمونه', $project->title);
        $this->assertSame('https://demo.example', $project->project_url);
        $this->assertSame(['Laravel'], json_decode($project->tags, true));
        $this->assertSame(['projects/gallery/one-en-a.webp'], json_decode($project->gallery_paths_en, true));
        $this->assertSame('web-application', DB::table('project_categories')->where('id', $project->project_category_id)->value('slug'));

        Storage::disk('public')->assertExists(['projects/cover-fa-a.webp', 'projects/cover-en-a.webp', 'projects/gallery/one-fa-a.webp', 'projects/gallery/one-en-a.webp']);
    }

    public function test_publishing_twice_changes_nothing(): void
    {
        $this->catalog();

        $this->publish();
        $this->publish();

        $this->assertSame(1, DB::table('projects')->count());
        $this->assertSame(1, DB::table('project_categories')->count());
    }

    public function test_an_existing_category_is_reused(): void
    {
        DB::table('project_categories')->insert(['title' => 'اپلیکیشن ها', 'slug' => 'web-application', 'sort_order' => 1, 'is_active' => true]);
        $this->catalog();

        $this->publish();

        $this->assertSame(1, DB::table('project_categories')->count());
        $this->assertSame('اپلیکیشن ها', DB::table('project_categories')->value('title'));
    }

    public function test_an_existing_project_gets_new_pictures_and_link_and_loses_the_old_files(): void
    {
        $this->catalog('a', ['project_url' => null]);
        $this->publish();

        $this->catalog('b', ['project_url' => 'https://real.example']);
        $this->publish();

        $project = DB::table('projects')->where('slug', 'demo')->first();

        $this->assertSame(1, DB::table('projects')->count());
        $this->assertSame('projects/cover-fa-b.webp', $project->image_path);
        $this->assertSame('https://real.example', $project->project_url);
        $this->assertSame('نمونه', $project->title, 'Text is left alone');

        Storage::disk('public')->assertExists('projects/cover-fa-b.webp');
        Storage::disk('public')->assertMissing(['projects/cover-fa-a.webp', 'projects/gallery/one-en-a.webp']);
    }

    public function test_sync_text_replaces_the_text_of_an_existing_project(): void
    {
        $this->catalog();
        $this->publish();

        $this->catalog('a', ['description' => 'متن تازه', 'description_en' => 'New text', 'sync_text' => true]);
        $this->publish();

        $project = DB::table('projects')->where('slug', 'demo')->first();

        $this->assertSame('متن تازه', $project->description);
        $this->assertSame('New text', $project->description_en);
    }

    public function test_an_update_only_item_is_never_created(): void
    {
        $this->catalog('a', ['update_only' => true, 'sync_text' => true]);

        $this->publish();

        $this->assertSame(0, DB::table('projects')->count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_an_update_only_item_without_pictures_changes_text_and_leaves_the_pictures(): void
    {
        $this->catalog();
        $this->publish();

        $this->catalog('a', [
            'update_only' => true, 'sync_text' => true, 'description' => 'فقط متن',
            'image_path' => null, 'image_path_en' => null, 'gallery_paths' => [], 'gallery_paths_en' => [],
        ]);
        $this->publish();

        $project = DB::table('projects')->where('slug', 'demo')->first();

        $this->assertSame('فقط متن', $project->description);
        $this->assertSame('projects/cover-fa-a.webp', $project->image_path);
        Storage::disk('public')->assertExists('projects/cover-fa-a.webp');
    }

    public function test_a_missing_picture_stops_everything_before_anything_is_written(): void
    {
        $this->catalog();
        File::delete("{$this->directory}/files/projects/cover-en-a.webp");

        $this->expectException(RuntimeException::class);

        try {
            $this->publish();
        } finally {
            $this->assertSame(0, DB::table('projects')->count());
            $this->assertSame([], Storage::disk('public')->allFiles());
        }
    }

    public function test_removing_a_project_deletes_its_pictures_unless_another_project_uses_them(): void
    {
        $this->catalog();
        $this->publish();

        DB::table('projects')->insert([
            'title' => 'دیگر', 'slug' => 'other', 'description' => 'x', 'cover_type' => 'web', 'sort_order' => 9,
            'is_active' => true, 'image_path' => 'projects/cover-fa-a.webp',
        ]);

        (new ProjectCatalog($this->directory))->remove(['demo']);

        $this->assertSame(['other'], DB::table('projects')->pluck('slug')->all());
        Storage::disk('public')->assertExists('projects/cover-fa-a.webp');
        Storage::disk('public')->assertMissing('projects/cover-en-a.webp');
    }
}
