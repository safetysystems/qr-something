<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ProjectTrackerController extends Controller
{
    public function __invoke(): Response
    {
        $features = config('project_tracker.features', []);
        $counts = collect($features)
            ->countBy('status')
            ->all();

        return Inertia::render('ProjectTracker/Index', [
            'summary' => config('project_tracker.summary', []),
            'phases' => config('project_tracker.phases', []),
            'features' => $features,
            'focus' => config('project_tracker.focus', []),
            'stats' => [
                'done' => $counts['done'] ?? 0,
                'in_progress' => $counts['in_progress'] ?? 0,
                'planned' => $counts['planned'] ?? 0,
                'total' => count($features),
            ],
        ]);
    }
}
