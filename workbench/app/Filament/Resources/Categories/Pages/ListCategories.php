<?php

namespace Workbench\App\Filament\Resources\Categories\Pages;

use Filament\Resources\Pages\ListRecords;
use Workbench\App\Filament\Resources\Categories\CategoryResource;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;
}
