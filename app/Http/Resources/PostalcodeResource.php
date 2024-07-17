<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostalcodeResource extends JsonResource
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
            'city' => $this->when($this->city()->exists(), $this->city->naam),
            'street' => $this->when($this->publicSpace()->exists(), $this->publicSpace->naam)
        ];
    }
}
