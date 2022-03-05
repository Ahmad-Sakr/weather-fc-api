<?php


namespace App\Services\v1;


use App\Interfaces\v1\RecordInterface;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PullNewDataService
{
    protected $recordInterface;

    public function __construct(RecordInterface $recordInterface)
    {
        $this->recordInterface = $recordInterface;
    }

    public function pull()
    {
        //Configure 3rd Party Vendor API URL
        $vendor_api_url = config('weather.vendor_api_path');
        $vendor_api_key = config('weather.vendor_api_key');

        //Get Names of All Available Cities
        $cities = City::all();

        //Convert Current Date to Unix Format
//        $date = \Carbon\Carbon::now();
//        $date_unix = $date->timestamp;

        //Configure Rules To Filter Data of API Request
        $rules = $this->getResponseRules();

        foreach ($cities as $city) {
            $data_to_post = [];

            $response = Http::get($vendor_api_url, [
                'q' => $city->name,
                'cnt' => 40,
                'appid' => $vendor_api_key,
            ]);
            $original_data = $response->json();
            $validator = Validator::make($original_data, $rules);
            $validated_data = $validator->validated();

            foreach ($validated_data['list'] as $detail) {
                $date = Str::substr($detail['dt_txt'],0,10);

                if(!array_key_exists($date, $data_to_post)) {
                    //Prepare New Data Array
                    $data_to_post[$date]['rc_date'] = $date;
                    $data_to_post[$date]['city'] = $city->name;
                    $data_to_post[$date]['details'] = [];
                }

                $detail_array=[];
                $time = date_format(date_create_from_format('Y-m-d H:i:s', $detail['dt_txt']), 'H');
                $detail_array['hour'] = $time;
                $detail_array['cloud'] = $detail['weather'][0]['main'];
                $detail_array['temp'] = $detail['main']['temp'];
                $detail_array['min_temp'] = $detail['main']['temp_min'];
                $detail_array['max_temp'] = $detail['main']['temp_max'];
                $detail_array['pressure'] = $detail['main']['pressure'];
                $detail_array['sea_level'] = $detail['main']['sea_level'];
                $detail_array['humidity'] = $detail['main']['humidity'];
                $detail_array['wind_speed'] = $detail['wind']['speed'];

                array_push($data_to_post[$date]['details'], $detail_array);
            }

            //Post Data to Our API to Store New Record
            foreach (array_values($data_to_post) as $record) {
                $this->recordInterface->storeRecordInDatabase($record, $city);
            }
        }
    }

    private function getResponseRules() {
        return [
            'list.*.dt'  => '',
            'list.*.dt_txt' => '',
            'list.*.weather.0.main' => '',
            'list.*.main.temp' => '',
            'list.*.main.temp_min' => '',
            'list.*.main.temp_max' => '',
            'list.*.main.pressure' => '',
            'list.*.main.sea_level' => '',
            'list.*.main.humidity' => '',
            'list.*.wind.speed' => ''
        ];
    }
}
