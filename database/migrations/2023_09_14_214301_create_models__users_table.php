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
        Schema::create('model_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('model_id')->references('id')->on('a_i_models');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models__users');
    }
};
