<?php

namespace App\Console\Commands;

use App\Jobs\PullDataFromVendorAPIJob;
use App\Models\City;
use Illuminate\Console\Command;

class PullDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull the weather forecast for the next 5 days in all registered cities from vendor API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Get Names of All Available Cities
        $cities = City::all();

        foreach ($cities as $city) {
            //Pull data for city
            dispatch(new PullDataFromVendorAPIJob($city));
        }

        $this->info('Weather forecast data has been pulled successfully');
        return true;
    }
}
