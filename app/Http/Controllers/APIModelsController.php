<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use  \App\Models\AIModel;
use  \App\Models\DataSet;
use  \App\Models\User;

class APIModelsController extends Controller
{
    public function store(Request $request)
    {
        $params = $request->all()['params'];
        $model = new AIModel();
        $model->model_name = $params['model_name'];
        $model->model_description = $params['model_description'];

        $data_set = DataSet::find($params['data_set_id']);
        if(!$data_set){
            return response()->json([
                'message' => 'data set not found'
            ], 404);
        }

        $file_path = $data_set->toArray()['data_set_path'];

        $model->dataset()->associate($data_set);

        $client = new Client();
        // params on form-data epoch, batch_size, learning_rate and file

        $data_body = [
            'multipart' => [
                [
                    'name' => 'epoch',
                    'contents' => $params['epochs']
                ],
                [
                    'name' => 'batch_size',
                    'contents' => $params['batch_size']
                ],
                [
                    'name' => 'learning_rate',
                    'contents' => $params['learning_rate']
                ],
                [
                    'name' => 'file',
                    'contents' => $file_path
                ],
                [
                    'name' => 'user_id',
                    'contents' => $params['user_id']
                ],
                [
                    'name' => 'api_url',
                    'contents' => env('APP_URL')
                ]
            ]
        ];


        $metrics_array = [
            'epochs' => $params['epochs'],
            'batch_size' => $params['batch_size'],
            'learning_rate' => $params['learning_rate'],
        ];

        $model->model_hyperparameters = json_encode($metrics_array);

        $req = $client->post(env('PYTHON_AI_ENGINE_HOST').'/api/v1/train', $data_body);
        $res_body = json_decode($req->getBody()->getContents());

        $model->model_token = $res_body->model_token;
        $model->user_id = $params['user_id'];

        $model->status = 'training';

        $model->save();

        return response()->json([
            'message' => 'model training job created successfully',
            'data' => $model
        ], 200);




    }

    public function updateStatus($model_token, Request $request){
        $model = AIModel::where('model_token', $model_token)->first();
        if(!$model){
            return response()->json([
                'message' => 'model not found'
            ], 404);
        }

        $model->status = $request->all()['status'];
        $model->save();

        if(isset($request->all()['metrics']) && $request->all()['metrics'] != "[]"){
            $model->model_metrics = $request->all()['metrics'];
            $model->save();
        }

        return response()->json([
            'message' => 'model status updated successfully',
            'data' => $model
        ], 200);
    }

    public function getModelsByUser($user_id){
        $user = User::find($user_id);
        $user->load('models');
        $models = $user->models;
//        load data set
        foreach ($models as $model){
            $model->load('dataset');
        }
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }

        return response()->json([
            'message' => 'user models retrieved successfully',
            'data' => $user
        ], 200);
    }


}
