<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Dashboard | ProjectPulse</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <i class="fa-solid fa-vial-circle-check brand-icon"></i>
            <span>ProjectPulse <span class="brand-badge">QA Portal</span></span>
        </div>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-name">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                <span class="user-role">QA Engineer</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn"><i class="fa-solid fa-power-off"></i></button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(isset($notifications) && $notifications->isNotEmpty())
            <div class="alert alert-info" style="background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <strong><i class="fa-solid fa-bell"></i> Notifications:</strong>
                <ul style="margin-top: 5px; margin-bottom: 0;">
                    @foreach($notifications as $notification)
                        <li>{{ $notification->message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($projects->isEmpty())
            <div class="card empty-state">
                <h3>No Projects Assigned</h3>
            </div>
        @else
            <div class="card" style="margin-bottom: 1.5rem;">
                <form action="{{ route('qa.dashboard') }}" method="GET">
                    <label>Switch Project:</label>
                    <select name="project_id" onchange="this.form.submit()">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $activeProject->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="grid grid-2">
                <div class="card">
                    <h3>Pending QA Approval ({{ $pendingQaTasks->count() }})</h3>
                    @forelse($pendingQaTasks as $task)
                        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                            <h4>{{ $task->title }}</h4>
                            <p>{{ $task->description }}</p>
                            <p><strong>Developer:</strong> {{ $task->assignedUser->name ?? 'N/A' }}</p>
                            <form action="{{ route('qa.tasks.approve', $task->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-success">Approve</button>
                            </form>
                            <form action="{{ route('qa.tasks.reject', $task->id) }}" method="POST" style="display:inline; margin-left: 10px;">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Rejection reason..." required>
                                <button class="btn btn-danger">Reject</button>
                            </form>
                        </div>
                    @empty
                        <p>No tasks waiting for QA approval.</p>
                    @endforelse
                </div>

                <div class="card">
                    <h3>Already Approved ({{ $approvedTasks->count() }})</h3>
                    @forelse($approvedTasks as $task)
                         <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; opacity: 0.7;">
                            <h4>{{ $task->title }} <span style="color: green;">(QA Approved)</span></h4>
                        </div>
                    @empty
                        <p>No tasks approved yet.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</body>
</html>
