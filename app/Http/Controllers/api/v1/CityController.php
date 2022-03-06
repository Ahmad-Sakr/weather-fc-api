<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CityRequest;
use App\Interfaces\v1\CityInterface;
use App\Models\City;

class CityController extends Controller
{
    protected $cityInterface;

    public function __construct(CityInterface $cityInterface)
    {
        $this->cityInterface = $cityInterface;
    }

    public function index()
    {
        return $this->cityInterface->getAllCities();
    }

    public function store(CityRequest $request)
    {
        return $this->cityInterface->storeCity($request);
    }

    public function show(City $city)
    {
        return $this->cityInterface->getSingleCity($city);
    }

    public function update(CityRequest $request, City $city)
    {
        return $this->cityInterface->storeCity($request, $city);
    }

    public function destroy(City $city)
    {
        return $this->cityInterface->deleteCity($city);
    }
}
