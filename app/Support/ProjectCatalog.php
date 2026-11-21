<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

/**
 * Publishes a folder of portfolio projects: their rows, their category and their pictures.
 *
 * The folder holds `data.json` and the pictures under `files/`, laid out like the public disk
 * (`files/projects/…`). It is meant for data migrations, so a deploy carries the content along.
 * Plain query-builder calls are used on purpose: a migration that depends on the Eloquent models
 * would break the day a model changes.
 *
 * Publishing twice is harmless. A project that already exists keeps everything except its pictures
 * and link, which are replaced when the folder carries different ones. Two per-item flags in data.json
 * widen that: `sync_text` also replaces the title and description of an existing project, and
 * `update_only` makes sure a project missing from this database is never created.
 */
final class ProjectCatalog
{
    public function __construct(private readonly string $directory) {}

    public function publish(): void
    {
        $data = $this->read();
        $disk = Storage::disk('public');
        $written = [];
        $replaced = [];

        try {
            DB::transaction(function () use ($data, $disk, &$written, &$replaced): void {
                foreach ($data['projects'] as $item) {
                    $existing = $this->find($item);

                    // An update-only item never creates a project that is missing from this database.
                    if ($existing === null && ($item['update_only'] ?? false)) {
                        continue;
                    }

                    foreach ($this->paths($item) as $path) {
                        if (! $disk->exists($path)) {
                            $disk->put($path, file_get_contents("{$this->directory}/files/{$path}"));
                            $written[] = $path;
                        }
                    }

                    if ($existing === null) {
                        $this->create($item, collect($data['categories'])->firstWhere('slug', $item['category_slug'] ?? null));

                        continue;
                    }

                    array_push($replaced, ...$this->refresh($existing, $item));

                    if ($item['sync_text'] ?? false) {
                        $this->syncText($existing, $item);
                    }
                }
            });
        } catch (Throwable $e) {
            $disk->delete($written);

            throw $e;
        }

        // The old pictures go only once the new ones are in place and nothing refers to them.
        $disk->delete(array_values(array_diff(array_unique($replaced), $this->inUse())));

        PublicApiCache::flush();
    }

    /**
     * Remove projects by slug, together with the pictures no other project uses.
     *
     * @param  list<string>  $slugs
     */
    public function remove(array $slugs): void
    {
        $rows = DB::table('projects')->whereIn('slug', $slugs)->get();

        DB::table('projects')->whereIn('slug', $slugs)->delete();

        $orphans = $rows->flatMap(fn (object $row): array => $this->paths($row))->unique()->all();
        Storage::disk('public')->delete(array_values(array_diff($orphans, $this->inUse())));

        PublicApiCache::flush();
    }

    /**
     * @return array{projects: list<array<string, mixed>>, categories: list<array<string, mixed>>}
     */
    private function read(): array
    {
        $file = "{$this->directory}/data.json";
        is_file($file) || throw new RuntimeException("Catalog data not found: {$file}");

        $data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['projects'] ?? [] as $item) {
            foreach (['slug', 'title', 'description'] as $key) {
                filled($item[$key] ?? null) || throw new RuntimeException("A catalog project has no {$key}.");
            }

            foreach ($this->paths($item) as $path) {
                is_file("{$this->directory}/files/{$path}") || throw new RuntimeException("Catalog picture missing: files/{$path}");
            }
        }

        return $data + ['projects' => [], 'categories' => []];
    }

    /** @param  array<string, mixed>  $item */
    private function find(array $item): ?object
    {
        return DB::table('projects')
            ->where(fn ($query) => $query
                ->where('slug', $item['slug'])
                ->when(filled($item['title_en'] ?? null), fn ($query) => $query->orWhere('title_en', $item['title_en'])))
            ->first();
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>|null  $category
     */
    private function create(array $item, ?array $category): void
    {
        $categoryId = null;
        $categorySlug = $item['category_slug'] ?? null;

        if (filled($categorySlug)) {
            $categoryId = DB::table('project_categories')->where('slug', $categorySlug)->value('id')
                ?? DB::table('project_categories')->insertGetId([
                    'title' => $category['title'] ?? $categorySlug,
                    'title_en' => $category['title_en'] ?? null,
                    'slug' => $categorySlug,
                    'sort_order' => ((int) DB::table('project_categories')->max('sort_order')) + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        DB::table('projects')->insert([
            'title' => $item['title'],
            'title_en' => $item['title_en'] ?? null,
            'slug' => $item['slug'],
            'description' => $item['description'],
            'description_en' => $item['description_en'] ?? null,
            'tags' => $this->json($item['tags'] ?? []),
            'brand_color' => $item['brand_color'] ?? null,
            'cover_type' => $item['cover_type'] ?? 'web',
            'project_url' => filled($item['project_url'] ?? null) ? $item['project_url'] : null,
            'image_path' => $item['image_path'] ?? null,
            'image_path_en' => $item['image_path_en'] ?? null,
            'gallery_paths' => $this->json($item['gallery_paths'] ?? []),
            'gallery_paths_en' => $this->json($item['gallery_paths_en'] ?? []),
            'project_category_id' => $categoryId,
            'sort_order' => ((int) DB::table('projects')->max('sort_order')) + 1,
            'is_active' => (bool) ($item['is_active'] ?? true),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Swap in the catalog's pictures and link; returns the paths that were replaced.
     *
     * @param  array<string, mixed>  $item
     * @return list<string>
     */
    private function refresh(object $existing, array $item): array
    {
        $cover = $item['image_path'] ?? null;

        if ($cover === null || $cover === $existing->image_path) {
            return [];
        }

        DB::table('projects')->where('id', $existing->id)->update([
            'image_path' => $cover,
            'image_path_en' => $item['image_path_en'] ?? null,
            'gallery_paths' => $this->json($item['gallery_paths'] ?? []),
            'gallery_paths_en' => $this->json($item['gallery_paths_en'] ?? []),
            'project_url' => filled($item['project_url'] ?? null) ? $item['project_url'] : $existing->project_url,
            'updated_at' => now(),
        ]);

        return $this->paths($existing);
    }

    /**
     * Replace the titles and descriptions of an existing project (only for items marked `sync_text`).
     *
     * @param  array<string, mixed>  $item
     */
    private function syncText(object $existing, array $item): void
    {
        $text = array_filter([
            'title' => $item['title'] ?? null,
            'title_en' => $item['title_en'] ?? null,
            'description' => $item['description'] ?? null,
            'description_en' => $item['description_en'] ?? null,
        ], fn (?string $value): bool => filled($value));

        DB::table('projects')->where('id', $existing->id)->update($text + ['updated_at' => now()]);
    }

    /** @return list<string> */
    private function inUse(): array
    {
        return DB::table('projects')
            ->get(['image_path', 'image_path_en', 'gallery_paths', 'gallery_paths_en'])
            ->flatMap(fn (object $row): array => $this->paths($row))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Every picture a project or catalog item refers to, across both languages.
     *
     * @param  array<string, mixed>|object  $record
     * @return list<string>
     */
    private function paths(array|object $record): array
    {
        $record = (array) $record;
        $list = fn (mixed $value): array => is_string($value) ? (json_decode($value, true) ?? []) : ($value ?? []);

        return array_values(array_unique(array_filter([
            $record['image_path'] ?? null,
            $record['image_path_en'] ?? null,
            ...$list($record['gallery_paths'] ?? null),
            ...$list($record['gallery_paths_en'] ?? null),
        ])));
    }

    private function json(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
