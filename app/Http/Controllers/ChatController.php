<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Store a new chat message/comment for a project.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Check auth (assuming the user is part of the project)
        $user = auth()->user();

        $project->comments()->create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        // Notifications logic
        // If Client commented -> notify PM and QA
        if ($user->role === 'client') {
            $pmIds = $project->users()->where('role', 'pm')->pluck('users.id');
            $qaIds = $project->users()->where('role', 'qa')->pluck('users.id');
            $notifyIds = $pmIds->merge($qaIds);

            foreach ($notifyIds as $notifyId) {
                Notification::create([
                    'user_id' => $notifyId,
                    'project_id' => $project->id,
                    'message' => "Client {$user->name} posted a new comment in {$project->title}",
                ]);
            }
        }

        // Return to the dashboard based on role
        if ($user->role === 'client') {
            return redirect()->route('client.dashboard', ['project_id' => $project->id])->with('success', 'Message posted!');
        } elseif ($user->role === 'pm') {
            return redirect()->route('pm.dashboard', ['project_id' => $project->id])->with('success', 'Message posted!');
        } elseif ($user->role === 'qa') {
            return redirect()->route('qa.dashboard', ['project_id' => $project->id])->with('success', 'Message posted!');
        } elseif ($user->role === 'designer') {
            return redirect()->route('designer.dashboard', ['project_id' => $project->id])->with('success', 'Message posted!');
        } else {
            return redirect()->route('developer.dashboard', ['project_id' => $project->id])->with('success', 'Message posted!');
        }
    }
}
