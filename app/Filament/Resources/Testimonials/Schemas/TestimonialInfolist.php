<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('boardingHouse.name')
                    ->numeric(),
                ImageEntry::make('photo')
                    ->disk('public')
                    ->visibility('public'),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
// LANJUTKAN DI MENIT 4:46