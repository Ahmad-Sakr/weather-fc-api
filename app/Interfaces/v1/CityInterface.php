<?php


namespace App\Interfaces\v1;


use App\Http\Requests\v1\CityRequest;
use App\Models\City;

interface CityInterface
{
    public function getAllCities();

    public function getSingleCity(City $city);

    public function storeCity(CityRequest $request, City $city = null);

    public function deleteCity(City $city);
}
