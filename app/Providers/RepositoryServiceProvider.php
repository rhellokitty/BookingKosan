<?php

namespace App\Providers;

use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\CategoryRepositoryInterface;
use App\Interface\CityRepositoryInterface;
use App\Interface\TransactionRepositoryInterface;
use App\Repositories\BoardingHouseRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CityRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BoardingHouseRepositoryInterface::class, BoardingHouseRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
