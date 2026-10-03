<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | ProjectPulse</title>
    <meta name="description" content="ProjectPulse — Sign in to access your project management workspace.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #6c63ff;
            --primary-dark:  #4f46e5;
            --primary-glow:  rgba(108,99,255,0.35);
            --accent-cyan:   #22d3ee;
            --accent-purple: #a855f7;
            --accent-green:  #10b981;
            --accent-amber:  #f59e0b;
            --accent-rose:   #f43f5e;

            --bg-app:     #07090f;
            --bg-card:    #0f1322;
            --bg-input:   #161b2e;
            --border:     #1e2540;
            --border-focus: rgba(108,99,255,0.6);

            --text-primary:   #f1f5f9;
            --text-secondary: #8892b0;
            --text-muted:     #3d4a6b;

            --font-body:    'Inter', sans-serif;
            --font-heading: 'Space Grotesk', sans-serif;
            --radius: 16px;
            --transition: 0.25s ease;
        }

        html, body {
            height: 100%;
            font-family: var(--font-body);
            background: var(--bg-app);
            color: var(--text-primary);
            overflow: hidden;
        }

        /* ── ANIMATED BACKGROUND ── */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0;
            background: radial-gradient(ellipse 80% 60% at 20% 10%, rgba(108,99,255,0.18) 0%, transparent 60%),
                        radial-gradient(ellipse 60% 50% at 80% 90%, rgba(34,211,238,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse 50% 40% at 60% 40%, rgba(168,85,247,0.08) 0%, transparent 55%),
                        var(--bg-app);
        }

        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px);
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(108,99,255,0.15); top: -10%; left: -5%; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: rgba(34,211,238,0.10); bottom: -5%; right: -5%; animation-delay: -3s; }
        .orb-3 { width: 200px; height: 200px; background: rgba(168,85,247,0.12); top: 40%; right: 10%; animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Grid lines overlay */
        .grid-overlay {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* ── LAYOUT ── */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem;
            gap: 4rem;
        }

        /* ── LEFT BRANDING PANEL ── */
        .brand-panel {
            flex: 1; max-width: 420px;
            display: flex; flex-direction: column; gap: 2rem;
            animation: slideInLeft 0.7s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .brand-logo {
            display: flex; align-items: center; gap: 0.75rem;
        }
        .brand-icon-wrap {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--accent-purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
            box-shadow: 0 8px 32px rgba(108,99,255,0.4);
        }
        .brand-name {
            font-family: var(--font-heading); font-size: 1.6rem;
            font-weight: 800; color: var(--text-primary);
        }
        .brand-name span {
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .brand-headline {
            font-family: var(--font-heading);
            font-size: 2.6rem; font-weight: 800; line-height: 1.15;
            color: var(--text-primary);
        }
        .brand-headline em {
            font-style: normal;
            background: linear-gradient(135deg, var(--primary), var(--accent-cyan));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .brand-desc {
            font-size: 1rem; color: var(--text-secondary); line-height: 1.75;
        }

        .roles-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;
        }
        .role-chip {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 10px; padding: 0.55rem 0.85rem;
            font-size: 0.82rem; color: var(--text-secondary);
            transition: all 0.2s;
        }
        .role-chip:hover { background: rgba(108,99,255,0.08); border-color: rgba(108,99,255,0.3); color: var(--text-primary); }
        .role-chip i { font-size: 0.85rem; }
        .role-chip.pm     i { color: var(--primary); }
        .role-chip.dev    i { color: var(--accent-cyan); }
        .role-chip.design i { color: var(--accent-purple); }
        .role-chip.qa     i { color: var(--accent-amber); }
        .role-chip.client i { color: var(--accent-green); }

        /* ── LOGIN CARD ── */
        .login-card {
            width: 100%; max-width: 420px;
            background: rgba(15, 19, 34, 0.85);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem;
            backdrop-filter: blur(24px);
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.04);
            animation: slideInRight 0.7s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .card-header {
            margin-bottom: 2rem; text-align: center;
        }
        .card-header h2 {
            font-family: var(--font-heading); font-size: 1.65rem; font-weight: 800;
            color: var(--text-primary); margin-bottom: 0.4rem;
        }
        .card-header p {
            font-size: 0.88rem; color: var(--text-secondary);
        }

        /* ── ERROR ALERT ── */
        .alert-error {
            display: flex; align-items: flex-start; gap: 0.65rem;
            background: rgba(244,63,94,0.1); border: 1px solid rgba(244,63,94,0.3);
            color: #fb7185; border-radius: 12px;
            padding: 0.85rem 1rem; font-size: 0.88rem;
            margin-bottom: 1.5rem; animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-6px); }
            75%      { transform: translateX(6px); }
        }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: var(--text-secondary); text-transform: uppercase;
            letter-spacing: 0.6px; margin-bottom: 0.45rem;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute; left: 0.9rem; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 0.9rem;
            pointer-events: none; transition: color var(--transition);
        }
        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 0.75rem 0.9rem 0.75rem 2.5rem;
            border-radius: 10px;
            font-size: 0.93rem;
            font-family: var(--font-body);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.2);
        }
        .form-control:focus + .input-icon,
        .input-wrap:focus-within .input-icon {
            color: var(--primary);
        }

        /* Toggle password visibility */
        .toggle-pw {
            position: absolute; right: 0.9rem; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--text-muted); font-size: 0.9rem;
            transition: color var(--transition); padding: 0;
        }
        .toggle-pw:hover { color: var(--primary); }

        /* ── SUBMIT BUTTON ── */
        .btn-signin {
            width: 100%; margin-top: 0.5rem;
            display: flex; align-items: center; justify-content: center; gap: 0.55rem;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--accent-purple));
            color: #fff; border: none; border-radius: 12px;
            font-family: var(--font-body); font-size: 0.95rem; font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(108,99,255,0.4);
            transition: all var(--transition);
            position: relative; overflow: hidden;
        }
        .btn-signin::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0; transition: opacity 0.2s;
        }
        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 36px rgba(108,99,255,0.55);
        }
        .btn-signin:hover::after { opacity: 1; }
        .btn-signin:active { transform: translateY(0); }

        /* ── FOOTER NOTE ── */
        .card-footer {
            margin-top: 1.75rem; text-align: center;
            font-size: 0.78rem; color: var(--text-muted);
            display: flex; align-items: center; gap: 0.5rem;
            justify-content: center;
        }
        .card-footer i { color: var(--accent-green); }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .brand-panel { display: none; }
            .page { padding: 1.5rem; overflow: auto; }
            html, body { overflow: auto; }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-scene">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>

    <!-- Page -->
    <div class="page">

        <!-- Left: Branding Panel -->
        <div class="brand-panel">
            <div class="brand-logo">
                <div class="brand-icon-wrap">
                    <i class="fa-solid fa-square-poll-vertical"></i>
                </div>
                <div class="brand-name">Project<span>Pulse</span></div>
            </div>

            <div>
                <div class="brand-headline">
                    Your team's<br><em>project hub</em>,<br>all in one place.
                </div>
                <p class="brand-desc" style="margin-top: 1rem;">
                    Track progress, assign tasks, review UI designs, and deliver projects — with every role having their own tailored workspace.
                </p>
            </div>

            <div>
                <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); font-weight: 600; margin-bottom: 0.75rem;">
                    Five role-based portals
                </p>
                <div class="roles-grid">
                    <div class="role-chip pm"><i class="fa-solid fa-gauge-high"></i> Project Manager</div>
                    <div class="role-chip dev"><i class="fa-solid fa-code"></i> Developer</div>
                    <div class="role-chip design"><i class="fa-solid fa-palette"></i> Designer</div>
                    <div class="role-chip qa"><i class="fa-solid fa-vial-circle-check"></i> QA Engineer</div>
                    <div class="role-chip client" style="grid-column: span 2; justify-content: center;"><i class="fa-solid fa-briefcase"></i> Client Portal</div>
                </div>
            </div>
        </div>

        <!-- Right: Login Card -->
        <div class="login-card">
            <div class="card-header">
                <h2>Welcome back</h2>
                <p>Sign in with your username and password</p>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-top: 2px; flex-shrink: 0;"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            @if(session('success'))
                <div style="display:flex; align-items: center; gap: 0.65rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #34d399; border-radius: 12px; padding: 0.85rem 1rem; font-size: 0.88rem; margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-check" style="flex-shrink: 0;"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="login-form">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="form-control"
                            placeholder="Enter your username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                        >
                        <i class="fa-solid fa-user input-icon"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <i class="fa-solid fa-lock input-icon"></i>
                        <button type="button" class="toggle-pw" id="toggle-pw" title="Show/hide password">
                            <i class="fa-solid fa-eye" id="pw-eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-signin" id="signin-btn">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Sign In to Workspace
                </button>
            </form>

            <div class="card-footer">
                <i class="fa-solid fa-shield-halved"></i>
                Secured access — credentials issued by your Project Manager
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('toggle-pw');
        const pwInput   = document.getElementById('password');
        const eyeIcon   = document.getElementById('pw-eye-icon');

        toggleBtn.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            eyeIcon.className = isHidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });

        // Loading state on submit
        document.getElementById('login-form').addEventListener('submit', function () {
            const btn = document.getElementById('signin-btn');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Signing In...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
