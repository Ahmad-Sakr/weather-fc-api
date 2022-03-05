<?php

namespace Tests\Unit;

use App\Http\Resources\v1\CityResource;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CityTest extends TestCase
{
    Use RefreshDatabase, WithFaker;

    protected $connectionsToTransact = ['testing'];

    public function test_can_list_cities()
    {
        //Clear Table
        $this->truncate();

        //Factory
        $cities = CityResource::collection(City::factory(5)->create())->toArray(null);

        //Get Response
        $response = $this->get(route('cities.index'));

        //Test Response Status
        $response->assertStatus(Response::HTTP_OK);

        //Test Response Data JSON
        $response_cities = $response->json()['data'];
        $this->assertEquals($cities, $response_cities);

        //Test JSON Structure
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [ 'id', 'name' ],
            ]
        ]);
    }

    public function test_can_create_city()
    {
        //Clear Table
        $this->truncate();

        //Post
        $response = $this->json('POST', route('cities.store'), [
            'name' => $this->faker->word,
        ]);

        //Test Response Status
        $response->assertStatus(Response::HTTP_CREATED);

        //Check New City Created
        $this->assertDatabaseCount('cities', 1);
    }

    public function test_name_is_required_for_city()
    {
        //Clear Table
        $this->truncate();

        //Post
        $response = $this->json('POST', route('cities.store'), [
            'name' => '',
        ]);

        //Test Response Status
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        //Test Response Status
        $response->assertJsonValidationErrors('name', 'data.errors');

        //Check City Not Created
        $this->assertDatabaseCount('cities', 0);
    }

    public function test_can_update_city()
    {
        //Clear Table
        $this->truncate();

        //Factory
        $city = City::factory()->create();

        //Update City
        $response = $this->json('PATCH', route('cities.update', ['city' => $city]), [
            'name' => 'New City',
        ]);

        //Test Response Status
        $response->assertStatus(Response::HTTP_OK);

        //Check New User Created
        $new_city = City::query()->first();
        $this->assertEquals('New City', $new_city->name);
    }

    public function test_can_delete_city()
    {
        //Clear Table
        $this->truncate();

        //Factory
        $city = City::factory()->create();

        //Delete City
        $response = $this->json('DELETE', route('cities.destroy', ['city' => $city]));

        //Test Response Status
        $response->assertStatus(Response::HTTP_OK);

        //Check Database Table Count
        $this->assertDatabaseCount('cities', 0);
    }

    private function truncate()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        City::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
