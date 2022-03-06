<?php

namespace Tests\Unit;

use App\Events\PullData;
use App\Jobs\PullDataFromVendorAPIJob;
use App\Models\City;
use App\Models\Detail;
use App\Models\Record;
use Database\Seeders\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PullDataTest extends TestCase
{
    Use RefreshDatabase, WithFaker;

    protected $connectionsToTransact = ['testing'];

    public function test_pull_data_from_vendor_api_job_is_dispatched()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Expect Job To Be Dispatched
        $this->expectsJobs(PullDataFromVendorAPIJob::class);

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');
    }

    public function test_can_pull_data_from_vendor_api()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Check Records Table
        foreach (City::all() as $city) {
            $this->assertDatabaseHas('records', [
                'city_id' => $city->id,
            ]);
        }
    }

    public function test_can_user_pull_data_for_specific_date()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Get Data API
        $date = date_format(now(), 'Ymd');
        $url = route('weather.forecast', ['date' => $date]);
        $response = $this->get($url);

        //Test Response Status
        $response->assertStatus(Response::HTTP_OK);

        //Test JSON Structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [ 'date', 'city', 'hourly' ],
            ]
        ]);
    }

    public function test_date_is_required_while_pulling_data()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Get Data API
        $url = route('weather.forecast');
        $response = $this->get($url);

        //Test Response Status
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_data_is_not_available_for_some_dates()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Get Data API
        $date = '20500101';
        $url = route('weather.forecast', ['date' => $date]);
        $response = $this->get($url);

        //Test Response Status
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_city_not_exists_while_pulling_data()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Get Data API
        $date = date_format(now(), 'Ymd');
        $city = 'XXX';
        $url = route('weather.forecast', ['date' => $date, 'city' => $city]);
        $response = $this->get($url);

        //Test Response Status
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_pull_data_event_is_dispatched()
    {
        //Clear Table
        $this->truncate();

        //Seed Cities
        $this->seedCities();

        //Dispatch Job
        Artisan::call('weather:pull');

        //Run Queued Job
        Artisan::call('queue:work --queue=pulling --stop-when-empty');

        //Expect Event
        $this->expectsEvents(PullData::class);

        //Get Data API
        $date = date_format(now(), 'Ymd');
        $url = route('weather.forecast', ['date' => $date]);
        $this->get($url);
    }

    private function truncate()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Detail::query()->truncate();
        Record::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedCities()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->seed(CitySeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
