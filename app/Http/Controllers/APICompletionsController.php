<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APICompletionsController extends Controller
{
    public function getCompletionByModel($model_token, Request $request)
    {
        $prompt = $request->prompt;
        $max_tokens = $request->input('max_tokens');
        $temperature = $request->input('temperature');

        $user = auth()->user();
        $default_api_token = $user->apiTokens()->first();

        if(!$default_api_token){
            return response()->json([
                'message' => 'API Token not found'
            ], 404);
        }


        $form_params = [
            'prompt' => $prompt,
            'max_tokens' => $max_tokens,
            'temperature' => $temperature,
            'model_token' => $model_token
        ];



        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('PYTHON_AI_ENGINE_HOST').'/api/v1/prompt', [
            'form_params' => $form_params
        ]);
        $body = $response->getBody();
        $json = json_decode($body);

        $completion = $json->completion;
        return response()->json(['completion' => $completion]);
    }


    public function getCompletionByModelPublic($model_token, Request $request)
    {

        $prompt = $request->prompt ?? '';
        $max_tokens = $request->max_tokens ?? 64;
        $temperature = $request->temperature ?? 0.7;
        $api_token = $request->api_token ?? '';


        $api_token_model = \App\Models\ApiToken::where('token', $api_token)->first();
        if(!$api_token_model){
            return response()->json([
                'message' => 'API Token not found'
            ], 404);
        }

        $user = $api_token_model->user;

        $model = \App\Models\AIModel::where('model_token', $model_token)->first();
        if(!$model){
            return response()->json([
                'message' => 'Model not found'
            ], 404);
        }

//        if model.user_id != user.id
        if(!$user->OwnerOrFollowed($model)){
            return response()->json([
                'message' => 'Model is not owned or followed by user'
            ], 403);
        }


        $api_token_authorized_models = $api_token_model->models;
        $api_token_authorized_models = json_decode($api_token_authorized_models);

//        if model.token not in api_token_authorized_models
        if(!in_array($model_token, $api_token_authorized_models)){
            return response()->json([
                'message' => 'Model is not authorized for this API Token'
            ], 403);
        }

        $form_params = [
            'prompt' => $prompt,
            'max_tokens' => $max_tokens,
            'temperature' => $temperature,
            'model_token' => $model_token
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', env('PYTHON_AI_ENGINE_HOST').'/api/v1/prompt', [
            'form_params' => $form_params
        ]);
        $body = $response->getBody();
        $json = json_decode($body);

        $completion = $json->completion;
        return response()->json(['completion' => $completion]);
    }
}
