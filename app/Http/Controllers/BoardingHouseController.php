<?php

namespace App\Http\Controllers;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Interface\CityRepositoryInterface;
use Illuminate\Http\Request;

class BoardingHouseController extends Controller
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


    public function show($slug)
    {
        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);

        return view('pages.boarding-house.show', compact('boardingHouses'));
    }

    public function find()
    {

        $cities = $this->cityRepository->all();
        $categories = $this->categoryRepository->all();

        return view(
            'pages.boarding-house.find',
            compact(
                'cities',
                'categories'
            )
        );
    }

    public function rooms($slug)
    {
        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);

        return view('pages.boarding-house.rooms', compact('boardingHouses'));
    }

    public function findResult(Request $request)
    {

        $boardingHouses = $this->boardingHouseRepository->getAllBoardingHouses($request->search, $request->city, $request->category);

        return view('pages.boarding-house.index', compact('boardingHouses'));
    }
}
