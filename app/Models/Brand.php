<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public $timestamps = true;

    /**
     * Get all of the series for the Brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function series(): HasMany
    {
        return $this->hasMany(Series::class, 'brand_id', 'id');
    }
}
