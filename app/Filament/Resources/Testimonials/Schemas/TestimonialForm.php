<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('boarding_house_id')
                    ->relationship('boardingHouse', 'name')
                    ->required(),
                FileUpload::make('photo')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('testimonials')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('rating')
                    ->required()
                    ->minValue(1)
                    ->maxValue(5)
                    ->numeric(),
            ]);
    }
}
