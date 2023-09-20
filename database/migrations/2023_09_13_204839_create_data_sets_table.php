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
        Schema::create('data_sets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('data_set_name');
            $table->longText('data_set_description');
            $table->string('data_set_path');
            // relation with user as owner
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            // public or private
            $table->boolean('is_public')->default(false);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_sets');
    }
};
