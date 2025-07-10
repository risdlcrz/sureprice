<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $projects = \App\Models\Project::where('project_manager_id', $user->id)->paginate(10);
        return view('manager.dashboard', compact('projects'));
    }
} 