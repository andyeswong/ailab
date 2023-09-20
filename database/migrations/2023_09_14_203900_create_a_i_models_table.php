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
        Schema::create('a_i_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model_name');
            $table->longText('model_description');
            $table->string('model_token');
            $table->string('status')->nullable();
            $table->json('model_metrics')->nullable();
            $table->json('model_hyperparameters')->nullable();
            // relation with user as owner
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            // relation with data set
            $table->unsignedBigInteger('data_set_id');
            $table->foreign('data_set_id')->references('id')->on('data_sets');
            // Public, private or not listed
            $table->boolean('is_public')->default(false);
            $table->boolean('is_listed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_i_models');
    }
};
