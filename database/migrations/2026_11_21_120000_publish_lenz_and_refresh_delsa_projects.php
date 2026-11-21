<?php

use App\Support\ProjectCatalog;
use Illuminate\Database\Migrations\Migration;

/**
 * Publishes Lenz Trading Journal and gives Delsa Gallery its real-domain pictures and link.
 * The data and the images travel with the repository, so a deploy is all it takes.
 */
return new class extends Migration
{
    private const CATALOG = 'content/projects/2026-11-lenz-and-delsa';

    public function up(): void
    {
        // Tests migrate a fresh database and seed their own projects; this content would get in their way.
        if (app()->runningUnitTests()) {
            return;
        }

        (new ProjectCatalog(database_path(self::CATALOG)))->publish();
    }

    public function down(): void
    {
        // Delsa's earlier pictures are gone for good; only the project this migration added is removed.
        (new ProjectCatalog(database_path(self::CATALOG)))->remove(['lenz-trading-journal']);
    }
};
