<?php

namespace Workbench\App\Filament\Resources\Tags;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Tags\Pages\CreateTag;
use Workbench\App\Filament\Resources\Tags\Pages\EditTag;
use Workbench\App\Filament\Resources\Tags\Pages\ListTags;
use Workbench\App\Filament\Resources\Tags\Schemas\TagForm;
use Workbench\App\Filament\Resources\Tags\Tables\TagsTable;
use Workbench\App\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
