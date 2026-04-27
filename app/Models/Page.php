<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'key',
        'content',
        'html',
        'css',
        'gjs_data',
        'action',
        'published',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'action' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Page $page) {
            // Auto-generate slug from title if not set
            if (empty($page->slug)) {
                $base  = Str::slug($page->title);
                $count = static::where('slug', 'LIKE', $base . '%')
                    ->where('id', '!=', $page->id ?? 0)
                    ->count();
                $page->slug = $count > 0 ? $base . '-' . ($count + 1) : $base;
            }
        });
    }

    /**
     * System pages that cannot be deleted.
     */
    public const SYSTEM_KEYS = [
        'home'         => 'Home Page (/)',
        'demo-landing' => 'Demo Landing (/demo)',
    ];

    public function isSystemPage(): bool
    {
        return ! is_null($this->key);
    }
}
