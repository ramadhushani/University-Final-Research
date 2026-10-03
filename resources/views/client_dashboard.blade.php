<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard | ProjectPulse</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- Header Navigation Bar -->
    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-square-poll-vertical brand-icon"></i>
            <span>ProjectPulse <span class="brand-badge">Client Portal</span></span>
        </div>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-name">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                <span class="user-role">Client Account</span>
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

    <!-- Main Content Wrapper -->
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
                <i class="fa-solid fa-sheet-plastic empty-state-icon"></i>
                <h3 class="empty-state-text">No Projects Configured</h3>
                <p style="font-size:0.85rem; color:var(--text-secondary); margin-top:0.5rem;">The project manager has not assigned any active projects to your Client ID yet.</p>
            </div>
        @else
            <!-- Project Selector (If multiple projects exist) -->
            @if($projects->count() > 1)
                <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
                    <form action="{{ route('client.dashboard') }}" method="GET" style="display:flex; align-items:center; gap: 1rem;">
                        <label for="project_id" class="form-label" style="margin-bottom:0; font-weight:700;">Switch Project:</label>
                        <select name="project_id" id="project_id" class="form-control" style="max-width:300px; padding:0.5rem;" onchange="this.form.submit()">
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $activeProject->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif

            <!-- Active Project Header -->
            <div class="card" style="margin-bottom: 2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
                <div>
                    <h1 style="font-family:var(--font-heading); font-size:1.75rem; font-weight:900;">{{ $activeProject->name }}</h1>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">
                        <i class="fa-solid fa-hashtag"></i> Client Project ID: PROJ-{{ 1000 + $activeProject->id }}
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    @if($activeProject->proposal_file_path)
                        <a href="{{ url('storage/' . $activeProject->proposal_file_path) }}" class="btn btn-outline-primary" download>
                            <i class="fa-solid fa-file-pdf"></i> Download Proposal
                        </a>
                    @endif
                    <div class="health-badge">
                        <span class="ping-indicator"></span>
                        <span>Project Health: Live & Stable</span>
                    </div>
                </div>
            </div>

            <!-- Dashboard Tabs Navigation -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab(event, 'overview-tab')">
                    <i class="fa-solid fa-chart-line"></i> Overview
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'developers-tab')">
                    <i class="fa-solid fa-user-group"></i> Developers
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'updates-tab')">
                    <i class="fa-solid fa-rectangle-list"></i> Project Updates
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'feedback-tab')">
                    <i class="fa-solid fa-comments"></i> Feedback & Discussion
                </button>
            </div>

            <!-- Tab 1: Overview -->
            <div id="overview-tab" class="tab-content active">
                <div class="grid grid-3" style="margin-bottom: 2rem;">
                    <!-- Overall progress -->
                    <div class="metric-box">
                        <div class="metric-label">Overall Progress</div>
                        <div class="metric-value">{{ $metrics['progress'] }}%</div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $metrics['progress'] }}%"></div>
                        </div>
                    </div>

                    <!-- Active Tasks count -->
                    <div class="metric-box">
                        <div class="metric-label">Active / In Progress</div>
                        <div class="metric-value" style="color: var(--color-warning);">
                            {{ sprintf('%02d', $metrics['in_progress']) }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.75rem;">
                            <i class="fa-solid fa-spinner fa-spin-slow"></i> Work currently being coded
                        </div>
                    </div>

                    <!-- Completed Tasks count -->
                    <div class="metric-box">
                        <div class="metric-label">Completed / QA Approved</div>
                        <div class="metric-value" style="color: var(--color-success);">
                            {{ sprintf('%02d', $metrics['completed'] + $metrics['qa_approved']) }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--color-success); margin-top:0.75rem;">
                            <i class="fa-solid fa-circle-check"></i> Finished work
                        </div>
                    </div>
                </div>

                <div class="grid grid-2" style="margin-bottom: 2rem; gap: 2rem;">
                    <!-- Project Statement -->
                    <div class="card">
                        <h3 style="font-family:var(--font-heading); margin-bottom:1rem; font-size:1.15rem;">Project Statement of Goals</h3>
                        <p style="color:var(--text-secondary); line-height:1.7;">{{ $activeProject->description ?? 'No project statement available.' }}</p>

                        <!-- UI Link Review -->
                        @if($activeProject->ui_link)
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                                <h4 style="font-family:var(--font-heading); margin-bottom:0.5rem;">UI Design Review</h4>
                                <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    <a href="{{ $activeProject->ui_link }}" target="_blank" class="btn btn-outline-primary" style="font-size: 0.85rem; padding: 0.25rem 0.75rem;">
                                        <i class="fa-solid fa-up-right-from-square"></i> View UI Design
                                    </a>
                                    <span style="margin-left: 10px; font-weight: bold; color: {{ $activeProject->ui_status === 'Accepted' ? 'green' : ($activeProject->ui_status === 'Rejected' ? 'red' : 'orange') }}">
                                        Status: {{ $activeProject->ui_status === 'Rejected' ? 'Changes Requested' : ($activeProject->ui_status ?? 'Pending') }}
                                    </span>
                                </p>
                                @if($activeProject->ui_status !== 'Accepted')
                                    <form action="{{ route('client.ui.update', $activeProject->id) }}" method="POST" style="display: flex; gap: 0.5rem;">
                                        @csrf
                                        <button type="submit" name="ui_status" value="Accepted" class="btn btn-success" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                                            <i class="fa-solid fa-check"></i> Accept UI
                                        </button>
                                        <button type="submit" name="ui_status" value="Rejected" class="btn btn-danger" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                                            <i class="fa-solid fa-xmark"></i> Request Changes
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Progress Chart -->
                    <div class="card" style="display: flex; flex-direction: column; align-items: center;">
                        <h3 style="font-family:var(--font-heading); margin-bottom:1rem; font-size:1.15rem; width: 100%;">Task Progress Breakdown</h3>
                        <div style="position: relative; height: 250px; width: 100%;">
                            <canvas id="taskProgressChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Assigned Developers -->
            <div id="developers-tab" class="tab-content">
                <div class="card">
                    <h3 style="font-family:var(--font-heading); margin-bottom:1.5rem; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fa-solid fa-laptop-code" style="color:var(--primary);"></i>
                        Assigned Technical Team
                    </h3>

                    @if($activeProject->users->isEmpty())
                        <div class="empty-state" style="padding: 1.5rem 0;">
                            <p class="empty-state-text">No team members have been assigned to this project yet.</p>
                        </div>
                    @else
                        <div class="dev-list">
                            @foreach($activeProject->users->where('role', '!=', 'client') as $member)
                                <div class="dev-item">
                                    <div class="dev-avatar-info">
                                        <div class="avatar" style="width:34px; height:34px; font-size:0.9rem;">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div style="font-size:0.92rem; font-weight:600;">{{ $member->name }}</div>
                                            <div style="font-size:0.75rem; color:var(--text-secondary);">{{ ucfirst($member->role) }}</div>
                                        </div>
                                    </div>
                                    <span class="dev-username">@id: {{ $member->username }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 3: Project Updates (Day-by-Day Logs) -->
            <div id="updates-tab" class="tab-content">
                <div class="card">
                    <h3 style="font-family:var(--font-heading); margin-bottom:1.5rem; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);"></i>
                        Day-by-Day Activity Stream
                    </h3>

                    @if($groupedUpdates->isEmpty())
                        <div class="empty-state" style="padding: 2rem 0;">
                            <i class="fa-solid fa-clipboard-list empty-state-icon"></i>
                            <p class="empty-state-text">No updates recorded yet. Assigned developers will post day-by-day logs here.</p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($groupedUpdates as $date => $tasks)
                                <div class="timeline-date-group">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-date-header">{{ $date }}</div>

                                    @foreach($tasks as $task)
                                        <div class="timeline-item">
                                            <div class="timeline-header">
                                                <div>
                                                    <span class="timeline-title">{{ $task->title }}</span>
                                                    <div class="timeline-author">
                                                        Logged by: <strong>{{ $task->assignedUser->name ?? 'System' }}</strong>
                                                    </div>
                                                </div>

                                                <!-- Status Badge -->
                                                @if($task->status === 'Completed')
                                                    <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> {{ $task->status }}</span>
                                                @elseif($task->status === 'In Progress')
                                                    <span class="badge badge-warning"><i class="fa-solid fa-spinner fa-spin-slow"></i> {{ $task->status }}</span>
                                                @else
                                                    <span class="badge badge-info"><i class="fa-solid fa-circle-question"></i> {{ $task->status }}</span>
                                                @endif
                                            </div>

                                            @if($task->client_update)
                                                <div class="timeline-update-note">
                                                    <strong style="color: var(--primary); font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:0.25rem;">
                                                        Client-Friendly Update:
                                                    </strong>
                                                    {{ $task->client_update }}
                                                </div>
                                            @else
                                                <div style="font-size:0.85rem; color:var(--text-muted); font-style:italic;">No description summary logged for this update.</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 4: Client Feedback & Project Discussion -->
            <div id="feedback-tab" class="tab-content">
                <div class="card">
                    <h3 style="font-family:var(--font-heading); margin-bottom:1.5rem; font-size:1.2rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fa-solid fa-comments" style="color:var(--primary);"></i>
                        Feedback & Questions for Project Team
                    </h3>

                    <!-- Feedback Submission Form -->
                    <form action="{{ route('chat.store', $activeProject->id) }}" method="POST" style="margin-bottom: 2rem; background: var(--bg-app); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        @csrf
                        <div class="form-group">
                            <label for="feedback_comment" class="form-label">
                                Share Client Feedback or Inquiries
                                <span style="font-size:0.75rem; color:var(--text-muted); font-weight:normal; display:block; margin-top:0.15rem;">
                                    Your message will be sent directly to the Project Manager and assigned Developers.
                                </span>
                            </label>
                            <textarea name="message" id="feedback_comment" class="form-control" placeholder="E.g. The checkout workflow looks great! Can we add PayPal in addition to Stripe?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane"></i> Send Feedback to Team
                        </button>
                    </form>

                    <!-- Discussion Feed List -->
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.35rem;">
                        <i class="fa-solid fa-comments-dollar" style="color: var(--primary);"></i> Discussion History ({{ $comments->count() }})
                    </h4>

                    @if($comments->isEmpty())
                        <div class="empty-state" style="padding: 1.5rem 0;">
                            <p class="empty-state-text">No feedback messages yet. Post your thoughts above to start the conversation.</p>
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach($comments as $comment)
                                <div style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; border-left: 4px solid {{ $comment->user->role === 'client' ? 'var(--color-success)' : ($comment->user->role === 'pm' ? 'var(--primary)' : 'var(--color-info)') }};">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <strong>{{ $comment->user->name }}</strong>
                                            @if($comment->user->role === 'client')
                                                <span class="badge badge-success">Client</span>
                                            @elseif($comment->user->role === 'pm')
                                                <span class="badge badge-warning">Project Manager</span>
                                            @else
                                                <span class="badge badge-info">Developer</span>
                                            @endif
                                        </div>
                                        <span style="font-size: 0.78rem; color: var(--text-muted);">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.6; margin-top: 0.25rem;">
                                        {{ $comment->message }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Switch Tab Javascript logic -->
    <script>
        function switchTab(event, tabId) {
            // Hide all tab content
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(c => c.classList.remove('active'));

            // Remove active style from all tab buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(b => b.classList.remove('active'));

            // Show target tab content & add active class to button
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Initialize Task Progress Chart if metrics are available
        @if(isset($metrics) && $metrics['total'] > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('taskProgressChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'In Progress', 'QA Approved', 'Completed'],
                    datasets: [{
                        data: [
                            {{ $metrics['pending'] }},
                            {{ $metrics['in_progress'] }},
                            {{ $metrics['qa_approved'] }},
                            {{ $metrics['completed'] }}
                        ],
                        backgroundColor: [
                            '#6c757d', // secondary for pending
                            '#f59e0b', // warning for in progress
                            '#3b82f6', // primary/blue for QA approved
                            '#10b981'  // success for completed
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Inter, sans-serif' }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
        @endif
    </script>
</body>
</html>