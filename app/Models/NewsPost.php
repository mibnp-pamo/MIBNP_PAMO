<?php

namespace App\Models;

use App\Support\PublicSiteContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsPost extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const CATEGORIES = [
        'Advisory',
        'Event',
        'Conservation',
        'Community',
        'Partner update',
        'Field update',
    ];

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'category',
        'source',
        'external_url',
        'image_path',
        'image_asset',
        'image_alt',
        'document_path',
        'document_name',
        'document_label',
        'document_size',
        'status',
        'is_pinned',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'document_size' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopePublicOrder(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function isVisible(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->isPast()
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function publicUrl(): string
    {
        return $this->external_url ?: route('news.show', $this, false);
    }

    public function publicImage(): string
    {
        if ($this->image_path) {
            return asset('storage/'.ltrim($this->image_path, '/'));
        }

        return PublicSiteContent::optimizedAsset(
            $this->image_asset ?: 'bckgrndHome/hbg1.jpg',
        );
    }

    public function documentDisplayName(): string
    {
        return $this->document_label ?: ($this->document_name ?: 'Download document');
    }

    public function documentSizeLabel(): ?string
    {
        if (! $this->document_size) {
            return null;
        }

        if ($this->document_size >= 1048576) {
            return number_format($this->document_size / 1048576, 1).' MB';
        }

        return max(1, (int) ceil($this->document_size / 1024)).' KB';
    }

    public function toPublicItem(): array
    {
        return [
            'source' => $this->source,
            'tag' => $this->category,
            'status' => $this->published_at?->format('F j, Y'),
            'title' => $this->title,
            'body' => $this->summary,
            'image' => $this->publicImage(),
            'image_alt' => $this->image_alt ?: "Featured image for {$this->title}",
            'url' => $this->publicUrl(),
            'external' => filled($this->external_url),
            'cta' => $this->external_url ? 'Open source' : 'Read update',
            'document_url' => $this->document_path ? route('news.document', $this, false) : null,
            'document_name' => $this->documentDisplayName(),
            'document_size' => $this->documentSizeLabel(),
        ];
    }
}
