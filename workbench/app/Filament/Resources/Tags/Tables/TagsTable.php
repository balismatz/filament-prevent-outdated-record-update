<?php

namespace Workbench\App\Filament\Resources\Tags\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('updated_at'),
            ])
            ->recordActions([
                EditAction::make()->preventOutdatedRecordUpdate(),
            ]);
    }
}
