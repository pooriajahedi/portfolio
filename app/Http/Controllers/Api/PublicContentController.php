<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceItemResource;
use App\Http\Resources\PortfolioFeedResource;
use App\Http\Resources\ProjectCategoryResource;
use App\Http\Resources\ProjectDetailResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SiteStateResource;
use App\Models\AboutSection;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\Skill;
use App\Support\LocalizedText;
use App\Support\ProjectSlug;
use App\Support\PublicApiCache;
use Illuminate\Database\Eloquent\Collection;

class PublicContentController extends Controller
{
    private const PROJECT_COLUMNS = [
        'id',
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'tags',
        'brand_color',
        'cover_type',
        'project_url',
        'image_path',
        'image_path_en',
        'gallery_paths',
        'gallery_paths_en',
        'project_category_id',
        'created_at',
        'updated_at',
    ];

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

            return [
                'title' => LocalizedText::pair($portfolioSection?->title ?: 'نمونه کارها', $portfolioSection?->title_en ?: 'Projects'),
                'categories' => ProjectCategoryResource::collection($categories)->resolve(),
                'projects' => ProjectResource::collection($this->activeProjects())->resolve(),
            ];
        });

        return new PortfolioFeedResource($payload);
    }

    public function portfolioProject(string $slug): ProjectDetailResource
    {
        $payload = PublicApiCache::remember(PublicApiCache::portfolioProjectKey($slug), function () use ($slug): array {
            $project = $this->activeProjects()->first(
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
     * @return Collection<int, Project>
     */
    private function activeProjects(): Collection
    {
        return Project::query()
            ->active()
            ->with('category:id,title,title_en,slug')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(self::PROJECT_COLUMNS);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSitePayload(): array
    {
        $about = AboutSection::query()->active()->latest('id')->first();
        $contact = ContactSection::query()->active()->latest('id')->first();

        return [
            'profile' => $this->buildProfile(HeroSection::query()->latest('id')->first()),
            'about' => $this->buildAbout($about),
            'skillGroups' => $this->buildSkillGroups($about),
            'experience' => ExperienceItemResource::collection($this->activeExperience())->resolve(),
            'contact' => $this->buildContact($contact),
            'links' => $this->buildLinks($contact),
            'appVersion' => (string) config('app.version', '1.0.0'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildProfile(?HeroSection $hero): array
    {
        $status = $hero?->current_status ?: HeroSection::STATUS_LOOKING_FOR_JOB;

        return [
            'handle' => 'pooria.jahedi',
            'name' => LocalizedText::pair($hero?->name ?: 'پوریا جاهدی', $hero?->name_en ?: 'Pooria Jahedi'),
            'role' => LocalizedText::pair(
                $hero?->role ?: 'برنامه‌نویس ارشد بک‌اند و فول‌استک',
                $hero?->role_en ?: 'Senior Backend & Full-Stack Developer',
            ),
            'tagline' => LocalizedText::pair($hero?->tagline, $hero?->tagline_en),
            'avatarImage' => $hero?->avatar_image ?: '/images/hero/pooria-hero.jpeg',
            'resumeFile' => $hero?->resume_file,
            'resumeFileVersion' => $hero?->updated_at?->timestamp,
            'status' => [
                'key' => $status,
                'fa' => HeroSection::statusLabel($status),
                'en' => HeroSection::statusLabelEn($status),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildAbout(?AboutSection $about): ?array
    {
        if (! $about) {
            return null;
        }

        $paragraphs = collect([
            LocalizedText::pair($about->paragraph_one, $about->paragraph_one_en),
            LocalizedText::pair($about->paragraph_two, $about->paragraph_two_en),
        ])->filter(fn (array $paragraph): bool => $paragraph['fa'] !== '')->values()->all();

        $services = $about->serviceCards()
            ->where('is_active', true)
            ->get()
            ->map(fn ($card): array => [
                'title' => LocalizedText::pair($card->title, $card->title_en),
                'text' => LocalizedText::pair($card->description, $card->description_en),
            ])
            ->all();

        return [
            'headline' => LocalizedText::pair($about->headline, $about->headline_en),
            'paragraphs' => $paragraphs,
            'services' => $services,
        ];
    }

    /**
     * @return Collection<int, ResumeItem>
     */
    private function activeExperience(): Collection
    {
        return ResumeItem::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildContact(?ContactSection $contact): ?array
    {
        if (! $contact) {
            return null;
        }

        return [
            'headline' => LocalizedText::pair($contact->headline, $contact->headline_en),
            'text' => LocalizedText::pair($contact->description, $contact->description_en),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function buildLinks(?ContactSection $contact): array
    {
        return [
            'email' => $contact?->email ?: '',
            'github' => $contact?->github ?: '',
            'linkedin' => $contact?->linkedin ?: '',
            'telegram' => $contact?->telegram ?: '',
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
                'title' => LocalizedText::pair($skill->title, $skill->title_en),
                'icon' => $this->normalizeSkillIcon((string) ($skill->icon ?? '')),
            ])
            ->groupBy('category');

        return collect(AboutSection::skillCategoryList($about?->skill_categories))
            ->map(fn (array $category): array => [
                'key' => $category['key'],
                'title' => LocalizedText::pair($category['label'], $category['label_en']),
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
