<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AIModel;

class ModelController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('models');
        $models = $user->models()->get();
        $models->load('dataset');
        $followed_models = $user->followedModels()->get();
        $followed_models->load('author');

        $data_sets = $user->dataSets()->get();

        // inertia render
        return Inertia::render('Models/Index', [
            'user' => $user,
            'followed_models' => $followed_models,
            'own_models' => $models,
            'data_sets' => $data_sets,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $user->load('dataSets');


        return Inertia::render('Models/Create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'model_name' => 'required',
            'model_description' => 'required',
            'model_token' => 'required',
            'user_id' => 'required',
            'data_set_id' => 'required',
            'is_public' => 'required',
            'is_listed' => 'required',
        ]);

        AIModel::create($request->all());

        return redirect()->route('models.index')
            ->with('success', 'Model created successfully.');
    }

    public function show(AIModel $model)
    {
        $user = auth()->user();
        $model->load('author');
        $is_author = $model->is_owner($user->id);
        $is_followed = $user->followedModels()->where('model_id', $model->id)->count() > 0;

        if($is_author){
            $model->load('dataset');
            $dataset = $model->dataset;
        }else{
            $model->dataset = null;
            $dataset = ['data_set_name' => 'Private', 'data_set_description' => 'Private', 'data_set_path' => 'Private'];
        }

        if($user->tokens()->count() > 0) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('webToken')->plainTextToken;

       return Inertia::render('Models/Show', [
            'model' => $model,
            'token' => $token,
            'user' => $user,
            'is_author' => $is_author,
            'is_followed' => $is_followed,
            'dataset' => $dataset,
        ]);
    }

    public function edit(AIModel $model)
    {
        return view('models.edit', compact('model'));
    }

    public function update(Request $request, AIModel $model)
    {
        $request->validate([
            'model_name' => 'required',
            'model_description' => 'required',
            'model_token' => 'required',
            'user_id' => 'required',
            'data_set_id' => 'required',
            'is_public' => 'required',
            'is_listed' => 'required',
        ]);
        $model->update($request->all());

        return redirect()->route('models.index')
            ->with('success', 'Model updated successfully');
    }

    public function destroy(AIModel $model)
    {

        $user = auth()->user();
        $author = $model->is_owner($user->id);
        if(!$author){
            return redirect()->route('models.index')->with('error', 'You are not the author of this model');
        }
        $model->delete();

//        $client = new \GuzzleHttp\Client();
//        $response = $client->request('DELETE', env('PYTHON_AI_ENGINE_HOST').'/api/v1/model/'.$model->model_token, [
//        ]);


        return redirect()->route('models.index')
            ->with('success', 'Model deleted successfully');
    }


    public function makePublic(AIModel $model)
    {
        $model->is_public = true;
        $model->save();

        $author = $model->is_owner(auth()->user()->id);
        if(!$author){
            return redirect()->route('models.index')->with('error', 'You are not the author of this model');
        }


        return redirect()->route('models.index')
            ->with('success', 'Model made public successfully');
    }

    public function makePrivate(AIModel $model)
    {
        $model->is_public = false;
        $model->save();

        $author = $model->is_owner(auth()->user()->id);
        if(!$author){
            return redirect()->route('models.index')->with('error', 'You are not the author of this model');
        }

        return redirect()->route('models.index')
            ->with('success', 'Model made private successfully');
    }

    public function follow(AIModel $model)
    {
        $user = auth()->user();
        $user->followedModels()->attach($model->id);

        return redirect()->route('models.index')
            ->with('success', 'Model followed successfully');
    }

    public function unfollow(AIModel $model)
    {
        $user = auth()->user();
        $user->followedModels()->detach($model->id);

        return redirect()->route('models.index')
            ->with('success', 'Model unfollowed successfully');
    }



}
