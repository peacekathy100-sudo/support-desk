<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'search_tags',
        'video_url',
        'views',
        'is_featured',
        'created_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(SysUser::class, 'created_by', 'user_id');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
