<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia; 
use App\Models\DataSet;

class DatasetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('datasets');
        
        // inertia render
        return Inertia::render('Dataset/Index', [
            'user' => $user,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        return Inertia::render('Dataset/Create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_set_name' => 'required',
            'user_id' => 'required',
            'file' => 'required',
            'data_set_description' => 'required',
        ]);

        if($request->hasFile('file')){
            // store file in storage/app/public/datasets filename
            $dataset_path = $request->file('file')->store('datasets');
            $request->merge(['data_set_path' => $dataset_path]);
        }


        DataSet::create($request->all());

        return redirect()->route('datasets.index')
            ->with('success', 'Dataset created successfully.');
    }

    public function show(DataSet $dataset)
    {
        return view('datasets.show', compact('dataset'));
    }

    public function edit(DataSet $dataset)
    {
        return view('datasets.edit', compact('dataset'));
    }

    public function update(Request $request, DataSet $dataset)
    {
        $request->validate([
            'data_set_name' => 'required',
            'data_set_description' => 'required',
            'user_id' => 'required',
            'is_public' => 'required',
        ]);

        if($request->hasFile('dataset_path')){
            $dataset_path = $request->file('dataset_path')->store('datasets');
            $request->merge(['dataset_path' => $dataset_path]);
        }else{
            $request->merge(['dataset_path' => $dataset->dataset_path]);
        }


        $dataset->update($request->all());

        return redirect()->route('datasets.index')
            ->with('success', 'Dataset updated successfully');
    }

    public function destroy(DataSet $dataset)
    {
        $dataset->delete();

        return redirect()->route('datasets.index')
            ->with('success', 'Dataset deleted successfully');
    }

    public function download(DataSet $dataset)
    {
        return Storage::download($dataset->dataset_path);
    }


}
