<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'price',
        'month',
        'year',
        'total'
    ];

    public $timestamps = true;

    /**
     * Get the series that owns the Sale
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }
}
