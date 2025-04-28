<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'wc_id',
        'name',
        'description',
        'short_description',
        'price',
        'regular_price',
        'sale_price',
        'stock_status',
        'stock_quantity',
        'sku',
        'status',
    ];

    /**
     * Get the site that owns the product.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
