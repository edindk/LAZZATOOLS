<?php

namespace App\Http\Controllers;

use App\City;

class CityController extends Controller
{
    /**
     * Returnerer alle byer
     *
     * @return City[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCities()
    {
        return City::all();
    }
}
