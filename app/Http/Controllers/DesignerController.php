<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DesignerController extends Controller
{
    /**
     * Designer Dashboard: View assigned projects to submit UI links.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $projects = collect();
        if (method_exists($user, 'projects')) {
            $projects = $user->projects()
                ->with(['client', 'users'])
                ->latest()
                ->get();
        } else {
            $projects = Project::with(['client', 'users'])->get();
        }

        $activeProject = null;
        if ($projects->isNotEmpty()) {
            $projectId     = $request->query('project_id');
            $activeProject = $projectId
                ? $projects->firstWhere('id', $projectId) ?? $projects->first()
                : $projects->first();
        }

        return view('designer.dashboard', compact('projects', 'activeProject'));
    }

    /**
     * Designer submits a UI link (Figma / live preview) for a project.
     */
    public function submitUiLink(Request $request, Project $project)
    {
        $request->validate([
            'ui_link' => 'required|url|max:500',
        ]);

        $project->update([
            'ui_link'   => $request->ui_link,
            'ui_status' => 'Pending',   // reset to Pending when a new link is submitted
            'status'    => 'UI Pending',
        ]);

        return redirect()->route('designer.dashboard', ['project_id' => $project->id])
            ->with('success', 'UI link submitted! Client and PM can now review it.');
    }
}
