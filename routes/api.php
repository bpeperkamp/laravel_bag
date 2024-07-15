<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PostalCodeController;
use App\Http\Controllers\ResidenceController;

use App\Models\City;
use App\Http\Resources\CityResource;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/cities/{id}', function (string $id) {
    return new CityResource(City::where('identificatie', $id)->first());
});

Route::get('/cities', function () {
    return CityResource::collection(City::paginate(100));
});

Route::post('/postalcode', PostalCodeController::class);
Route::post('/residence', ResidenceController::class);
