<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'api_key',
        'jwt_secret',
        'jwt_format',
        'status',
        'wp_version',
        'admin_email',
        'last_sync',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_sync' => 'datetime',
    ];

    /**
     * Get all posts for the site.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all products for the site.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all SEO analyses for the site.
     */
    public function seoAnalyses(): HasMany
    {
        return $this->hasMany(SeoAnalysis::class);
    }
}
