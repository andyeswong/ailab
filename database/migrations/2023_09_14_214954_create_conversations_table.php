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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('conversation_name');
            $table->json('conversation_data');
            // relation with user as owner
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            // relation with AI model
            $table->unsignedBigInteger('a_i_model_id');
            $table->foreign('a_i_model_id')->references('id')->on('a_i_models');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
