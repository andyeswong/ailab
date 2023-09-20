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
        Schema::create('model_comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->longText('comment');
            // relation with user as owner
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            // relation with AI model
            $table->unsignedBigInteger('a_i_model_id');
            $table->foreign('a_i_model_id')->references('id')->on('a_i_models');
            //realtion with parent comment
            $table->unsignedBigInteger('parent_comment_id')->nullable();
            $table->foreign('parent_comment_id')->references('id')->on('model_comments');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_comments');
    }
};
