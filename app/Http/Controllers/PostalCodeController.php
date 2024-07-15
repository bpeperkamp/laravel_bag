<?php

namespace App\Http\Controllers;

use App\Models\Number;
use Illuminate\Http\Request;

class PostalCodeController extends Controller
{
    public function __invoke(Request $request)
    {
        // This code needs some cleaning

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return response()->json([
                'error' => 'Please provide a postalcode and a number (and optional addition)',
            ]);
        }

        $postalcode = $data['postalcode'] ?? $data['postcode'];
        $number = $data['number'] ?? $data['nummer'];
        $addition = $data['addition'] ?? $data['toevoeging'] ?? null;

        $result = Number::query();
        $result->where('postcode', $postalcode)
            ->where('nummer', $number);

        if ($addition) {
            $result->whereRaw('LOWER(huisletter) = ?', strtolower($addition));
        }

        $result = $result->first();

        if ($result) {
            if ($addition) {
                return response()->json([
                    'postalcode' => $result->postcode ?? null,
                    'nummer' => $result->nummer ?? null,
                    'addition' => $result->huisletter ?? null,
                    'street' => $result->publicSpace->naam ?? null,
                    'city' => $result->city->naam ?? null,
                ]);
            } else {
                return response()->json([
                    'postalcode' => $result->postcode ?? null,
                    'nummer' => $result->nummer ?? null,
                    'street' => $result->publicSpace->naam ?? null,
                    'city' => $result->city->naam ?? null,
                ]);
            }

        } else {
            return response()->json([
                'error' => 'No result found',
            ]);
        }
    }
}
