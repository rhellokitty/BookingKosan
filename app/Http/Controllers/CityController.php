<?php

namespace App\Http\Controllers;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Interface\CityRepositoryInterface;
use Illuminate\Http\Request;

class CityController extends Controller
{
    private CityRepositoryInterface $cityRepository;

    private BoardingHouseRepositoryInterface $boardingHouseRepository;

    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        CityRepositoryInterface $cityRepository,
        BoardingHouseRepositoryInterface $boardingHouseRepository,
        CategoryRepositoryInterface $categoryRepository,
    ) {
        $this->cityRepository = $cityRepository;
        $this->boardingHouseRepository = $boardingHouseRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function show($slug)
    {
        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseByCitySlug($slug);
        $categories = $this->categoryRepository->all();
        $cities = $this->cityRepository->getCityBySlug($slug);

        return view('pages.city.show', compact('cities', 'categories', 'boardingHouses'));
    }
}
