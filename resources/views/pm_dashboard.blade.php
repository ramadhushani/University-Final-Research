<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM Dashboard | ProjectPulse</title>
    <meta name="description" content="ProjectPulse Project Manager Dashboard — manage projects, assign teams, track status.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* ──────────────────────────────────────────────────
           DESIGN TOKENS
        ────────────────────────────────────────────────── */
        :root {
            --bg-app:        #0b0d17;
            --bg-surface:    #111527;
            --bg-card:       #161a2e;
            --bg-input:      #1c2038;
            --border:        #252a42;
            --border-light:  #1e2338;

            --primary:       #6c63ff;
            --primary-glow:  rgba(108,99,255,0.25);
            --primary-dark:  #4f46e5;
            --accent-cyan:   #22d3ee;
            --accent-green:  #10b981;
            --accent-amber:  #f59e0b;
            --accent-rose:   #f43f5e;
            --accent-purple: #a855f7;

            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted:     #4a5568;

            --font-body:    'Inter', sans-serif;
            --font-heading: 'Space Grotesk', sans-serif;

            --radius-sm:  6px;
            --radius-md:  12px;
            --radius-lg:  18px;
            --radius-xl:  24px;

            --shadow-sm:  0 2px 8px rgba(0,0,0,0.3);
            --shadow-md:  0 4px 20px rgba(0,0,0,0.4);
            --shadow-lg:  0 8px 40px rgba(0,0,0,0.5);
            --shadow-glow: 0 0 24px var(--primary-glow);

            --transition: 0.2s ease;
            --navbar-h: 66px;
        }

        /* ──────────────────────────────────────────────────
           RESET & BASE
        ────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body);
            background: var(--bg-app);
            color: var(--text-primary);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }

        /* ──────────────────────────────────────────────────
           NAVBAR
        ────────────────────────────────────────────────── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            height: var(--navbar-h);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            background: rgba(11,13,23,0.85);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .brand {
            display: flex; align-items: center; gap: 0.6rem;
            font-family: var(--font-heading); font-weight: 800; font-size: 1.25rem;
            color: var(--text-primary); text-decoration: none;
        }
        .brand-icon { color: var(--primary); font-size: 1.4rem; }
        .brand-badge {
            background: var(--primary); color: #fff;
            font-size: 0.65rem; font-weight: 700; padding: 2px 8px;
            border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .user-pill {
            display: flex; align-items: center; gap: 0.7rem;
            background: var(--bg-card); border: 1px solid var(--border);
            padding: 0.4rem 0.9rem 0.4rem 0.4rem; border-radius: 40px;
        }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent-purple));
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem; color: #fff;
            flex-shrink: 0;
        }
        .user-details { line-height: 1.3; }
        .user-name { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
        .user-role { font-size: 0.72rem; color: var(--text-muted); }
        .logout-btn {
            background: rgba(244,63,94,0.12); border: 1px solid rgba(244,63,94,0.3);
            color: var(--accent-rose); padding: 0.45rem 0.85rem; border-radius: var(--radius-sm);
            cursor: pointer; font-size: 0.8rem; font-weight: 600; transition: var(--transition);
            display: flex; align-items: center; gap: 0.35rem;
        }
        .logout-btn:hover {
            background: rgba(244,63,94,0.22); border-color: var(--accent-rose);
        }

        /* ──────────────────────────────────────────────────
           LAYOUT
        ────────────────────────────────────────────────── */
        .page-wrap {
            padding-top: calc(var(--navbar-h) + 2rem);
            padding-bottom: 4rem;
        }
        .container { max-width: 1440px; margin: 0 auto; padding: 0 2rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 {
            font-family: var(--font-heading); font-size: 1.75rem;
            font-weight: 800; color: var(--text-primary);
            display: flex; align-items: center; gap: 0.6rem;
        }
        .page-header p { color: var(--text-secondary); margin-top: 0.35rem; font-size: 0.9rem; }

        /* ──────────────────────────────────────────────────
           ALERTS
        ────────────────────────────────────────────────── */
        .alert {
            display: flex; align-items: flex-start; gap: 0.75rem;
            padding: 1rem 1.25rem; border-radius: var(--radius-md);
            margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;
            animation: slideDown 0.3s ease;
        }
        .alert-success {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.35);
            color: #34d399;
        }
        .alert-error {
            background: rgba(244,63,94,0.12);
            border: 1px solid rgba(244,63,94,0.35);
            color: #fb7185;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ──────────────────────────────────────────────────
           STATS ROW
        ────────────────────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            transition: var(--transition); position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; inset: 0;
            opacity: 0; transition: opacity 0.3s;
        }
        .stat-card:hover::before { opacity: 1; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-icon {
            width: 46px; height: 46px; border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .stat-icon.purple { background: rgba(108,99,255,0.15); color: var(--primary); }
        .stat-icon.cyan   { background: rgba(34,211,238,0.12); color: var(--accent-cyan); }
        .stat-icon.green  { background: rgba(16,185,129,0.12); color: var(--accent-green); }
        .stat-icon.amber  { background: rgba(245,158,11,0.12); color: var(--accent-amber); }
        .stat-num { font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.78rem; color: var(--text-secondary); margin-top: 3px; font-weight: 500; }

        /* ──────────────────────────────────────────────────
           TWO-COLUMN MAIN LAYOUT
        ────────────────────────────────────────────────── */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            align-items: start;
        }

        /* ──────────────────────────────────────────────────
           CARDS
        ────────────────────────────────────────────────── */
        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.75rem;
        }
        .card + .card { margin-top: 1.5rem; }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;
        }
        .card-title {
            font-family: var(--font-heading); font-size: 1.1rem; font-weight: 700;
            color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;
        }
        .card-title i { color: var(--primary); }

        /* ──────────────────────────────────────────────────
           CHART CARD
        ────────────────────────────────────────────────── */
        .chart-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.75rem;
            margin-bottom: 1.5rem;
        }
        .chart-wrap {
            display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;
        }
        .chart-canvas-wrap {
            position: relative; width: 200px; height: 200px; flex-shrink: 0;
        }
        .chart-center-text {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            text-align: center; pointer-events: none;
        }
        .chart-center-num {
            font-family: var(--font-heading); font-size: 1.8rem; font-weight: 800;
            color: var(--text-primary); line-height: 1;
        }
        .chart-center-label { font-size: 0.72rem; color: var(--text-muted); }
        .chart-legend { flex: 1; display: flex; flex-direction: column; gap: 0.6rem; min-width: 160px; }
        .legend-item { display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .legend-label { color: var(--text-secondary); flex: 1; }
        .legend-count { font-weight: 700; color: var(--text-primary); }

        /* ──────────────────────────────────────────────────
           PROJECT CARDS
        ────────────────────────────────────────────────── */
        .project-list { display: flex; flex-direction: column; gap: 1.25rem; }
        .project-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 1.5rem;
            transition: box-shadow var(--transition), border-color var(--transition);
        }
        .project-card:hover {
            box-shadow: var(--shadow-md); border-color: rgba(108,99,255,0.3);
        }
        .project-card-header {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
            margin-bottom: 0.75rem;
        }
        .project-id { font-size: 0.72rem; color: var(--text-muted); font-family: monospace; }
        .project-title {
            font-family: var(--font-heading); font-size: 1.05rem; font-weight: 700;
            color: var(--text-primary); line-height: 1.3;
        }
        .project-desc { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; margin-top: 0.35rem; }
        .project-actions { display: flex; gap: 0.5rem; flex-shrink: 0; }

        /* Progress bar */
        .progress-wrap {
            background: var(--bg-input); border-radius: 99px;
            height: 6px; margin: 1rem 0 0.4rem; overflow: hidden;
        }
        .progress-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, var(--primary), var(--accent-cyan));
            transition: width 0.5s ease;
        }
        .progress-fill.green { background: linear-gradient(90deg, var(--accent-green), var(--accent-cyan)); }
        .progress-meta {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.78rem; color: var(--text-muted);
        }

        /* Team pills row */
        .team-row {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1rem; margin-top: 1.25rem;
            padding-top: 1.25rem; border-top: 1px solid var(--border);
        }
        .team-section-label {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); font-weight: 600; margin-bottom: 0.5rem;
            display: flex; align-items: center; gap: 0.35rem;
        }
        .pill-group { display: flex; flex-wrap: wrap; gap: 0.35rem; }

        /* Status badge */
        .status-badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700;
        }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: block; }
        .s-inprogress  { background: rgba(108,99,255,0.15); color: var(--primary); }
        .s-inprogress::before  { background: var(--primary); }
        .s-uipending   { background: rgba(245,158,11,0.12); color: var(--accent-amber); }
        .s-uipending::before   { background: var(--accent-amber); }
        .s-pendingqa   { background: rgba(34,211,238,0.12); color: var(--accent-cyan); }
        .s-pendingqa::before   { background: var(--accent-cyan); }
        .s-finished    { background: rgba(16,185,129,0.12); color: var(--accent-green); }
        .s-finished::before    { background: var(--accent-green); }

        /* Pill badges */
        .pill {
            display: inline-block; padding: 0.2rem 0.55rem; border-radius: 20px;
            font-size: 0.72rem; font-weight: 600;
        }
        .pill-blue   { background: rgba(34,211,238,0.12); color: var(--accent-cyan); }
        .pill-purple { background: rgba(168,85,247,0.12); color: var(--accent-purple); }
        .pill-green  { background: rgba(16,185,129,0.12); color: var(--accent-green); }
        .pill-amber  { background: rgba(245,158,11,0.12); color: var(--accent-amber); }
        .pill-gray   { background: rgba(148,163,184,0.1); color: var(--text-secondary); }

        /* ──────────────────────────────────────────────────
           TASKS
        ────────────────────────────────────────────────── */
        .tasks-section {
            margin-top: 1.25rem; padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .section-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 0.75rem; gap: 0.5rem;
        }
        .section-head-label {
            font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);
            display: flex; align-items: center; gap: 0.35rem;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .task-item {
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-input); border: 1px solid var(--border-light);
            padding: 0.65rem 0.9rem; border-radius: var(--radius-sm);
            margin-bottom: 0.4rem; gap: 0.5rem;
        }
        .task-info { flex: 1; min-width: 0; }
        .task-title { font-size: 0.88rem; font-weight: 600; color: var(--text-primary); }
        .task-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
        .task-status { flex-shrink: 0; }
        .ts-pending    { background: rgba(245,158,11,0.12); color: var(--accent-amber); }
        .ts-completed  { background: rgba(16,185,129,0.12); color: var(--accent-green); }
        .ts-qaapproved { background: rgba(34,211,238,0.12); color: var(--accent-cyan); }

        /* ──────────────────────────────────────────────────
           COMMENTS
        ────────────────────────────────────────────────── */
        .comments-section {
            margin-top: 1.25rem; padding-top: 1.25rem;
            border-top: 1px solid var(--border);
        }
        .comment-feed {
            max-height: 200px; overflow-y: auto; margin-bottom: 0.75rem;
            display: flex; flex-direction: column; gap: 0.5rem;
            padding-right: 0.25rem;
        }
        .comment-feed::-webkit-scrollbar { width: 4px; }
        .comment-feed::-webkit-scrollbar-track { background: transparent; }
        .comment-feed::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        .comment-item {
            background: var(--bg-input); border-left: 3px solid var(--primary);
            padding: 0.6rem 0.85rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            font-size: 0.82rem;
        }
        .comment-meta {
            display: flex; justify-content: space-between; margin-bottom: 0.25rem;
        }
        .comment-author { font-weight: 700; color: var(--text-primary); }
        .comment-time   { font-size: 0.72rem; color: var(--text-muted); }
        .comment-text   { color: var(--text-secondary); line-height: 1.5; }
        .comment-form {
            display: flex; gap: 0.5rem;
        }

        /* ──────────────────────────────────────────────────
           MARK FINISHED BUTTON
        ────────────────────────────────────────────────── */
        .finish-bar {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.25);
            border-radius: var(--radius-md); padding: 0.85rem 1.1rem;
            margin-top: 1.25rem; gap: 1rem; flex-wrap: wrap;
        }
        .finish-bar-text { font-size: 0.85rem; color: var(--text-secondary); }
        .finish-bar-text strong { color: var(--accent-green); }

        /* ──────────────────────────────────────────────────
           INLINE FORMS (Task / Comment toggle)
        ────────────────────────────────────────────────── */
        .inline-form-wrap {
            display: none; background: var(--bg-surface); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 1.1rem; margin-bottom: 0.75rem;
        }
        .inline-form-wrap.open { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ──────────────────────────────────────────────────
           RIGHT PANEL — NEW PROJECT FORM
        ────────────────────────────────────────────────── */
        .sticky-panel { position: sticky; top: calc(var(--navbar-h) + 1.5rem); }
        .panel-title {
            font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800;
            color: var(--text-primary); margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .panel-title i { color: var(--primary); }

        /* ──────────────────────────────────────────────────
           FORM ELEMENTS
        ────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1rem; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: var(--text-secondary); text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 0.35rem;
        }
        .form-control {
            width: 100%; background: var(--bg-input); border: 1px solid var(--border);
            color: var(--text-primary); padding: 0.6rem 0.875rem;
            border-radius: var(--radius-sm); font-size: 0.9rem; font-family: var(--font-body);
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.18);
        }
        .form-control::placeholder { color: var(--text-muted); }
        textarea.form-control { min-height: 80px; resize: vertical; }
        select.form-control option { background: var(--bg-surface); }

        /* Checkbox grid */
        .checkbox-grid { display: flex; flex-direction: column; gap: 0.4rem; max-height: 130px; overflow-y: auto; }
        .checkbox-grid::-webkit-scrollbar { width: 4px; }
        .checkbox-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
        .checkbox-label {
            display: flex; align-items: center; gap: 0.6rem;
            font-size: 0.85rem; color: var(--text-secondary);
            cursor: pointer; padding: 0.35rem 0.5rem; border-radius: var(--radius-sm);
            transition: background var(--transition);
        }
        .checkbox-label:hover { background: var(--bg-input); color: var(--text-primary); }
        .checkbox-label input[type="checkbox"] { accent-color: var(--primary); width: 14px; height: 14px; cursor: pointer; }

        /* Source toggle */
        .source-toggle {
            display: flex; background: var(--bg-input); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 3px; margin-bottom: 0.75rem;
        }
        .toggle-opt {
            flex: 1; text-align: center; padding: 0.4rem; border-radius: 4px;
            font-size: 0.8rem; font-weight: 600; cursor: pointer;
            color: var(--text-muted); transition: all var(--transition);
        }
        .toggle-opt.active {
            background: var(--primary); color: #fff;
            box-shadow: 0 2px 8px rgba(108,99,255,0.35);
        }

        /* Divider */
        .form-divider {
            height: 1px; background: var(--border); margin: 1.25rem 0;
        }
        .section-title {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            font-weight: 700; color: var(--text-muted); margin-bottom: 0.75rem;
            display: flex; align-items: center; gap: 0.4rem;
        }

        /* ──────────────────────────────────────────────────
           BUTTONS
        ────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.45rem;
            padding: 0.55rem 1.1rem; border-radius: var(--radius-sm);
            font-size: 0.85rem; font-weight: 600; cursor: pointer;
            border: none; font-family: var(--font-body);
            transition: all var(--transition); text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--accent-purple));
            color: #fff; box-shadow: 0 2px 12px rgba(108,99,255,0.35);
        }
        .btn-primary:hover {
            transform: translateY(-1px); box-shadow: 0 4px 20px rgba(108,99,255,0.5);
        }
        .btn-success {
            background: linear-gradient(135deg, var(--accent-green), #06b6d4);
            color: #fff; box-shadow: 0 2px 12px rgba(16,185,129,0.3);
        }
        .btn-success:hover {
            transform: translateY(-1px); box-shadow: 0 4px 20px rgba(16,185,129,0.45);
        }
        .btn-secondary {
            background: var(--bg-input); border: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .btn-secondary:hover { color: var(--text-primary); border-color: var(--text-muted); }
        .btn-danger {
            background: rgba(244,63,94,0.12); border: 1px solid rgba(244,63,94,0.3);
            color: var(--accent-rose);
        }
        .btn-danger:hover { background: rgba(244,63,94,0.2); }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.78rem; }
        .btn-block { width: 100%; justify-content: center; margin-top: 1rem; }

        /* ──────────────────────────────────────────────────
           EMPTY STATE
        ────────────────────────────────────────────────── */
        .empty-state {
            text-align: center; padding: 3rem 1.5rem;
            background: var(--bg-card); border: 1px dashed var(--border);
            border-radius: var(--radius-lg);
        }
        .empty-state-icon { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 0.75rem; }
        .empty-state-text { font-size: 0.9rem; color: var(--text-secondary); max-width: 320px; margin: 0 auto; }

        /* ──────────────────────────────────────────────────
           PROPOSAL FILE DISPLAY
        ────────────────────────────────────────────────── */
        .proposal-link {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: rgba(108,99,255,0.1); border: 1px solid rgba(108,99,255,0.3);
            color: var(--primary); padding: 0.35rem 0.75rem; border-radius: var(--radius-sm);
            font-size: 0.8rem; font-weight: 600; transition: all var(--transition);
        }
        .proposal-link:hover { background: rgba(108,99,255,0.2); }

        /* ──────────────────────────────────────────────────
           RESPONSIVE
        ────────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .main-grid { grid-template-columns: 1fr; }
            .sticky-panel { position: static; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .stats-row { grid-template-columns: 1fr 1fr; }
            .team-row  { grid-template-columns: 1fr; }
            .chart-wrap { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <!-- ── NAVBAR ──────────────────────────────────────────────────── -->
    <nav class="navbar">
        <a href="{{ route('pm.dashboard') }}" class="brand" id="brand-link">
            <i class="fa-solid fa-square-poll-vertical brand-icon"></i>
            <span>ProjectPulse <span class="brand-badge">Manager</span></span>
        </a>
        <div class="nav-right">
            <div class="user-pill">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Project Manager</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn" id="logout-btn">
                    <i class="fa-solid fa-power-off"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- ── PAGE WRAP ───────────────────────────────────────────────── -->
    <div class="page-wrap">
        <div class="container">

            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fa-solid fa-gauge-high" style="color:var(--primary);"></i> PM Control Center</h1>
                <p>Manage projects, assign teams, track progress, and sign off on deliverables.</p>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success" id="flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" id="flash-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-error" id="flash-validation">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <strong style="display:block;margin-bottom:0.25rem;">Please fix the following:</strong>
                        <ul style="list-style:disc;padding-left:1rem;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if(isset($notifications) && $notifications->isNotEmpty())
                <div class="alert alert-info" style="background: rgba(34,211,238,0.12); border: 1px solid rgba(34,211,238,0.35); color: var(--accent-cyan);">
                    <i class="fa-solid fa-bell"></i>
                    <div>
                        <strong style="display:block;margin-bottom:0.25rem;">Recent Notifications:</strong>
                        <ul style="list-style:disc;padding-left:1rem;">
                            @foreach($notifications as $notification)
                                <li>{{ $notification->message }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- ── STATS ROW ──────────────────────────────────────── -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="fa-solid fa-folder-tree"></i></div>
                    <div>
                        <div class="stat-num">{{ $projects->count() }}</div>
                        <div class="stat-label">Total Projects</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fa-solid fa-spinner"></i></div>
                    <div>
                        <div class="stat-num">{{ $projects->whereIn('status', ['In-Progress','UI Pending','Pending QA'])->count() }}</div>
                        <div class="stat-label">Active Projects</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="stat-num">{{ $projects->where('status', 'Finished')->count() }}</div>
                        <div class="stat-label">Finished</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <div class="stat-num">{{ $projects->sum(fn($p) => $p->tasks->count()) }}</div>
                        <div class="stat-label">Total Tasks</div>
                    </div>
                </div>
            </div>

            <!-- ── MAIN GRID ──────────────────────────────────────── -->
            <div class="main-grid">

                <!-- ── LEFT COLUMN ────────────────────────────────── -->
                <div>

                    <!-- Chart Card -->
                    <div class="chart-card">
                        <div class="card-header" style="margin-bottom:1.25rem;">
                            <span class="card-title"><i class="fa-solid fa-chart-pie"></i> Project Status Breakdown</span>
                            <span style="font-size:0.8rem;color:var(--text-muted);">{{ $projects->count() }} total projects</span>
                        </div>
                        <div class="chart-wrap">
                            <div class="chart-canvas-wrap">
                                <canvas id="statusChart" width="200" height="200"></canvas>
                                <div class="chart-center-text">
                                    <div class="chart-center-num">{{ $projects->count() }}</div>
                                    <div class="chart-center-label">Projects</div>
                                </div>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <div class="legend-dot" style="background:#10b981;"></div>
                                    <span class="legend-label">Finished</span>
                                    <span class="legend-count">{{ $statusCounts['Finished'] }}</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot" style="background:#6c63ff;"></div>
                                    <span class="legend-label">In-Progress</span>
                                    <span class="legend-count">{{ $statusCounts['In-Progress'] }}</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot" style="background:#f59e0b;"></div>
                                    <span class="legend-label">UI Pending</span>
                                    <span class="legend-count">{{ $statusCounts['UI Pending'] }}</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot" style="background:#22d3ee;"></div>
                                    <span class="legend-label">Pending QA</span>
                                    <span class="legend-count">{{ $statusCounts['Pending QA'] }}</span>
                                </div>
                            </div>
                        </div>

                        <hr style="border:0; border-top:1px solid var(--border); margin: 2rem 0;">

                        <div class="card-header" style="margin-bottom:1.25rem;">
                            <span class="card-title"><i class="fa-solid fa-chart-column"></i> Task Status Breakdown</span>
                        </div>
                        <div class="chart-wrap" style="display: block;">
                            <div style="position: relative; height: 250px; width: 100%;">
                                <canvas id="tasksChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Projects List -->
                    <div class="card-header">
                        <span class="card-title" style="font-size:1.2rem;">
                            <i class="fa-solid fa-folder-open"></i> Active Projects
                        </span>
                    </div>

                    @if($projects->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fa-solid fa-folder-open"></i></div>
                            <p class="empty-state-text">No projects yet. Use the panel on the right to launch your first project.</p>
                        </div>
                    @else
                        <div class="project-list">
                            @foreach($projects as $project)
                                @php
                                    $total      = $project->tasks->count();
                                    $done       = $project->tasks->whereIn('status', ['Completed', 'QA Approved'])->count();
                                    $pct        = $total > 0 ? round(($done / $total) * 100) : 0;
                                    $canFinish  = $project->status !== 'Finished'
                                                  && $total > 0
                                                  && $project->tasks->whereNotIn('status', ['Completed','QA Approved'])->count() === 0;

                                    $statusClass = match($project->status) {
                                        'In-Progress' => 's-inprogress',
                                        'UI Pending'  => 's-uipending',
                                        'Pending QA'  => 's-pendingqa',
                                        'Finished'    => 's-finished',
                                        default       => 's-inprogress',
                                    };
                                @endphp

                                <div class="project-card" id="project-{{ $project->id }}">
                                    <!-- Card Header -->
                                    <div class="project-card-header">
                                        <div>
                                            <div class="project-id">PROJ-{{ 1000 + $project->id }}</div>
                                            <div class="project-title">{{ $project->title }}</div>
                                            @if($project->description)
                                                <div class="project-desc">{{ Str::limit($project->description, 120) }}</div>
                                            @endif
                                        </div>
                                        <div class="project-actions">
                                            <span class="status-badge {{ $statusClass }}">{{ $project->status }}</span>
                                            <form action="{{ route('pm.projects.delete', $project) }}" method="POST" style="margin:0;"
                                                  onsubmit="return confirm('Delete project \'{{ addslashes($project->title) }}\'? All tasks and comments will be permanently removed.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" id="delete-project-{{ $project->id }}" title="Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Proposal + UI link -->
                                    <div style="display:flex;gap:0.6rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                                        @if($project->proposal_file_path)
                                            <a href="{{ route('pm.projects.proposal', $project) }}" class="proposal-link" id="proposal-{{ $project->id }}">
                                                <i class="fa-solid fa-file-pdf"></i> Proposal PDF
                                            </a>
                                        @endif
                                        @if($project->ui_link)
                                            <a href="{{ $project->ui_link }}" target="_blank" class="proposal-link" style="border-color:rgba(168,85,247,0.3);color:var(--accent-purple);background:rgba(168,85,247,0.1);" id="uilink-{{ $project->id }}">
                                                <i class="fa-solid fa-palette"></i> UI Design
                                                <span class="pill pill-{{ $project->ui_status === 'Accepted' ? 'green' : ($project->ui_status === 'Rejected' ? 'amber' : 'gray') }}" style="margin-left:0.25rem;">
                                                    {{ $project->ui_status }}
                                                </span>
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Progress -->
                                    <div class="progress-wrap">
                                        <div class="progress-fill {{ $pct === 100 ? 'green' : '' }}" style="width:{{ $pct }}%;"></div>
                                    </div>
                                    <div class="progress-meta">
                                        <span>{{ $done }} / {{ $total }} tasks done</span>
                                        <span>{{ $pct }}% complete</span>
                                    </div>

                                    <!-- Status Update -->
                                    <div style="margin-top:0.9rem;">
                                        <form action="{{ route('pm.projects.status', $project) }}" method="POST" style="display:flex;gap:0.5rem;align-items:center;" id="status-form-{{ $project->id }}">
                                            @csrf
                                            <select name="status" class="form-control" style="flex:1;font-size:0.82rem;padding:0.4rem 0.65rem;">
                                                @foreach(['In-Progress','UI Pending','Pending QA','Finished'] as $st)
                                                    <option value="{{ $st }}" {{ $project->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                                        </form>
                                    </div>

                                    <!-- Team Row -->
                                    <div class="team-row">
                                        @php
                                            $teamByRole = $project->users->groupBy('role');
                                        @endphp
                                        <div>
                                            <div class="team-section-label"><i class="fa-solid fa-user-tie"></i> Client</div>
                                            <div class="pill-group">
                                                @if($project->client)
                                                    <span class="pill pill-amber" title="{{ $project->client->username }}">{{ $project->client->name }}</span>
                                                @else
                                                    <span class="pill pill-gray">None</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="team-section-label"><i class="fa-solid fa-laptop-code"></i> Developers</div>
                                            <div class="pill-group">
                                                @forelse($teamByRole->get('developer', collect()) as $u)
                                                    <span class="pill pill-blue" title="{{ $u->username }}">{{ $u->name }}</span>
                                                @empty
                                                    <span class="pill pill-gray">None</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div>
                                            <div class="team-section-label"><i class="fa-solid fa-pen-ruler"></i> Designers</div>
                                            <div class="pill-group">
                                                @forelse($teamByRole->get('designer', collect()) as $u)
                                                    <span class="pill pill-purple" title="{{ $u->username }}">{{ $u->name }}</span>
                                                @empty
                                                    <span class="pill pill-gray">None</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div>
                                            <div class="team-section-label"><i class="fa-solid fa-vial-circle-check"></i> QA</div>
                                            <div class="pill-group">
                                                @forelse($teamByRole->get('qa', collect()) as $u)
                                                    <span class="pill pill-green" title="{{ $u->username }}">{{ $u->name }}</span>
                                                @empty
                                                    <span class="pill pill-gray">None</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tasks -->
                                    <div class="tasks-section">
                                        <div class="section-head">
                                            <span class="section-head-label">
                                                <i class="fa-solid fa-list-check" style="color:var(--primary);"></i>
                                                Tasks ({{ $project->tasks->count() }})
                                            </span>
                                            <button type="button" class="btn btn-secondary btn-sm" id="toggle-task-btn-{{ $project->id }}"
                                                    onclick="togglePanel('task-form-{{ $project->id }}', this)">
                                                <i class="fa-solid fa-plus"></i> Add Task
                                            </button>
                                        </div>

                                        <!-- Task Form -->
                                        <div class="inline-form-wrap" id="task-form-{{ $project->id }}">
                                            <form action="{{ route('pm.tasks.store', $project) }}" method="POST" id="task-submit-form-{{ $project->id }}">
                                                @csrf
                                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
                                                    <div class="form-group" style="margin:0;">
                                                        <label class="form-label">Task Title</label>
                                                        <input type="text" name="title" class="form-control" placeholder="E.g. Build Auth API" required>
                                                    </div>
                                                    <div class="form-group" style="margin:0;">
                                                        <label class="form-label">Assign To</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">— Unassigned —</option>
                                                            @foreach($project->users->whereNotIn('role', ['client']) as $member)
                                                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
                                                    <div class="form-group" style="margin:0;">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-control">
                                                            <option value="Pending">Pending</option>
                                                            <option value="Completed">Completed</option>
                                                            <option value="QA Approved">QA Approved</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group" style="margin:0;">
                                                        <label class="form-label">Description (optional)</label>
                                                        <input type="text" name="description" class="form-control" placeholder="Brief task note...">
                                                    </div>
                                                </div>
                                                <div style="display:flex;justify-content:flex-end;gap:0.5rem;">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                            onclick="togglePanel('task-form-{{ $project->id }}', document.getElementById('toggle-task-btn-{{ $project->id }}'))">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Assign Task</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Task List -->
                                        @if($project->tasks->isEmpty())
                                            <p style="font-size:0.82rem;color:var(--text-muted);font-style:italic;">No tasks yet.</p>
                                        @else
                                            @foreach($project->tasks as $task)
                                                @php
                                                    $tsClass = match($task->status) {
                                                        'Completed'   => 'ts-completed',
                                                        'QA Approved' => 'ts-qaapproved',
                                                        default       => 'ts-pending',
                                                    };
                                                @endphp
                                                <div class="task-item">
                                                    <div class="task-info">
                                                        <div class="task-title">{{ $task->title }}</div>
                                                        <div class="task-sub">
                                                            {{ $task->assignedUser?->name ?? 'Unassigned' }}
                                                            &nbsp;·&nbsp; {{ $task->updated_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    <span class="pill {{ $tsClass }} task-status">{{ $task->status }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <!-- Comments -->
                                    <div class="comments-section">
                                        <div class="section-head">
                                            <span class="section-head-label">
                                                <i class="fa-solid fa-comments" style="color:var(--primary);"></i>
                                                Activity ({{ $project->comments->count() }})
                                            </span>
                                        </div>

                                        @if($project->comments->isNotEmpty())
                                            <div class="comment-feed">
                                                @foreach($project->comments->sortByDesc('created_at')->take(5) as $comm)
                                                    <div class="comment-item">
                                                        <div class="comment-meta">
                                                            <span class="comment-author">
                                                                {{ $comm->user->name }}
                                                                <span class="pill pill-gray" style="font-size:0.65rem;">{{ ucfirst($comm->user->role) }}</span>
                                                            </span>
                                                            <span class="comment-time">{{ $comm->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div class="comment-text">{{ $comm->message }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <form action="{{ route('chat.store', $project) }}" method="POST" class="comment-form" id="comment-form-{{ $project->id }}">
                                            @csrf
                                            <input type="text" name="message" class="form-control" style="font-size:0.82rem;" placeholder="Post update or reply..." required>
                                            <button type="submit" class="btn btn-secondary btn-sm" style="white-space:nowrap;">
                                                <i class="fa-solid fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- ── MARK AS FINISHED ── -->
                                    @if($project->status !== 'Finished')
                                        <div class="finish-bar" id="finish-bar-{{ $project->id }}">
                                            <div class="finish-bar-text">
                                                @if($canFinish)
                                                    <strong>All tasks complete!</strong> Ready to sign off on this project.
                                                @else
                                                    <strong>{{ $project->tasks->whereNotIn('status', ['Completed','QA Approved'])->count() }}</strong> task(s) still pending — complete all to enable final sign-off.
                                                @endif
                                            </div>
                                            <form action="{{ route('pm.projects.finish', $project) }}" method="POST" style="margin:0;"
                                                  onsubmit="return confirm('Mark \'{{ addslashes($project->title) }}\' as FINISHED? The client will be notified via email.');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" id="finish-btn-{{ $project->id }}" {{ !$canFinish ? 'disabled' : '' }}>
                                                    <i class="fa-solid fa-flag-checkered"></i> Mark as Finished
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div style="margin-top:1rem;text-align:center;font-size:0.82rem;color:var(--accent-green);font-weight:700;">
                                            <i class="fa-solid fa-circle-check"></i> This project has been signed off as Finished.
                                        </div>
                                    @endif

                                </div><!-- /.project-card -->
                            @endforeach
                        </div>
                    @endif

                </div><!-- /.left column -->

                <!-- ── RIGHT COLUMN: New Project Form ─────────────── -->
                <div class="sticky-panel">
                    <div class="card">
                        <div class="panel-title">
                            <i class="fa-solid fa-folder-plus"></i> Launch New Project
                        </div>

                        <form action="{{ route('pm.projects.store') }}" method="POST" enctype="multipart/form-data" id="new-project-form">
                            @csrf

                            <!-- Project Info -->
                            <div class="section-title"><i class="fa-solid fa-info-circle"></i> Project Details</div>

                            <div class="form-group">
                                <label for="title" class="form-label">Project Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                       placeholder="E.g. E-Commerce Platform v2" value="{{ old('title') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control"
                                          placeholder="Project goals and deliverables...">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="proposal_file" class="form-label">Proposal PDF (optional)</label>
                                <input type="file" name="proposal_file" id="proposal_file" class="form-control"
                                       accept=".pdf" style="padding:0.5rem;">
                            </div>

                            <div class="form-divider"></div>

                            <!-- Client -->
                            <div class="section-title"><i class="fa-solid fa-user-tie"></i> 1. Client</div>
                            <div class="source-toggle">
                                <div class="toggle-opt active" id="toggle-client-new" onclick="setSource('client','new')">New Client</div>
                                <div class="toggle-opt" id="toggle-client-existing" onclick="setSource('client','existing')">Existing</div>
                            </div>
                            <input type="hidden" name="client_source" id="client_source" value="{{ old('client_source','new') }}">

                            <div id="client-new-fields">
                                <div class="form-group">
                                    <label for="client_username" class="form-label">Username</label>
                                    <input type="text" name="client_username" id="client_username" class="form-control"
                                           placeholder="client_apex" value="{{ old('client_username') }}">
                                </div>
                                <div class="form-group">
                                    <label for="client_name" class="form-label">Display Name</label>
                                    <input type="text" name="client_name" id="client_name" class="form-control"
                                           placeholder="Apex Corp Admin" value="{{ old('client_name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="client_email" class="form-label">Email (for notifications)</label>
                                    <input type="email" name="client_email" id="client_email" class="form-control"
                                           placeholder="client@company.com" value="{{ old('client_email') }}">
                                </div>
                                <div class="form-group">
                                    <label for="client_password" class="form-label">Login Password</label>
                                    <input type="password" name="client_password" id="client_password" class="form-control"
                                           placeholder="••••••••">
                                </div>
                            </div>
                            <div id="client-existing-fields" style="display:none;">
                                <div class="form-group">
                                    <label for="client_id" class="form-label">Select Client</label>
                                    <select name="client_id" id="client_id" class="form-control">
                                        <option value="">— Choose Client —</option>
                                        @foreach($existingClients as $c)
                                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-divider"></div>

                            <!-- Developers -->
                            <div class="section-title"><i class="fa-solid fa-laptop-code"></i> 2. Developers</div>
                            @if($existingDevelopers->isNotEmpty())
                                <div class="form-group">
                                    <label class="form-label">Assign Developer(s)</label>
                                    <div class="checkbox-grid">
                                        @foreach($existingDevelopers as $dev)
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="developer_ids[]" value="{{ $dev->id }}"
                                                       {{ is_array(old('developer_ids')) && in_array($dev->id, old('developer_ids')) ? 'checked' : '' }}>
                                                <span>{{ $dev->name }} <span style="color:var(--text-muted);font-size:0.75rem;">({{ $dev->username }})</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="form-divider"></div>

                            <!-- Designers -->
                            <div class="section-title"><i class="fa-solid fa-pen-ruler"></i> 3. Designers</div>
                            @if($existingDesigners->isNotEmpty())
                                <div class="form-group">
                                    <label class="form-label">Assign Designer(s)</label>
                                    <div class="checkbox-grid">
                                        @foreach($existingDesigners as $des)
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="designer_ids[]" value="{{ $des->id }}"
                                                       {{ is_array(old('designer_ids')) && in_array($des->id, old('designer_ids')) ? 'checked' : '' }}>
                                                <span>{{ $des->name }} <span style="color:var(--text-muted);font-size:0.75rem;">({{ $des->username }})</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.75rem;">No designers in the system yet.</p>
                            @endif

                            <div class="form-divider"></div>

                            <!-- QA -->
                            <div class="section-title"><i class="fa-solid fa-vial-circle-check"></i> 4. QA Engineers</div>
                            @if($existingQas->isNotEmpty())
                                <div class="form-group">
                                    <label class="form-label">Assign QA Engineer(s)</label>
                                    <div class="checkbox-grid">
                                        @foreach($existingQas as $qa)
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="qa_ids[]" value="{{ $qa->id }}"
                                                       {{ is_array(old('qa_ids')) && in_array($qa->id, old('qa_ids')) ? 'checked' : '' }}>
                                                <span>{{ $qa->name }} <span style="color:var(--text-muted);font-size:0.75rem;">({{ $qa->username }})</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.75rem;">No QA engineers in the system yet.</p>
                            @endif

                            <button type="submit" class="btn btn-primary btn-block" id="launch-project-btn">
                                <i class="fa-solid fa-rocket"></i> Launch Project & Send Emails
                            </button>
                        </form>
                    </div>
                </div><!-- /.sticky-panel -->

            </div><!-- /.main-grid -->
        </div><!-- /.container -->
    </div><!-- /.page-wrap -->

    <!-- ── CHART.JS + UI SCRIPTS ──────────────────────────────────────── -->
    <script>
    // ── Donut Chart ────────────────────────────────────────────────────
    (function () {
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;

        const data = {
            finished:   {{ $statusCounts['Finished'] }},
            inProgress: {{ $statusCounts['In-Progress'] }},
            uiPending:  {{ $statusCounts['UI Pending'] }},
            pendingQa:  {{ $statusCounts['Pending QA'] }},
        };
        const total = data.finished + data.inProgress + data.uiPending + data.pendingQa;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Finished', 'In-Progress', 'UI Pending', 'Pending QA'],
                datasets: [{
                    data: [data.finished, data.inProgress, data.uiPending, data.pendingQa],
                    backgroundColor: ['#10b981', '#6c63ff', '#f59e0b', '#22d3ee'],
                    borderColor: '#161a2e',
                    borderWidth: 3,
                    hoverOffset: 8,
                }]
            },
            options: {
                cutout: '72%',
                responsive: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.raw} (${total > 0 ? Math.round(ctx.raw / total * 100) : 0}%)`
                        },
                        backgroundColor: '#1a1d2e',
                        borderColor: '#252a42',
                        borderWidth: 1,
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        padding: 10,
                    }
                }
            }
        });
    })();

    // ── Bar Chart ──────────────────────────────────────────────────────
    (function () {
        const ctx = document.getElementById('tasksChart');
        if (!ctx) return;

        const data = {
            pending: {{ $taskCounts['Pending'] }},
            completed: {{ $taskCounts['Completed'] }},
            qaApproved: {{ $taskCounts['QA Approved'] }},
        };

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Completed (Pending QA)', 'QA Approved'],
                datasets: [{
                    label: 'Tasks',
                    data: [data.pending, data.completed, data.qaApproved],
                    backgroundColor: ['#f59e0b', '#22d3ee', '#10b981'],
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8' },
                        grid: { color: '#252a42' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1d2e',
                        borderColor: '#252a42',
                        borderWidth: 1,
                        titleColor: '#f1f5f9',
                        bodyColor: '#94a3b8',
                        padding: 10,
                    }
                }
            }
        });
    })();

    // ── Client Source Toggle ────────────────────────────────────────────
    function setSource(type, source) {
        const hiddenInput = document.getElementById(type + '_source');
        const newBtn      = document.getElementById('toggle-' + type + '-new');
        const existBtn    = document.getElementById('toggle-' + type + '-existing');
        const newFields   = document.getElementById(type + '-new-fields');
        const existFields = document.getElementById(type + '-existing-fields');

        hiddenInput.value = source;
        if (source === 'new') {
            newBtn.classList.add('active');
            existBtn.classList.remove('active');
            newFields.style.display   = 'block';
            existFields.style.display = 'none';
        } else {
            newBtn.classList.remove('active');
            existBtn.classList.add('active');
            newFields.style.display   = 'none';
            existFields.style.display = 'block';
        }
    }

    // ── Inline Panel Toggle (Tasks / etc) ──────────────────────────────
    function togglePanel(id, triggerEl) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('open');
    }

    // ── Restore client_source on validation failure ─────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const src = document.getElementById('client_source')?.value || 'new';
        setSource('client', src);

        // Auto-dismiss flash messages after 5s
        ['flash-success', 'flash-error', 'flash-validation'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; setTimeout(() => el.remove(), 600); }, 5000);
        });
    });
    </script>

</body>
</html>
