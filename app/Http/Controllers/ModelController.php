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
        // inertia render
        return Inertia::render('Models/Index', [
            'user' => $user,
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
       return Inertia::render('Models/Show', [
            'model' => $model,
            'user' => $user,
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
        $model->delete();

        return redirect()->route('models.index')
            ->with('success', 'Model deleted successfully');
    }


}
