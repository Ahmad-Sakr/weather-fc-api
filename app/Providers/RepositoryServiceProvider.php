<?php

namespace App\Providers;

use App\Interfaces\v1\CityInterface;
use App\Services\v1\CityService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(CityInterface::class,CityService::class);
    }
}
