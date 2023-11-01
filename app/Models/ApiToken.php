<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;
    protected $table = 'api_tokens';

    protected $fillable = [
        'token',
        'models',
        'datasets',
        'is_active',
        'expires_at',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function models()
    {
        $json = $this->models;
        if(!$json){
            return [];
        }
        return json_decode($json);
    }

    public function addModelToken($model)
    {
        $token = $model->model_token;
        $models = $this->models();
        if(!$models){
            $models = [];
        }
        if(!in_array($token, $models)){
            array_push($models, $token);
        }
        $this->models = json_encode($models);
        $this->save();
    }

    public function removeModelToken($model)
    {
        $token = $model->model_token;
        $models = $this->models();
        if(!$models){
            $models = [];
        }
        if(in_array($token, $models)){
            $index = array_search($token, $models);
            unset($models[$index]);
        }
        $this->models = json_encode($models);
        $this->save();
    }

}
