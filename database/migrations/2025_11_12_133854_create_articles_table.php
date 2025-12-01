<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->mediumText('content');
            
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            
            // RAG Pipeline Fields
            $table->boolean('needs_sync')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            
            $table->timestamps();
        });

        // Add Full-Text Index for RAG and Search (MySQL syntax)
        DB::statement('ALTER TABLE articles ADD FULLTEXT fulltext_index(title, content)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
