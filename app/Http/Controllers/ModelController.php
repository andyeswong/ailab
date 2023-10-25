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

        $followed_models = $user->followedModels()->get();
        $followed_models->load('author');

        // inertia render
        return Inertia::render('Models/Index', [
            'user' => $user,
            'followed_models' => $followed_models,
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


       return Inertia::render('Models/Show', [
            'model' => $model,
            'user' => $user,
            'is_author' => $is_author,
            'is_followed' => $is_followed,
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
