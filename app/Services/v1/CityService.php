<?php


namespace App\Services\v1;


use App\Http\Requests\v1\CityRequest;
use App\Http\Resources\v1\CityResource;
use App\Interfaces\v1\CityInterface;
use App\Models\City;
use Exception;
use App\Traits\ApiResponder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CityService implements CityInterface
{
    use ApiResponder;

    public function getAllCities()
    {
        return $this->success(CityResource::collection(City::all()),"List of Cities", Response::HTTP_OK);
    }

    public function getSingleCity(City $city)
    {
        return $this->success(new CityResource($city));
    }

    public function storeCity(CityRequest $request, City $city = null)
    {
        DB::beginTransaction();
        try {
            $create = ($city === null);
            $data = $request->validated();

            if($create) {
                $city = City::query()->create($data);
            }
            else {
                $city->update($data);
            }

            DB::commit();
            return $this->success(new CityResource($city),
                $create ? 'Successfully Created' : 'Successfully Updated',
                $create ? Response::HTTP_CREATED : Response::HTTP_OK);
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCity(City $city)
    {
        DB::beginTransaction();
        try {
            $city->delete();

            DB::commit();
            return $this->success([],'Successfully Deleted', Response::HTTP_OK);
        } catch(Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
