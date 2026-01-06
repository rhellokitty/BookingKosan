<?php

namespace App\Filament\Resources\BoardingHouses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Str;

class BoardingHouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Informasi Umum')
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('boarding_house')
                                    ->required(),
                                TextInput::make('name')
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->required(),
                                Select::make('city_id')
                                    ->required()
                                    ->relationship('city', 'name'),
                                Select::make('category_id')
                                    ->required()
                                    ->relationship('category', 'name'),
                                RichEditor::make('description')
                                    ->required(),
                                TextInput::make('price')
                                    ->required()
                                    ->prefix('IDR')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric(),
                                Textarea::make('address')
                                    ->required(),
                            ]),
                        Tab::make('Bonus Ngekos')
                            ->schema([
                                Repeater::make('bonuses')
                                    ->relationship('bonuses')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('bonuses')
                                            ->visibility('public')
                                            ->required(),
                                        TextInput::make('name')
                                            ->required(),
                                        Textarea::make('description')
                                            ->required(),
                                    ])
                            ]),
                        Tab::make('Kamar')
                            ->schema([
                                Repeater::make('rooms')
                                    ->relationship('rooms')
                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('room_type')
                                            ->required(),
                                        TextInput::make('square_feet')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('capacity')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('price_per_month')
                                            ->numeric()
                                            ->prefix('IDR')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->required(),
                                        Toggle::make('is_available')
                                            ->required(),
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('rooms')
                                                    ->visibility('public')
                                                    ->required(),
                                            ])


                                    ])
                            ]),
                    ])->columnSpan(2)
            ]);
    }
}
