<?php

namespace Database\Seeders;

use App\Models\DataSet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create dataset
        // Schema::create('data_sets', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        //     $table->string('data_set_name');
        //     $table->longText('data_set_description');
        //     $table->string('dataset_path');
        //     // relation with user as owner
        //     $table->unsignedBigInteger('user_id');
        //     $table->foreign('user_id')->references('id')->on('users');
        //     // public or private
        //     $table->boolean('is_public')->default(false);
            
        // });

        // dataset path if windows
        // $dataset_path = storage_path('app\public\datasets\base.csv');
        // dataset path if linux
        // $dataset_path = storage_path('app/public/datasets/base.csv');

        if(PHP_OS_FAMILY == 'Windows'){
            $dataset_path = '\datasets\base.csv';
        }else{
            $dataset_path = '/datasets/base.csv';
        }

        DataSet::create([
            'data_set_name' => 'base',
            'data_set_description' => 'this is a base dataset',
            'data_set_path' =>$dataset_path,
            'user_id' => 1,
            'is_public' => true,
        ]);
    }
}
