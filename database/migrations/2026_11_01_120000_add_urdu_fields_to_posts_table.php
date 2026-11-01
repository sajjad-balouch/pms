<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title_ur')->nullable()->after('title');
            $table->text('summary_ur')->nullable()->after('summary');
            $table->longText('content_ur')->nullable()->after('content');
        });

        // Purani Urdu posts: text Urdu fields mein copy. Main fields waise hi rehte hain
        // (kuch delete nahi hota); English likhne par woh badal jate hain.
        DB::table('posts')->orderBy('id')->chunkById(100, function ($posts) {
            foreach ($posts as $post) {
                if (preg_match('/\p{Arabic}/u', (string) $post->title)) {
                    DB::table('posts')->where('id', $post->id)->update([
                        'title_ur' => $post->title,
                        'summary_ur' => $post->summary,
                        'content_ur' => $post->content,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title_ur', 'summary_ur', 'content_ur']);
        });
    }
};
