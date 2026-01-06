<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('city')
                    ->visibility('public')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required(),
            ]);
    }
}
