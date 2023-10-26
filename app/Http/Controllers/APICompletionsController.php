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
