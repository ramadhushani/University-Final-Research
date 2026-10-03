<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;

class QAController extends Controller
{
    /**
     * QA Dashboard: View tasks waiting for approval.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $projects = collect();
        if (method_exists($user, 'projects')) {
            $projects = $user->projects()
                ->with(['client', 'users', 'tasks.assignedUser', 'comments.user'])
                ->latest()
                ->get();
        } else {
            // fallback
            $projects = Project::with(['client', 'users', 'tasks.assignedUser', 'comments.user'])->get();
        }

        $activeProject = null;
        if ($projects->isNotEmpty()) {
            $projectId     = $request->query('project_id');
            $activeProject = $projectId
                ? $projects->firstWhere('id', $projectId) ?? $projects->first()
                : $projects->first();
        }

        $pendingQaTasks = collect();
        $approvedTasks = collect();

        if ($activeProject) {
            $allTasks = $activeProject->tasks()->with('assignedUser')->latest()->get();
            $pendingQaTasks = $allTasks->where('status', 'Completed'); // Dev marked completed
            $approvedTasks = $allTasks->where('status', 'QA Approved');
        }

        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->get();

        return view('qa.dashboard', compact('projects', 'activeProject', 'pendingQaTasks', 'approvedTasks', 'notifications'));
    }

    /**
     * QA approves a completed task → status becomes "QA Approved".
     */
    public function approveTask(Request $request, Task $task)
    {
        $project = Project::findOrFail($task->project_id);

        if ($task->status !== 'Completed') {
            return redirect()->route('qa.dashboard', ['project_id' => $project->id])
                ->with('error', 'Only tasks with status "Completed" can be QA approved.');
        }

        $task->update(['status' => 'QA Approved']);

        // Check if ALL tasks are now QA Approved → update project status
        $pendingCount = $project->tasks()
            ->whereNotIn('status', ['QA Approved'])
            ->count();

        if ($pendingCount === 0) {
            // All done — PM can now mark as Finished
        }

        return redirect()->route('qa.dashboard', ['project_id' => $project->id])
            ->with('success', 'Task QA Approved ✅ — PM dashboard updated.');
    }

    /**
     * QA rejects a task → sends it back to "Pending" or "In Progress" for the developer.
     */
    public function rejectTask(Request $request, Task $task)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $project = Project::findOrFail($task->project_id);

        $task->update([
            'status'      => 'Pending',
            'description' => $task->description . ($request->rejection_reason
                ? "\n\n[QA Rejection] " . $request->rejection_reason
                : "\n\n[QA Rejected — requires rework]"),
        ]);

        if ($project->status === 'Pending QA') {
            $project->update(['status' => 'In-Progress']);
        }

        return redirect()->route('qa.dashboard', ['project_id' => $project->id])
            ->with('error', 'Task rejected and sent back to developer for rework.');
    }
}
