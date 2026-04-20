<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected Width | string | null $maxContentWidth = Width::Full;
}

