<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\City;

class CitiesApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response_from_the_base_url(): void
    {
        $response = $this->get('/api/cities');

        $response->assertStatus(200);
    }

    public function test_the_api_responds_with_50_created_cities(): void
    {
        City::factory()->count(50)->create();

        $response = $this->get('/api/cities');
        $city_count = $response->decodeResponseJson()->json('data');

        $this->assertCount(50, $city_count);
    }

    public function test_the_api_returns_a_specific_city(): void
    {
        City::factory()->create([
            'naam' => 'Amsterdam',
            'identificatie' => 1000
        ]);

        $response = $this->get('/api/cities/1000');
        $city_response = $response->decodeResponseJson()->json('data');

        $this->assertJsonStringEqualsJsonString('{"identificatie":1000,"naam":"Amsterdam"}', json_encode($city_response));
    }
}
