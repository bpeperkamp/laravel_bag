<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PublicSpace extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'identificatie', 'ligtIn');
    }
}
