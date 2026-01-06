<?php

namespace App\Repositories;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Models\BoardingHouse;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

class BoardingHouseRepository implements BoardingHouseRepositoryInterface
{
    public function getAllBoardingHouses($search = null, $city = null, $category = null)
    {
        $query = BoardingHouse::query();

        if ($search) {
            $query->where('name', 'Like', '%' . $search . '%');
        }

        if ($city) {
            $query->whereHas('city', function ($query) use ($city) {
                $query->where('slug', $city);
            });
        }
        return $query->get();
    }

    public function getPopularBoardingHouses($limit = 5)
    {
        return BoardingHouse::withCount('transactions')->orderBy('transactions_count', 'desc')->limit($limit)->get();
    }

    public function getBoardingHouseByCitySlug($slug)
    {
        return BoardingHouse::whereHas('city', function (Builder $query) use ($slug) {
            $query->where('slug', $slug);
        })->get();
    }

    public function getBoardingHouseByCategorySlug($slug)
    {
        return BoardingHouse::whereHas('category', function (Builder $query) use ($slug) {
            $query->where('slug', $slug);
        })->get();
    }

    public function getBoardingHouseBySlug($slug)
    {
        return BoardingHouse::where('slug', $slug)->first();
    }

    public function getBoardingHouseRoomById($id)
    {
        return Room::find($id);
    }
}
