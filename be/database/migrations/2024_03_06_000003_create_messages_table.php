<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_role'); // admin, client, technician
            $table->text('content')->nullable();
            $table->string('type')->default('text'); // text, image
            $table->timestamp('recalled_at')->nullable(); // Khi bị thu hồi
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
