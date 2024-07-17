<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\City;
use App\Models\Number;
use App\Models\PublicSpace;

class PostalcodeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_api_returns_a_specific_postalcode(): void
    {
        /** @todo - factory relation creation */

        Number::factory()->create([
            'postcode' => '9901AA',
            'identificatie' => 3200000133985,
            'nummer' => 3,
            'ligtAan' => 3300000117203
        ]);

        PublicSpace::factory()->create([
            'naam' => 'Snelgersmastraat',
            'identificatie' => 3300000117203,
            'type' => "Weg",
            'status' => "Naamgeving uitgegeven",
            'geconstateerd' => false,
            'documentdatum' => '2010-07-20 00:00:00',
            'documentnummer' => 'FB 2010/OR0001',
            'ligtIn' => 3386
        ]);

        City::factory()->create([
            'naam' => 'Appingedam',
            'identificatie' => 3386
        ]);

        $response = $this->postJson('/api/postalcode', ['postalcode' => '9901AA', 'number' => 3]);

        $postalcode_response = $response->decodeResponseJson()->json('data');

        $this->assertJsonStringEqualsJsonString('{"postalcode":"9901AA","number":3,"city":"Appingedam","street":"Snelgersmastraat"}', json_encode($postalcode_response));
    }

    public function test_the_api_returns_validation_error_with_empty_request(): void
    {
        $response = $this->postJson('/api/postalcode');

        $postalcode_response = $response->decodeResponseJson()->json('data');

        $error = [
            "number" => [
                "The number field is required."
            ],
            "postalcode" =>  [
                "The postalcode field is required."
            ]
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($error), json_encode($postalcode_response));
    }

    public function test_the_api_returns_additional_field_when_requested(): void
    {
        Number::factory()->create([
            'postcode' => '9901AA',
            'identificatie' => 3200000133985,
            'nummer' => 3,
            'huisletter' => "A",
            'ligtAan' => 3300000117203
        ]);

        PublicSpace::factory()->create([
            'naam' => 'Snelgersmastraat',
            'identificatie' => 3300000117203,
            'type' => "Weg",
            'status' => "Naamgeving uitgegeven",
            'geconstateerd' => false,
            'documentdatum' => '2010-07-20 00:00:00',
            'documentnummer' => 'FB 2010/OR0001',
            'ligtIn' => 3386
        ]);

        City::factory()->create([
            'naam' => 'Appingedam',
            'identificatie' => 3386
        ]);

        $response = $this->postJson('/api/postalcode', ['postalcode' => '9901AA', 'number' => 3, "addition" => "a"]);

        $postalcode_response = $response->decodeResponseJson()->json('data');

        $answer = [
            "postalcode" => "9901AA",
            "number" => 3,
            "addition" => "A",
            "city" => "Appingedam",
            "street" => "Snelgersmastraat"
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($answer), json_encode($postalcode_response));
    }
}
