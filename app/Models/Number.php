<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Number extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function city(): HasOneThrough
    {
        return $this->hasOneThrough(
            City::class,
            PublicSpace::class,
            'identificatie',
            'identificatie',
            'ligtAan',
            'ligtIn'
        );
    }

    public function publicSpace(): HasOne
    {
        return $this->hasOne(PublicSpace::class, 'identificatie', 'ligtAan');
    }

    public function residence(): HasOne
    {
        return $this->hasOne(Residence::class, 'heeftAlshoofdadres', 'identificatie');
    }
}
