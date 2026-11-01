<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'title_ur',
        'slug',
        'category',
        'featured_image',
        'summary',
        'summary_ur',
        'content',
        'content_ur',
        'tags',
        'read_time',
        'views_count',
        'is_featured',
        'status',
    ];

    /** Main fields English hain; purani post jis ka English abhi nahi likha, un mein Urdu hi rehti hai. */
    public function hasEnglish(): bool
    {
        return ! preg_match('/\p{Arabic}/u', (string) $this->title);
    }

    public function hasUrdu(): bool
    {
        return trim((string) $this->title_ur) !== '' && trim(strip_tags((string) $this->content_ur)) !== '';
    }

    /** Is waqt kaun si zaban dikh rahi hai: 'en' ya 'ur'. */
    public function displayLang(): string
    {
        if (! $this->hasUrdu()) {
            return $this->hasEnglish() ? 'en' : 'ur';
        }

        return app()->getLocale() === 'ur' || ! $this->hasEnglish() ? 'ur' : 'en';
    }

    private function displayField(string $field): ?string
    {
        return $this->displayLang() === 'ur' && $this->hasUrdu() ? $this->{$field . '_ur'} : $this->{$field};
    }

    public function getDisplayTitleAttribute(): ?string
    {
        return $this->displayField('title');
    }

    public function getDisplaySummaryAttribute(): ?string
    {
        return $this->displayField('summary');
    }

    public function getDisplayContentAttribute(): ?string
    {
        return $this->displayField('content');
    }

    /** Har zaban ka apna URL, taa-ke Google dono versions index kar sake. */
    public function languageUrl(string $lang): string
    {
        return route('blog.show', $lang === 'ur' ? ['slug' => $this->slug, 'lang' => 'ur'] : ['slug' => $this->slug]);
    }

    /** Mojooda zaban mein article ka link. */
    public function displayUrl(): string
    {
        return $this->languageUrl(app()->getLocale() === 'ur' && $this->hasUrdu() ? 'ur' : 'en');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(PostReview::class, 'post_id')->where('is_approved', true);
    }

}