<?php

namespace App\Repositories;

use App\Interface\CityRepositoryInterface;
use App\Models\City;

class CityRepository implements CityRepositoryInterface
{
    public function all()
    {
        return City::all();
    }

    public function getCityBySlug($slug)
    {
        return City::where('slug', $slug)->first();
    }
}
