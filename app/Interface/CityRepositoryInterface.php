<?php

namespace App\Interface;

interface CityRepositoryInterface
{
    public function all();

    public function getCityBySlug($slug);
}
