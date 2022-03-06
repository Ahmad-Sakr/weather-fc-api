<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\RecordRequest;
use App\Interfaces\v1\RecordInterface;
use App\Models\City;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class RecordController extends Controller
{
    protected $recordInterface;

    public function __construct(RecordInterface $recordInterface)
    {
        $this->recordInterface = $recordInterface;
    }

    public function index()
    {
        //Check for Date query parameter
        $date_query = request()->input('date', null);
        if(!$date_query) {
            throw new BadRequestException('Date parameter is required.');
        }
        $date = date_format(date_create_from_format('Ymd', $date_query), 'Y-m-d');

        //Check for city query parameter (Optional)
        $city_query = request()->input('city', null);
        $city = null;
        if($city_query) {
            $city = City::query()->where('name', $city_query)->first();
            if(!$city) {
                throw new ModelNotFoundException("City '$city_query' does not exist.");
            }
        }

        return $this->recordInterface->forecast($date, $city);
    }

    public function store(RecordRequest $request)
    {
        return $this->recordInterface->storeRecord($request);
    }
}
