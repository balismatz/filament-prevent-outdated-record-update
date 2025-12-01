<?php

namespace Workbench\App\Filament\Resources\Categories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Categories\Pages\CreateCategory;
use Workbench\App\Filament\Resources\Categories\Pages\EditCategory;
use Workbench\App\Filament\Resources\Categories\Pages\ListCategories;
use Workbench\App\Filament\Resources\Categories\Schemas\CategoryForm;
use Workbench\App\Filament\Resources\Categories\Tables\CategoriesTable;
use Workbench\App\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
