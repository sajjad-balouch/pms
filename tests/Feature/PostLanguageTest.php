<?php

namespace Tests\Feature;

use App\Models\Post;
use Tests\TestCase;

class PostLanguageTest extends TestCase
{
    private function both(): Post
    {
        return new Post(['slug' => 'demo', 'title' => 'English title', 'content' => '<p>English</p>', 'title_ur' => 'اردو عنوان', 'content_ur' => '<p>اردو</p>']);
    }

    public function test_english_is_the_default_and_urdu_follows_the_locale(): void
    {
        $post = $this->both();

        app()->setLocale('en');
        $this->assertSame('English title', $post->display_title);
        $this->assertStringEndsWith('/blog/demo', $post->displayUrl());

        app()->setLocale('ur');
        $this->assertSame('اردو عنوان', $post->display_title);
        $this->assertStringEndsWith('/blog/demo?lang=ur', $post->displayUrl());
    }

    public function test_old_urdu_only_post_still_shows_urdu_in_english_mode(): void
    {
        $post = new Post(['slug' => 'old', 'title' => 'اردو', 'content' => '<p>اردو</p>', 'title_ur' => 'اردو', 'content_ur' => '<p>اردو</p>']);
        app()->setLocale('en');

        $this->assertFalse($post->hasEnglish());
        $this->assertSame('ur', $post->displayLang());
        $this->assertStringEndsWith('/blog/old', $post->displayUrl());
    }

    public function test_old_post_translations_are_complete_english(): void
    {
        $posts = \Database\Seeders\OldPostsEnglishSeeder::posts();
        $this->assertCount(12, $posts);

        foreach ($posts as $slug => $post) {
            $this->assertDoesNotMatchRegularExpression('/\p{Arabic}/u', $post['title'] . $post['summary'] . $post['content'], $slug);
            $this->assertLessThanOrEqual(160, mb_strlen($post['summary']), $slug);
            $this->assertStringContainsString('<h2>', $post['content'], $slug);
        }
    }

    public function test_english_only_post_falls_back_in_urdu_mode(): void
    {
        $post = new Post(['slug' => 'en', 'title' => 'Only English', 'content' => '<p>x</p>']);
        app()->setLocale('ur');

        $this->assertFalse($post->hasUrdu());
        $this->assertSame('en', $post->displayLang());
        $this->assertSame('Only English', $post->display_title);
    }
}
