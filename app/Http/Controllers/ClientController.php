<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Show Client dashboard.
     */
    public function dashboard(Request $request)
    {
        $projects = Project::where('client_id', auth()->id())
            ->with(['users', 'tasks.assignedUser', 'comments.user'])
            ->latest()
            ->get();

        $activeProject = null;
        if ($projects->isNotEmpty()) {
            $projectId = $request->query('project_id');
            $activeProject = $projectId 
                ? $projects->firstWhere('id', $projectId) 
                : $projects->first();
            
            // If the query parameter is invalid, fall back to first
            if (!$activeProject) {
                $activeProject = $projects->first();
            }
        }

        // Calculate project metrics if there is an active project
        $metrics = [
            'progress' => 0,
            'completed' => 0,
            'qa_approved' => 0,
            'in_progress' => 0,
            'pending' => 0,
            'total' => 0,
        ];

        $groupedUpdates = collect();
        $comments = collect();

        if ($activeProject) {
            $tasks = $activeProject->tasks;
            $metrics['total'] = $tasks->count();
            if ($metrics['total'] > 0) {
                $metrics['completed'] = $tasks->where('status', 'Completed')->count();
                $metrics['qa_approved'] = $tasks->where('status', 'QA Approved')->count();
                $metrics['in_progress'] = $tasks->where('status', 'In Progress')->count();
                $metrics['pending'] = $tasks->where('status', 'Pending')->count();
                $metrics['progress'] = round((($metrics['completed'] + $metrics['qa_approved']) / $metrics['total']) * 100);
            }

            // Group tasks/updates day-by-day
            $groupedUpdates = $tasks->sortByDesc('updated_at')->groupBy(function ($task) {
                return $task->updated_at->format('F d, Y');
            });

            // Get feedback / comments
            $comments = $activeProject->comments()->with('user')->latest()->get();
        }

        return view('client_dashboard', compact('projects', 'activeProject', 'metrics', 'groupedUpdates', 'comments'));
    }

    /**
     * Submit client feedback / comments on project.
     */
    public function storeComment(Request $request, Project $project)
    {
        // Ensure this client is assigned to this project
        if ($project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $project->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('client.dashboard', ['project_id' => $project->id])
            ->with('success', 'Your feedback / comment has been posted successfully!');
    }

    /**
     * Accept UI or Request Changes.
     */
    public function updateUiStatus(Request $request, Project $project)
    {
        if ($project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'ui_status' => 'required|in:Accepted,Rejected',
        ]);

        $project->update([
            'ui_status' => $request->ui_status,
            'status' => $request->ui_status === 'Accepted' ? 'In-Progress' : 'UI Pending',
        ]);

        return redirect()->route('client.dashboard', ['project_id' => $project->id])
            ->with('success', 'UI feedback submitted successfully!');
    }
}
