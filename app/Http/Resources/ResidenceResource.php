<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidenceResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'postalcode' => $this->postcode,
            'number' => $this->nummer,
            'addition' => $this->when($request->input('addition'), $this->huisletter),
            'city' => $this->when($this->city->exists(), $this->city->naam),
            'street' => $this->when($this->publicSpace->exists(), $this->publicSpace->naam),
            'residence' => $this->when($this->residence->exists(), function () {
                return [
                    'status' => $this->residence->status,
                    'surface (m2)' => $this->residence->oppervlakte,
                    'purpose' => $this->residence->gebruiksdoel,
                    'document_date' => $this->residence->documentdatum,
                    'document_number' => $this->residence->documentnummer,
                ];
            }),
            'premise' => $this->when($this->residence->premise()->exists(), function () {
                return [
                    'status' => $this->residence->premise->status ?? null,
                    'build_year' => $this->residence->premise->oorspronkelijkBouwjaar ?? null,
                ];
            }),
        ];
    }
}
