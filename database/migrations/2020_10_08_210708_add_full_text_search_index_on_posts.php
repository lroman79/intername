<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFullTextSearchIndexOnPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $sql = 'ALTER TABLE `posts` ADD FULLTEXT `posts_full_text_search_idx`(`title`, `body`);';
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $sql = 'ALTER TABLE `posts` DROP INDEX `posts_full_text_search_idx`;';
        DB::statement($sql);
    }
}
