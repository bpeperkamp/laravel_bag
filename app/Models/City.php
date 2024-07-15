<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function publicSpaces(): HasMany
    {
        return $this->hasMany(PublicSpace::class, 'ligtIn', 'identificatie');
    }
}
