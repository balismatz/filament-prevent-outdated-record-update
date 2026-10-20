<?php

namespace Workbench\App\Filament\Resources\Posts\Pages;

use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Posts\PostResource;

class ManageRelatedPosts extends ManageRelatedRecords
{
    protected static string $resource = PostResource::class;

    protected static string $relationship = 'relatedPosts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('information')
                    ->string()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('information'),
            ])
            ->recordActions([
                EditAction::make()->preventOutdatedRecordUpdate(),
                EditAction::make('edit-pivot')->preventOutdatedRecordUpdate(true),
                EditAction::make('edit-pivot-allow-outdated')
                    ->preventOutdatedRecordUpdate(true)
                    ->allowOutdatedRecordUpdate(),
            ])
            ->recordTitleAttribute('title');
    }
}
