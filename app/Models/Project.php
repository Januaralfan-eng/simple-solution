<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'client_name', 'project_url', 'github_url',
        'thumbnail', 'cover_image', 'gallery', 'tech_stack',
        'is_featured', 'is_published', 'sort_order',
        'views_count', 'likes_count', 'project_date', 'published_at',
    ];

    protected $casts = [
        'gallery'      => 'array',
        'tech_stack'   => 'array',
        'is_featured'  => 'boolean',
        'is_published' => 'boolean',
        'project_date' => 'date',
        'published_at' => 'datetime',
    ];

    // ─── Slug ────────────────────────────────────────────────────────────────

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_category', 'project_id', 'project_category_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ProjectLike::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
                     ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->gallery ?? [])
            ->map(fn ($path) => Storage::url($path))
            ->toArray();
    }
}
