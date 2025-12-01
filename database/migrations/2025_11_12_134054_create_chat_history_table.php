<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_history', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->text('user_question');
            $table->text('ai_response');
            $table->json('retrieved_article_ids')->nullable(); // For storing sources
            $table->enum('user_feedback', ['good', 'bad', 'none'])->default('none');
            $table->mediumText('retrieved_context')->nullable(); // For debugging
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_history');
    }
};
