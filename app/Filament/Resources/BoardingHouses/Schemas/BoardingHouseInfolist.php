<?php

namespace App\Filament\Resources\BoardingHouses\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BoardingHouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Informasi Umum')
                            ->schema([
                                ImageEntry::make('thumbnail')
                                    ->disk('public')
                                    ->visibility('public'),
                                TextEntry::make('name')
                                    ->label('Nama'),
                                TextEntry::make('slug'),
                                TextEntry::make('city.name')
                                    ->label('Kota'),
                                TextEntry::make('category.name')
                                    ->label('Kategori'),
                                TextEntry::make('description')
                                    ->html()
                                    ->label('Deskripsi'),
                                TextEntry::make('price')
                                    ->money('IDR')
                                    ->label('Harga'),
                                TextEntry::make('address')
                                    ->label('Alamat'),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->label('Dibuat pada'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->label('Diupdate pada'),
                            ]),
                        Tab::make('Bonus Ngekos')
                            ->schema([
                                RepeatableEntry::make('bonuses')
                                    ->schema([
                                        ImageEntry::make('image')
                                            ->disk('public')
                                            ->visibility('public'),
                                        TextEntry::make('name')
                                            ->label('Nama Bonus'),
                                        TextEntry::make('description')
                                            ->label('Deskripsi'),
                                    ])
                            ]),
                        Tab::make('Kamar')
                            ->schema([
                                RepeatableEntry::make('rooms')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Nama Kamar'),
                                        TextEntry::make('room_type')
                                            ->label('Tipe Kamar'),
                                        TextEntry::make('square_feet')
                                            ->label('Luas (kaki persegi)'),
                                        TextEntry::make('capacity')
                                            ->label('Kapasitas'),
                                        TextEntry::make('price_per_month')
                                            ->money('IDR')
                                            ->label('Harga per Bulan'),
                                        TextEntry::make('is_available')
                                            ->badge()
                                            ->color(fn($state) => $state ? 'success' : 'danger')
                                            ->formatStateUsing(fn($state) => $state ? 'Tersedia' : 'Tidak Tersedia')
                                            ->label('Status'),
                                        RepeatableEntry::make('images')
                                            ->schema([
                                                ImageEntry::make('image')
                                                    ->disk('public')
                                                    ->label('Foto Kamar'),
                                            ])
                                    ])
                            ]),
                    ])->columnSpanFull()
            ]);
    }
}
