<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Workspace | ProjectPulse</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <a href="{{ route('developer.dashboard') }}" class="brand">
            <i class="fa-solid fa-square-poll-vertical brand-icon"></i>
            <span>ProjectPulse <span class="brand-badge">Dev Workspace</span></span>
        </a>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-name">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                <span class="user-role">Software Developer</span>
            </div>
            <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <form action="{{ route('logout') }}" method="POST" style="margin-left: 0.5rem;">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fa-solid fa-power-off"></i>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Please check the errors below:</strong>
                    <ul style="list-style:disc; padding-left:1rem; margin-top:0.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if($projects->isEmpty())
            <div class="card empty-state">
                <i class="fa-solid fa-code empty-state-icon"></i>
                <h3 class="empty-state-text">No Assigned Projects</h3>
                <p style="font-size:0.85rem; color:var(--text-secondary); margin-top:0.5rem;">You are not assigned to any projects at the moment. Contact your Project Manager to be added.</p>
            </div>
        @else
            <!-- Project Selector (If multiple projects) -->
            @if($projects->count() > 1)
                <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
                    <form action="{{ route('developer.dashboard') }}" method="GET" style="display:flex; align-items:center; gap: 1rem;">
                        <label for="project_id" class="form-label" style="margin-bottom:0; font-weight:700;">Active Workspace:</label>
                        <select name="project_id" id="project_id" class="form-control" style="max-width:300px; padding:0.5rem;" onchange="this.form.submit()">
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $activeProject->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            <!-- Active Project Banner -->
            <div class="card" style="margin-bottom: 2rem;">
                <h2 style="font-family: var(--font-heading); font-size:1.5rem; font-weight:900;">
                    <span style="color:var(--text-secondary); font-size: 0.85rem; text-transform:uppercase; display:block; font-weight:600; margin-bottom: 0.25rem;">Active Project</span>
                    {{ $activeProject->name }}
                </h2>
                <p style="font-size: 0.9rem; color:var(--text-secondary); margin-top: 0.5rem; line-height: 1.6;">
                    {{ $activeProject->description ?? 'No project statement provided.' }}
                </p>
                <div style="font-size:0.8rem; color:var(--text-secondary); margin-top: 0.75rem;">
                    <strong>Assigned Client:</strong> {{ $activeProject->client->name ?? 'None' }} 
                    <span style="color:var(--text-muted); margin-left:0.5rem;">({{ $activeProject->client->username ?? '' }})</span>
                </div>
            </div>

            <!-- Dashboard Split -->
            <div class="grid grid-split-3-1">
                
                <!-- Left Panel: Form to Create/Update Task Logs & Discussion -->
                <div>
                    <div class="card">
                        <h3 id="form-header" style="font-family: var(--font-heading); font-weight: 800; font-size: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                            <span id="form-title-text"><i class="fa-solid fa-square-plus" style="color:var(--primary); margin-right:0.35rem;"></i> Log a Task Update</span>
                            <button type="button" id="cancel-edit-btn" class="btn btn-secondary btn-sm" style="display: none;" onclick="resetTaskForm()">
                                <i class="fa-solid fa-xmark"></i> Cancel Edit
                            </button>
                        </h3>

                        <form id="task-form" action="{{ route('developer.tasks.update', '__task_id__') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_task_id" id="task_id_field" value="">

                            <div class="form-group">
                                <label for="title" class="form-label">Task Title</label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="E.g. Stripe API Integration" required>
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Development Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Pending">⏳ Pending</option>
                                    <option value="Completed">✅ Completed → Routes to QA</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">
                                    Progress Notes
                                    <span style="font-size:0.75rem; color:var(--text-muted); font-weight:normal; display:block; margin-top:0.15rem;">Describe what was done. Visible to PM and QA.</span>
                                </label>
                                <textarea name="description" id="description" class="form-control" placeholder="E.g. Integrated Stripe API, card payments now processing in sandbox..."></textarea>
                            </div>

                            <button type="submit" id="submit-btn" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                                <i class="fa-solid fa-circle-arrow-up"></i> Save Changes
                            </button>
                        </form>
                    </div>

                    <!-- Project Discussion / Client Inquiries Card -->
                    <div class="card" style="margin-top: 1.5rem;">
                        <h3 style="font-family: var(--font-heading); font-weight: 800; font-size: 1.15rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-comments" style="color:var(--primary);"></i>
                            Client & Team Discussion ({{ $comments->count() }})
                        </h3>

                        @if($comments->isEmpty())
                            <p style="font-size: 0.85rem; color: var(--text-muted); font-style: italic; margin-bottom: 1rem;">No discussion posted yet.</p>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 240px; overflow-y: auto; margin-bottom: 1rem; padding-right: 0.35rem;">
                                @foreach($comments as $c)
                                    <div style="background: var(--bg-app); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem; border-left: 3px solid {{ $c->user->role === 'client' ? 'var(--color-success)' : ($c->user->role === 'pm' ? 'var(--primary)' : 'var(--color-info)') }};">
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; margin-bottom: 0.25rem;">
                                            <strong>{{ $c->user->name }} <span class="badge {{ $c->user->role === 'client' ? 'badge-success' : ($c->user->role === 'pm' ? 'badge-warning' : 'badge-info') }}" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">{{ ucfirst($c->user->role) }}</span></strong>
                                            <span style="font-size: 0.72rem; color: var(--text-muted);">{{ $c->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.45;">{{ $c->message }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
@if(isset($activeProject) && $activeProject)
    <div class="alert alert-info" style="font-size: 0.85rem; margin-top: 1rem;">
        Comments feature will be available soon.
    </div>
@endif
                    </div>
                </div>

                <!-- Right Panel: List of Tasks/Updates on this Project -->
                <div>
                    <h2 style="font-family: var(--font-heading); font-size:1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-list-check" style="color:var(--primary);"></i>
                        Sprint Logs
                    </h2>

                    @if(!isset($myTasks) || $myTasks->isEmpty())
                        <div class="card empty-state" style="padding:2.5rem 1rem;">
                            <i class="fa-solid fa-clipboard-list empty-state-icon"></i>
                            <p class="empty-state-text">No updates have been logged for this project yet. Use the left panel to submit your first progress report!</p>
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach($myTasks as $task)
                                <div class="card" style="padding:1.25rem;">
                                    <div class="timeline-header" style="margin-bottom:0.5rem;">
                                        <span class="timeline-title" style="font-weight:700;">{{ $task->title }}</span>
                                        @if($task->status === 'QA Approved')
                                            <span class="badge badge-success">✅ QA Approved</span>
                                        @elseif($task->status === 'Completed')
                                            <span class="badge badge-warning">⏳ Pending QA</span>
                                        @else
                                            <span class="badge badge-info">{{ $task->status }}</span>
                                        @endif
                                    </div>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.75rem;">
                                        Assigned to: <strong>{{ $task->assignedUser->name ?? 'Unassigned' }}</strong>
                                        @if($task->assigned_to === auth()->id())
                                            <span class="badge badge-success" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; margin-left: 0.35rem;">Assigned to You</span>
                                        @endif
                                        • Updated {{ $task->updated_at->diffForHumans() }}
                                    </div>
                                    @if($task->description)
                                        <p style="font-size:0.85rem; color:var(--text-secondary); background:var(--bg-app); padding:0.6rem 0.8rem; border-radius:var(--radius-sm); border-left: 2px solid var(--primary); line-height:1.5;">
                                            {{ $task->description }}
                                        </p>
                                    @endif
                                    @if($task->status !== 'QA Approved')
                                    <div style="margin-top: 1rem; text-align: right;">
                                        <button type="button" class="btn btn-secondary btn-sm" 
                                            data-id="{{ $task->id }}"
                                            data-title="{{ $task->title }}"
                                            data-status="{{ $task->status }}"
                                            data-description="{{ $task->description }}"
                                            onclick="editTaskFromBtn(this)">
                                            <i class="fa-solid fa-pen-to-square"></i> Update Status
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endif
    </div>

    <!-- Edit Task inline javascript -->
    <script>
        function editTaskFromBtn(btn) {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            const status = btn.getAttribute('data-status');
            const description = btn.getAttribute('data-description') || '';

            // Update form header
            document.getElementById('form-title-text').innerHTML = '<i class="fa-solid fa-pen-to-square" style="color:var(--primary); margin-right:0.35rem;"></i> Update Task Status';
            document.getElementById('cancel-edit-btn').style.display = 'inline-flex';

            // Populate inputs
            document.getElementById('title').value = title;
            document.getElementById('title').setAttribute('readonly', 'readonly');
            document.getElementById('status').value = (status === 'Completed' || status === 'QA Approved') ? 'Completed' : 'Pending';
            document.getElementById('description').value = description;

            // Modify form action to point to the update route
            const form = document.getElementById('task-form');
            form.action = `/developer/tasks/${id}`;

            // Change submit button text
            document.getElementById('submit-btn').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';

            // Scroll form into view
            document.getElementById('task-form').scrollIntoView({ behavior: 'smooth' });
        }

        function resetTaskForm() {
            // Restore header
            document.getElementById('form-title-text').innerHTML = '<i class="fa-solid fa-square-plus" style="color:var(--primary); margin-right:0.35rem;"></i> Update My Task';
            document.getElementById('cancel-edit-btn').style.display = 'none';

            // Reset inputs
            document.getElementById('title').value = '';
            document.getElementById('title').removeAttribute('readonly');
            document.getElementById('status').value = 'Pending';
            document.getElementById('description').value = '';

            // Reset submit button text
            document.getElementById('submit-btn').innerHTML = '<i class="fa-solid fa-circle-arrow-up"></i> Save Changes';
        }
    </script>

</body>
</html>
