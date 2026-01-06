<?php

namespace App\Http\Controllers;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private BoardingHouseRepositoryInterface $boardingHouseRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        BoardingHouseRepositoryInterface $boardingHouseRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->boardingHouseRepository = $boardingHouseRepository;
        $this->categoryRepository = $categoryRepository;
    }


    public function show($slug)
    {
        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseByCategorySlug($slug);
        $categories = $this->categoryRepository->all();

        return view('pages.category.show', compact('boardingHouses'));
    }
}
