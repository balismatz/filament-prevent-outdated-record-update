<?php

namespace Workbench\App\Filament\Resources\Categories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Workbench\App\Filament\Resources\Categories\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
