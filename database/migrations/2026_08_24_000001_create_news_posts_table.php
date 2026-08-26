<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->text('summary');
            $table->longText('body')->nullable();
            $table->string('category', 80);
            $table->string('source', 120);
            $table->text('external_url')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_asset')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_posts');
    }
};
