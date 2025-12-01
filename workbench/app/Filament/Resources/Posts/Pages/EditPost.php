<?php

namespace Workbench\App\Filament\Resources\Posts\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\Posts\PostResource;

class EditPost extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    protected static string $resource = PostResource::class;
}
