<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WebController extends Controller
{
    public function Dashboard()
    {
        $user = auth()->user();
        $user->load('models','followedModels');
        return Inertia::render('Dashboard', [
            'user' => $user,
        ]);
    }
}
