<?php

namespace App\Filament\Resources\BoardingHouses\Pages;

use App\Filament\Resources\BoardingHouses\BoardingHouseResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBoardingHouse extends ViewRecord
{
    protected static string $resource = BoardingHouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
