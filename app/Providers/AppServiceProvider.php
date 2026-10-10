<?php

namespace App\Providers;

use App\Models\AboutSection;
use App\Models\AboutServiceCard;
use App\Models\ContactSection;
use App\Models\HeroSection;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ResumeItem;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Observers\PublicContentCacheObserver;
use Illuminate\Support\Facades\Schema;
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
        // Indexed strings must fit older MySQL/MariaDB key limits (767 bytes on
        // InnoDB, 1000 on MyISAM): 191 characters of utf8mb4 is 764 bytes.
        Schema::defaultStringLength(191);

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
        SiteSetting::observe($observer);
    }
}
