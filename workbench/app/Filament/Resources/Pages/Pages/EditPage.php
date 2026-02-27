<?php

namespace Workbench\App\Filament\Resources\Pages\Pages;

use BalisMatz\FilamentPreventOutdatedRecordUpdate\Concerns\PreventsOutdatedRecordUpdate;
use Filament\Resources\Pages\EditRecord;
use Workbench\App\Filament\Resources\Pages\PageResource;

class EditPage extends EditRecord
{
    use PreventsOutdatedRecordUpdate;

    protected static string $resource = PageResource::class;
}
