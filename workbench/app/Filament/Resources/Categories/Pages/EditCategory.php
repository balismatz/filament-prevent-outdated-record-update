<?php

namespace Workbench\App\Filament\Resources\Categories\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\Categories\CategoryResource;

class EditCategory extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    protected static string $resource = CategoryResource::class;
}
