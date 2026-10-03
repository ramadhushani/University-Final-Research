<?php

namespace App\Http\Controllers;

use App\Mail\ProjectCreatedClient;
use App\Mail\ProjectCreatedTeam;
use App\Mail\ProjectFinished;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProjectManagerController extends Controller
{
    /**
     * Show PM dashboard with all projects, team lists, and chart data.
     */
    public function dashboard()
    {
        $projects = Project::with([
            'client',
            'users',
            'tasks.assignedUser',
            'comments.user',
        ])->latest()->get();

        // Chart.js data — count projects by status
        $statusCounts = [
            'Finished'   => $projects->where('status', 'Finished')->count(),
            'In-Progress' => $projects->where('status', 'In-Progress')->count(),
            'Pending QA' => $projects->where('status', 'Pending QA')->count(),
            'UI Pending' => $projects->where('status', 'UI Pending')->count(),
        ];

        // Tasks Chart.js data
        $allTasks = $projects->flatMap->tasks;
        $taskCounts = [
            'Pending' => $allTasks->where('status', 'Pending')->count(),
            'Completed' => $allTasks->where('status', 'Completed')->count(),
            'QA Approved' => $allTasks->where('status', 'QA Approved')->count(),
        ];

        // Lists for assignment dropdowns
        $existingClients   = User::where('role', 'client')->orderBy('name')->get();
        $existingDevelopers = User::where('role', 'developer')->orderBy('name')->get();
        $existingDesigners  = User::where('role', 'designer')->orderBy('name')->get();
        $existingQas        = User::where('role', 'qa')->orderBy('name')->get();

        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->get();

        return view('pm_dashboard', compact(
            'projects',
            'statusCounts',
            'taskCounts',
            'existingClients',
            'existingDevelopers',
            'existingDesigners',
            'existingQas',
            'notifications',
        ));
    }

    /**
     * Store a new project, assign team, upload proposal PDF, and fire emails.
     */
    public function storeProject(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'proposal_file'       => 'nullable|file|mimes:pdf|max:10240',

            // Client
            'client_source'       => 'required|in:new,existing',
            'client_id'           => 'required_if:client_source,existing|nullable|exists:users,id',
            'client_username'     => 'required_if:client_source,new|nullable|string|max:255|unique:users,username',
            'client_name'         => 'required_if:client_source,new|nullable|string|max:255',
            'client_email'        => 'nullable|email|unique:users,email',
            'client_password'     => 'required_if:client_source,new|nullable|string|min:6',

            // Team
            'developer_ids'       => 'nullable|array',
            'developer_ids.*'     => 'exists:users,id',
            'designer_ids'        => 'nullable|array',
            'designer_ids.*'      => 'exists:users,id',
            'qa_ids'              => 'nullable|array',
            'qa_ids.*'            => 'exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Handle PDF Proposal Upload
            $proposalPath = null;
            if ($request->hasFile('proposal_file')) {
                $proposalPath = $request->file('proposal_file')->store('proposals', 'public');
            }

            // 2. Resolve or Create Client
            $clientPlainPassword = null;
            $isNewClient         = false;

            if ($request->client_source === 'existing') {
                $client = User::findOrFail($request->client_id);
            } else {
                $clientPlainPassword = $request->client_password;
                $client = User::create([
                    'name'     => $request->client_name,
                    'username' => $request->client_username,
                    'email'    => $request->client_email,
                    'password' => Hash::make($clientPlainPassword),
                    'role'     => 'client',
                ]);
                $isNewClient = true;
            }

            // 3. Create Project
            $project = Project::create([
                'title'              => $request->title,
                'description'        => $request->description,
                'proposal_file_path' => $proposalPath,
                'client_id'          => $client->id,
                'status'             => 'In-Progress',
                'ui_status'          => 'Pending',
            ]);

            // 4. Resolve team members to attach
            $teamIds = array_merge(
                $request->developer_ids ?? [],
                $request->designer_ids  ?? [],
                $request->qa_ids        ?? [],
            );

            // Always attach the PM themselves
            $teamIds[] = auth()->id();

            if (!empty($teamIds)) {
                $project->users()->sync(array_unique($teamIds));
            }

            // 5. Fire Emails (log driver in local — safe to call)
            // Client email
            if ($isNewClient) {
                try {
                    Mail::to($client->email ?? 'noreply@projectpulse.local')
                        ->send(new ProjectCreatedClient($client, $project, $clientPlainPassword));
                } catch (\Exception $e) {
                    Log::warning('Client project email failed: ' . $e->getMessage());
                }
            }

            // Team member emails — developers, designers, QAs
            $teamMemberIds = array_merge(
                $request->developer_ids ?? [],
                $request->designer_ids  ?? [],
                $request->qa_ids        ?? [],
            );
            $teamMembers = User::whereIn('id', $teamMemberIds)->get();

            foreach ($teamMembers as $member) {
                try {
                    Mail::to($member->email ?? 'noreply@projectpulse.local')
                        ->send(new ProjectCreatedTeam($member, $project, 'password'));
                } catch (\Exception $e) {
                    Log::warning("Team email failed for {$member->username}: " . $e->getMessage());
                }
            }
        });

        return redirect()->route('pm.dashboard')
            ->with('success', 'Project launched successfully! Credentials dispatched to client and team members.');
    }

    /**
     * Mark project as "Finished" — the final sign-off action.
     * Triggers client notification email.
     */
    public function markFinished(Project $project)
    {
        // Check all tasks are either Completed or QA Approved
        $pendingCount = $project->tasks()
            ->whereNotIn('status', ['Completed', 'QA Approved'])
            ->count();

        if ($pendingCount > 0) {
            return redirect()->route('pm.dashboard')
                ->with('error', "Cannot mark as Finished: {$pendingCount} task(s) are still pending or in progress.");
        }

        $project->update(['status' => 'Finished']);

        // Notify client
        if ($project->client) {
            try {
                Mail::to($project->client->email ?? 'noreply@projectpulse.local')
                    ->send(new ProjectFinished($project->client, $project));
            } catch (\Exception $e) {
                Log::warning('Project finished email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('pm.dashboard')
            ->with('success', "🎉 Project '{$project->title}' marked as Finished! Client has been notified.");
    }

    /**
     * Update project status (In-Progress, UI Pending, Pending QA, Finished).
     */
    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status' => 'required|in:In-Progress,UI Pending,Pending QA,Finished',
        ]);

        $project->update(['status' => $request->status]);

        return redirect()->route('pm.dashboard')
            ->with('success', "Project status updated to '{$request->status}'.");
    }

    /**
     * PM assigns a new task to a team member.
     */
    public function storeTask(Request $request, Project $project)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status'      => 'required|in:Pending,Completed,QA Approved',
        ]);

        $project->tasks()->create([
            'title'       => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'status'      => $request->status,
        ]);

        return redirect()->route('pm.dashboard')
            ->with('success', 'Task assigned successfully!');
    }

    /**
     * PM posts a comment / update note on a project.
     */
    public function storeComment(Request $request, Project $project)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $project->comments()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'task_id' => $request->task_id ?: null,
        ]);

        return redirect()->route('pm.dashboard')
            ->with('success', 'Comment posted!');
    }

    /**
     * Delete a project and all related data.
     */
    public function deleteProject(Project $project)
    {
        // Delete proposal file from storage
        if ($project->proposal_file_path) {
            Storage::disk('public')->delete($project->proposal_file_path);
        }

        $project->delete();

        return redirect()->route('pm.dashboard')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Serve/download a proposal PDF file.
     */
    public function downloadProposal(Project $project)
    {
        if (!$project->proposal_file_path || !Storage::disk('public')->exists($project->proposal_file_path)) {
            abort(404, 'Proposal file not found.');
        }

        return Storage::disk('public')->download($project->proposal_file_path);
    }
}
