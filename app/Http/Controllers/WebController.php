<?php

namespace App\Http\Controllers;

use App\Models\AIModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebController extends Controller
{
    public function Dashboard()
    {
        $user = auth()->user();
        $user->load('models','followedModels','dataSets');
        $public_models = AIModel::where('is_public', true)->get();
        $public_models->load('author');
        foreach ($public_models as $model){
            $model->load('followers');
        }
        return Inertia::render('Dashboard', [
            'user' => $user,
            'public_models' => $public_models,
        ]);
    }
}
