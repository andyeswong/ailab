<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use Illuminate\Http\Request;

class APITokensController extends Controller
{
    public function store(Request $request)
    {

//        validate request
        $request->validate([
            'name' => 'required',
            'model_tokens' => 'required',
        ]);

        $user = auth()->user();
        $user->load('apiTokens');
        $model_tokens = $request->model_tokens;
        $api_token = \Str::random(35);
        $new_token = new ApiToken();
        $new_token->name = $request->name;
        $new_token->token = $api_token;
        $new_token->models = json_encode($model_tokens);
        $new_token->user_id = $user->id;
        $new_token->save();

        $res_json = [
            'token' => $api_token,
            'message' => 'API Token created successfully',
        ];

        return response()->json($res_json, 200);

    }
}
