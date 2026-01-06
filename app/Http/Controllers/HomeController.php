<?php

namespace App\Http\Controllers;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Interface\CityRepositoryInterface;

class HomeController extends Controller
{
    private BoardingHouseRepositoryInterface $boardingHouseRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private CityRepositoryInterface $cityRepository;

    public function __construct(
        BoardingHouseRepositoryInterface $boardingHouseRepository,
        CategoryRepositoryInterface $categoryRepository,
        CityRepositoryInterface $cityRepository
    ) {
        $this->boardingHouseRepository = $boardingHouseRepository;
        $this->categoryRepository = $categoryRepository;
        $this->cityRepository = $cityRepository;
    }

    public function index()
    {
        $categories = $this->categoryRepository->all();
        $popularBoardingHouses = $this->boardingHouseRepository->getPopularBoardingHouses();
        $cities = $this->cityRepository->all();

        return view(
            'pages.home',
            compact(
                'categories',
                'popularBoardingHouses',
                'cities'
            )
        );
    }
}
