<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** Record that the current user viewed a page (shown in the activity logs). */
    protected function logPageView(string $description, ?string $modelType = null, $modelId = null): void
    {
        \App\Models\Activity::create([
            'user_id' => auth()->id(),
            'action' => 'viewed',
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
        ]);
    }
}
