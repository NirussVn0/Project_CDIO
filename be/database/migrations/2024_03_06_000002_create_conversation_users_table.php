<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->string('user_role'); // admin, client, technician
            $table->timestamps();

            // Index để query nhanh
            $table->index(['user_id', 'user_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_users');
    }
};
