<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AIModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Schema::create('a_i_models', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        //     $table->string('model_name');
        //     $table->longText('model_description');
        //     $table->string('model_token');
        //     // relation with user as owner
        //     $table->unsignedBigInteger('user_id');
        //     $table->foreign('user_id')->references('id')->on('users');
        //     // relation with data set
        //     $table->unsignedBigInteger('data_set_id');
        //     $table->foreign('data_set_id')->references('id')->on('data_sets');
        //     // Public, private or not listed
        //     $table->boolean('is_public')->default(false);
        //     $table->boolean('is_listed')->default(false);
        // });


    }
}
