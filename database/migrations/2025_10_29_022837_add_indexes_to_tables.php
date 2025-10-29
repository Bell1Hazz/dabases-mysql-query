<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category_id');
            $table->index('date');
            $table->index('views');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('article_id');
            $table->index('user_id');
            $table->index('parent_id');
        });

        Schema::table('article_tag', function (Blueprint $table) {
            $table->index('article_id');
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'category_id', 'date', 'views']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['article_id', 'user_id', 'parent_id']);
        });

        Schema::table('article_tag', function (Blueprint $table) {
            $table->dropIndex(['article_id', 'tag_id']);
        });
    }
};