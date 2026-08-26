<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_posts', function (Blueprint $table) {
            $table->string('document_path')->nullable()->after('image_alt');
            $table->string('document_name')->nullable()->after('document_path');
            $table->string('document_label', 160)->nullable()->after('document_name');
            $table->unsignedBigInteger('document_size')->nullable()->after('document_label');
        });
    }

    public function down(): void
    {
        Schema::table('news_posts', function (Blueprint $table) {
            $table->dropColumn([
                'document_path',
                'document_name',
                'document_label',
                'document_size',
            ]);
        });
    }
};
