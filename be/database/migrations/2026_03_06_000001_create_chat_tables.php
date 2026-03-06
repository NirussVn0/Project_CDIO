<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('direct');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->nullOnDelete();
        });

        Schema::create('chat_conversation_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);

            $table->foreign('conversation_id')
                ->references('id')
                ->on('chat_conversations')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->string('type')->default('text');
            $table->text('content')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamp('recalled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('conversation_id')
                ->references('id')
                ->on('chat_conversations')
                ->cascadeOnDelete();

            $table->foreign('sender_id')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();
        });

        Schema::create('chat_message_deletes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['message_id', 'user_id']);

            $table->foreign('message_id')
                ->references('id')
                ->on('chat_messages')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_deletes');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversation_members');
        Schema::dropIfExists('chat_conversations');
    }
};
