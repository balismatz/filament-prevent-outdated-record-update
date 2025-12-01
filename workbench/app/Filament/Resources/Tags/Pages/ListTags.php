<?php

namespace Workbench\App\Filament\Resources\Tags\Pages;

use Filament\Resources\Pages\ListRecords;
use Workbench\App\Filament\Resources\Tags\TagResource;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;
}
