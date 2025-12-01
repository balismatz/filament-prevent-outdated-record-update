<?php

namespace Workbench\App\Filament\Resources\Tags\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\Tags\TagResource;

class EditTag extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    protected static string $resource = TagResource::class;
}
