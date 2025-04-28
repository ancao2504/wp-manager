<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_id',
        'url',
        'page_title',
        'meta_description',
        'keyword',
        'score',
        'issues',
        'recommendations',
        'last_analyzed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issues' => 'array',
        'recommendations' => 'array',
        'last_analyzed_at' => 'datetime',
    ];

    /**
     * Get the site that owns the SEO analysis.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
