<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Residence extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sideAddresses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Number::class,
            ResidenceSideAddress::class,
            'residence_identificatie',
            'identificatie',
            'identificatie',
            'number_identificatie'
        );
    }

    public function address(): HasOne
    {
        return $this->hasOne(Number::class, 'identificatie', 'heeftAlsHoofdadres');
    }

    public function premise(): HasOne
    {
        return $this->hasOne(Premise::class, 'identificatie', 'maaktDeelUitVan');
    }
}
