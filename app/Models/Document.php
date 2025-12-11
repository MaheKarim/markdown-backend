<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id',
    ];

    protected $casts = [
        'content' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->slug)) {
                $document->slug = Str::slug($document->title) . '-' . Str::random(6);
            }
        });

        static::updating(function ($document) {
            if ($document->isDirty('title') && empty($document->slug)) {
                $document->slug = Str::slug($document->title) . '-' . Str::random(6);
            }
        });
    }
}
