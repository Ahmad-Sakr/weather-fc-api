<?php

namespace App\Jobs;

use App\Interfaces\v1\RecordInterface;
use App\Models\City;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PullDataFromVendorAPIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var City
     */
    protected $city;

    /**
     * Create a new job instance.
     *
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->onQueue('pulling');
        $this->city = $city;
    }

    /**
     * Execute the job.
     *
     * @param RecordInterface $recordInterface
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle(RecordInterface $recordInterface)
    {
        //Get 3rd Party Vendor API URL
        $vendor_api_url = config('weather.vendor_api_path');
        $vendor_api_key = config('weather.vendor_api_key');

        $rules = [
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

        $data_to_post = [];

        $response = Http::get($vendor_api_url, [
            'q' => $this->city->name,
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
                $data_to_post[$date]['city'] = $this->city->name;
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

        //Store New Record
        foreach (array_values($data_to_post) as $record) {
            $recordInterface->storeRecordInDatabase($record, $this->city);
        }
    }
}
