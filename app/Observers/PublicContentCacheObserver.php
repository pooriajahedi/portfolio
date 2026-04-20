<?php

namespace App\Observers;

use App\Support\PublicApiCache;

class PublicContentCacheObserver
{
    public function saved(object $model): void
    {
        PublicApiCache::flush();
    }

    public function deleted(object $model): void
    {
        PublicApiCache::flush();
    }

    public function restored(object $model): void
    {
        PublicApiCache::flush();
    }

    public function forceDeleted(object $model): void
    {
        PublicApiCache::flush();
    }
}
