<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioFeedResource;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectCategoryResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SiteStateResource;
use App\Models\AboutSection;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Support\ProjectSlug;
use App\Support\PublicApiCache;

class PublicContentController extends Controller
{
    public function site(): SiteStateResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::SITE_KEY, fn (): array => $this->buildSitePayload());

        return new SiteStateResource($payload);
    }

    public function portfolio(): PortfolioFeedResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::PORTFOLIO_KEY, function (): array {
            $portfolioSection = PortfolioSection::query()->latest('id')->first();

            $categories = ProjectCategory::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['title', 'title_en', 'slug']);

            $projects = Project::query()
                ->active()
                ->with('category:id,title,title_en,slug')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'title_en',
                    'slug',
                    'description',
                    'description_en',
                    'tags',
                    'project_url',
                    'image_path',
                    'gallery_paths',
                    'project_category_id',
                    'created_at',
                    'updated_at',
                ]);

            return [
                'title' => [
                    'fa' => $portfolioSection?->title ?: 'نمونه کارها',
                    'en' => $portfolioSection?->title_en ?: 'Projects',
                ],
                'categories' => ProjectCategoryResource::collection($categories)->resolve(),
                'projects' => ProjectResource::collection($projects)->resolve(),
            ];
        });

        return new PortfolioFeedResource($payload);
    }

    public function portfolioProject(string $slug): ProjectDetailResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::portfolioProjectKey($slug), function () use ($slug): array {
            $projects = Project::query()
                ->active()
                ->with('category:id,title,title_en,slug')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get([
                    'id',
                    'title',
                    'title_en',
                    'slug',
                    'description',
                    'description_en',
                    'tags',
                    'project_url',
                    'image_path',
                    'gallery_paths',
                    'project_category_id',
                    'created_at',
                    'updated_at',
                ]);

            $project = $projects->first(
                fn (Project $item): bool => ProjectSlug::resolve($item->slug, (string) $item->title, $item->id) === $slug
            );

            abort_unless($project, 404);

            return [
                'project' => $project,
            ];
        });

        return new ProjectDetailResource($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSitePayload(): array
    {
        $hero = HeroSection::query()->latest('id')->first();
        $about = AboutSection::query()->active()->latest('id')->first();
        $contact = ContactSection::query()->active()->latest('id')->first();
        $siteSettings = SiteSetting::getMany(SiteSetting::defaults());

        $starfieldEnabled = filter_var(
            (string) ($siteSettings[SiteSetting::KEY_STARFIELD_ENABLED] ?? '1'),
            FILTER_VALIDATE_BOOL
        );

        $starfieldOpacity = max(0, min(100, (int) ($siteSettings[SiteSetting::KEY_STARFIELD_OPACITY] ?? 80)));

        $status = $hero?->current_status ?: HeroSection::STATUS_LOOKING_FOR_JOB;

        return [
            'profile' => [
                'handle' => 'pooria.jahedi',
                'name' => [
                    'fa' => $hero?->name ?: 'پوریا جاهدی',
                    'en' => $hero?->name_en ?: 'Pooria Jahedi',
                ],
                'role' => [
                    'fa' => $hero?->role ?: 'برنامه‌نویس ارشد بک‌اند و فول‌استک',
                    'en' => $hero?->role_en ?: 'Senior Backend & Full-Stack Developer',
                ],
                'tagline' => [
                    'fa' => $hero?->tagline ?: '',
                    'en' => $hero?->tagline_en ?: '',
                ],
                'avatarImage' => $hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg',
                'resumeFile' => $hero?->resume_file,
                'resumeFileVersion' => $hero?->updated_at?->timestamp,
                'status' => [
                    'key' => $status,
                    'fa' => HeroSection::statusLabel($status),
                    'en' => HeroSection::statusLabelEn($status),
                ],
            ],
            'skillGroups' => $this->buildSkillGroups($about),
            'links' => [
                'email' => $contact?->email ?: '',
                'emailIcon' => $contact?->email_icon ?: ContactSection::ICON_EMAIL,
                'github' => $contact?->github ?: '',
                'githubIcon' => $contact?->github_icon ?: ContactSection::ICON_GITHUB,
                'linkedin' => $contact?->linkedin ?: '',
                'linkedinIcon' => $contact?->linkedin_icon ?: ContactSection::ICON_LINKEDIN,
                'telegram' => $contact?->telegram ?: '',
                'telegramIcon' => $contact?->telegram_icon ?: ContactSection::ICON_TELEGRAM,
            ],
            'appearance' => [
                'starfield' => [
                    'enabled' => $starfieldEnabled,
                    'opacity' => $starfieldOpacity,
                ],
            ],
            'appVersion' => (string) config('app.version', '1.0.0'),
        ];
    }

    /**
     * Skills bucketed into their categories, in the order the categories are configured.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildSkillGroups(?AboutSection $about): array
    {
        $skills = Skill::query()
            ->active()
            ->whereNotNull('icon')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['title', 'title_en', 'category', 'icon'])
            ->map(fn (Skill $skill): array => [
                'category' => (string) $skill->category,
                'title' => [
                    'fa' => (string) $skill->title,
                    'en' => $skill->title_en ?: (string) $skill->title,
                ],
                'icon' => $this->normalizeSkillIcon((string) ($skill->icon ?? '')),
            ])
            ->groupBy('category');

        return collect(AboutSection::skillCategoryList($about?->skill_categories))
            ->map(fn (array $category): array => [
                'key' => $category['key'],
                'title' => [
                    'fa' => $category['label'],
                    'en' => $category['label_en'],
                ],
                'items' => $skills->get($category['key'], collect())
                    ->map(fn (array $skill): array => [
                        'title' => $skill['title'],
                        'icon' => $skill['icon'],
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();
    }

    private function normalizeSkillIcon(string $icon): string
    {
        return $icon === 'simple-icons:httptoolkit' ? 'mdi:toolbox-outline' : $icon;
    }
}
