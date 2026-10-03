<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Dashboard | ProjectPulse</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <span>ProjectPulse <span class="brand-badge">Designer Portal</span></span>
        </div>
        <div class="nav-user">
            <span class="user-name">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($projects->isEmpty())
            <div class="card">
                <h3>No Projects Assigned</h3>
            </div>
        @else
            <div class="card">
                <form action="{{ route('designer.dashboard') }}" method="GET">
                    <label>Switch Project:</label>
                    <select name="project_id" onchange="this.form.submit()">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $activeProject->id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="card mt-2">
                <h3>Submit UI Link for {{ $activeProject->name }}</h3>
                <p>Current UI Link: 
                    @if($activeProject->ui_link)
                        <a href="{{ $activeProject->ui_link }}" target="_blank">{{ $activeProject->ui_link }}</a>
                        (Status: {{ $activeProject->ui_status ?? 'Pending' }})
                    @else
                        <em>None</em>
                    @endif
                </p>
                
                <form action="{{ route('designer.ui.submit', $activeProject->id) }}" method="POST" style="margin-top: 15px;">
                    @csrf
                    <input type="url" name="ui_link" placeholder="https://figma.com/..." class="form-control" required style="width: 100%; margin-bottom: 10px;">
                    <button class="btn btn-primary">Submit UI Link</button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>
