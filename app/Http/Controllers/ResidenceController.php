<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Http\Resources\ResidenceResource;
use App\Models\Number;

class ResidenceController extends Controller
{
    public function __invoke(ApiRequest $request)
    {
        $data = json_decode($request->getContent(), true);

        $postalcode = $data['postalcode'];
        $number = $data['number'];
        $addition = $data['addition'] ?? null;

        $result = Number::query()
            ->where('postcode', $postalcode)
            ->where('nummer', $number);

        if ($addition) {
            $result->whereRaw('LOWER(huisletter) = ?', strtolower($addition));
        }

        if (empty($result->first())) {
            return response()->json(["message" => "No result found"], 404);
        }

        return new ResidenceResource($result->first());
    }
}
