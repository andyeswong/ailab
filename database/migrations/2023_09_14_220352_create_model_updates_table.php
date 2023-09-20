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
        Schema::create('model_updates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model_update_name');
            $table->longText('model_update_content');
            // pinned or not
            $table->boolean('is_pinned')->default(false);
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
        Schema::dropIfExists('model_updates');
    }
};
