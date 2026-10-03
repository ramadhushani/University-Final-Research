<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    /**
     * Show the shared team dashboard (Developer / Designer / QA).
     * Each role sees their assigned projects and relevant tasks.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // All projects this team member is assigned to (via pivot)
        $projects = $user->projects()
            ->with(['client', 'users', 'tasks.assignedUser', 'comments.user'])
            ->latest()
            ->get();

        $activeProject = null;
        if ($projects->isNotEmpty()) {
            $projectId     = $request->query('project_id');
            $activeProject = $projectId
                ? $projects->firstWhere('id', $projectId) ?? $projects->first()
                : $projects->first();
        }

        // Tasks scoped by role
        $myTasks    = collect();
        $teamTasks  = collect();
        $comments   = collect();

        if ($activeProject) {
            $allTasks = $activeProject->tasks()->with('assignedUser')->latest()->get();

            if ($user->role === 'developer') {
                // Developer sees their own tasks + pending/in-progress to pick up
                $myTasks   = $allTasks->where('assigned_to', $user->id);
                $teamTasks = $allTasks->whereNotIn('status', ['QA Approved'])->where('assigned_to', '!=', $user->id);
            } elseif ($user->role === 'qa') {
                // QA sees all Completed tasks waiting for approval
                $myTasks   = $allTasks->whereIn('status', ['Completed']);
                $teamTasks = $allTasks->whereIn('status', ['QA Approved']);
            } elseif ($user->role === 'designer') {
                // Designer sees all tasks and full project context
                $myTasks   = $allTasks->where('assigned_to', $user->id);
                $teamTasks = $allTasks;
            } else {
                $myTasks   = $allTasks;
                $teamTasks = collect();
            }

            $comments = $activeProject->comments()->with('user')->latest()->get();
        }

        return view('developer_dashboard', compact(
            'projects',
            'activeProject',
            'myTasks',
            'teamTasks',
            'comments',
        ));
    }

    /**
     * Developer marks a task status.
     * When marked "Completed" → auto-set project status to "Pending QA" so QA is notified.
     */
    public function updateTask(Request $request, Task $task)
    {
        $request->validate([
            'status'      => 'required|in:Pending,Completed',
            'description' => 'nullable|string|max:1000',
        ]);

        // Authorise: task must belong to a project this user is assigned to
        $project = auth()->user()->projects()->findOrFail($task->project_id);

        // Developers can only update their own tasks
        if (auth()->user()->role === 'developer' && $task->assigned_to !== auth()->id()) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        $newStatus = $request->status;

        $task->update([
            'status'      => $newStatus,
            'description' => $request->description ?? $task->description,
        ]);

        // When a developer marks a task Completed → push project to "Pending QA"
        if ($newStatus === 'Completed' && $project->status === 'In-Progress') {
            $project->update(['status' => 'Pending QA']);
            
            // Notify PM and QA
            $pmIds = $project->users()->where('role', 'pm')->pluck('users.id');
            $qaIds = $project->users()->where('role', 'qa')->pluck('users.id');
            $notifyIds = $pmIds->merge($qaIds);

            foreach ($notifyIds as $notifyId) {
                Notification::create([
                    'user_id' => $notifyId,
                    'project_id' => $project->id,
                    'message' => "Developer " . auth()->user()->name . " completed task '{$task->title}'",
                ]);
            }
        }

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('success', $newStatus === 'Completed'
                ? 'Task marked Completed — routed to QA for approval!'
                : 'Task status updated.');
    }

    /**
     * QA approves a completed task → status becomes "QA Approved".
     */
    public function approveTask(Request $request, Task $task)
    {
        // QA only
        if (auth()->user()->role !== 'qa') {
            abort(403, 'Only QA Engineers can approve tasks.');
        }

        $project = auth()->user()->projects()->findOrFail($task->project_id);

        if ($task->status !== 'Completed') {
            return redirect()->route('developer.dashboard', ['project_id' => $project->id])
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

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('success', 'Task QA Approved ✅ — PM dashboard updated.');
    }

    /**
     * QA rejects a task → sends it back to "Pending" for the developer.
     */
    public function rejectTask(Request $request, Task $task)
    {
        if (auth()->user()->role !== 'qa') {
            abort(403, 'Only QA Engineers can reject tasks.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $project = auth()->user()->projects()->findOrFail($task->project_id);

        $task->update([
            'status'      => 'Pending',
            'description' => $task->description . ($request->rejection_reason
                ? "\n\n[QA Rejection] " . $request->rejection_reason
                : "\n\n[QA Rejected — requires rework]"),
        ]);

        // Put project back to In-Progress
        if ($project->status === 'Pending QA') {
            $project->update(['status' => 'In-Progress']);
        }

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('error', 'Task rejected and sent back to developer for rework.');
    }

    /**
     * Designer submits a UI link (Figma / live preview) for a project.
     */
    public function submitUiLink(Request $request, Project $project)
    {
        if (auth()->user()->role !== 'designer') {
            abort(403, 'Only Designers can submit UI links.');
        }

        // Authorise: designer must be assigned to this project
        auth()->user()->projects()->findOrFail($project->id);

        $request->validate([
            'ui_link' => 'required|url|max:500',
        ]);

        $project->update([
            'ui_link'   => $request->ui_link,
            'ui_status' => 'Pending',   // reset to Pending when a new link is submitted
            'status'    => 'UI Pending',
        ]);

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('success', 'UI link submitted! Client and PM can now review it.');
    }

    /**
     * Post a comment on a project (available to all team roles).
     */
    public function storeComment(Request $request, Project $project)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Authorise: user must be assigned to this project
        auth()->user()->projects()->findOrFail($project->id);

        $project->comments()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('success', 'Comment posted.');
    }

    /**
     * Legacy storeTask — kept for backward compat but not used in new flow.
     */
    public function storeTask(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = auth()->user()->projects()->findOrFail($request->project_id);

        Task::create([
            'project_id'  => $project->id,
            'title'       => $request->title,
            'description' => $request->description,
            'assigned_to' => auth()->id(),
            'status'      => 'Pending',
        ]);

        return redirect()->route('developer.dashboard', ['project_id' => $project->id])
            ->with('success', 'Task logged.');
    }
}
