<?php

use App\Support\ProjectCatalog;
use Illuminate\Database\Migrations\Migration;

/**
 * Publishes Vesam Academy and rewrites the descriptions of the other projects with longer,
 * plainer ones. The text-only items never create a project that is missing from the database.
 */
return new class extends Migration
{
    private const CATALOG = 'content/projects/2026-11-vesam-and-texts';

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
        // The earlier descriptions are not kept; only the project this migration added is removed.
        (new ProjectCatalog(database_path(self::CATALOG)))->remove(['vesam-academy']);
    }
};
