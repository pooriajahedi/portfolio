<?php

namespace App\Providers;

use App\Models\AboutSection;
use App\Models\AboutServiceCard;
use App\Models\BlogPost;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Observers\PublicContentCacheObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $observer = PublicContentCacheObserver::class;

        AboutSection::observe($observer);
        AboutServiceCard::observe($observer);
        HeroSection::observe($observer);
        ContactSection::observe($observer);
        Skill::observe($observer);
        ResumeItem::observe($observer);
        ProjectCategory::observe($observer);
        Project::observe($observer);
        PortfolioSection::observe($observer);
        BlogPost::observe($observer);
        SiteSetting::observe($observer);
    }
}
