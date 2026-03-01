<?php

namespace App\Observers;

use App\Models\City;
use Illuminate\Support\Facades\Storage;

class CityObserver
{
    /**
     * Handle the City "created" event.
     */
    public function created(City $city): void {}

    /**
     * Handle the City "updated" event.
     */
    public function updated(City $city): void
    {
        if ($city->isDirty('image')) {
            $oldImage = $city->getOriginal('image');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }
    }

    /**
     * Handle the City "deleted" event.
     */
    public function deleted(City $city): void
    {
        if ($city->image) {
            Storage::disk('public')->delete($city->image);
        }
    }

    /**
     * Handle the City "restored" event.
     */
    public function restored(City $city): void
    {
        //
    }

    /**
     * Handle the City "force deleted" event.
     */
    public function forceDeleted(City $city): void
    {
        //
    }
}
