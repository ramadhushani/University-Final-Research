<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Project Assignment – ProjectPulse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f1117; color: #e2e8f0; }
        .wrapper { max-width: 600px; margin: 40px auto; }
        .header { background: linear-gradient(135deg, #06b6d4 0%, #6c63ff 100%); padding: 40px 48px; border-radius: 16px 16px 0 0; text-align: center; }
        .logo { font-size: 2rem; margin-bottom: 8px; }
        .header h1 { font-size: 1.7rem; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 0.95rem; margin-top: 6px; }
        .body { background: #1a1d2e; padding: 40px 48px; }
        .greeting { font-size: 1.1rem; font-weight: 600; color: #e2e8f0; margin-bottom: 16px; }
        .intro { color: #94a3b8; line-height: 1.7; font-size: 0.95rem; margin-bottom: 28px; }
        .role-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
        .role-developer { background: rgba(6,182,212,0.15); color: #22d3ee; }
        .role-designer  { background: rgba(168,85,247,0.15); color: #c084fc; }
        .role-qa        { background: rgba(34,197,94,0.15); color: #4ade80; }
        .creds-box { background: #0f1117; border: 1px solid #2d3748; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
        .creds-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #06b6d4; margin-bottom: 14px; }
        .cred-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #1a1d2e; }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { font-size: 0.85rem; color: #64748b; }
        .cred-value { font-size: 0.9rem; font-weight: 700; color: #e2e8f0; font-family: 'Courier New', monospace; background: #1a1d2e; padding: 4px 10px; border-radius: 6px; }
        .project-box { background: #0f1117; border: 1px solid #2d3748; border-left: 4px solid #06b6d4; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
        .project-box h3 { font-size: 1rem; font-weight: 700; color: #e2e8f0; margin-bottom: 8px; }
        .project-box p { font-size: 0.88rem; color: #94a3b8; line-height: 1.6; }
        .meta-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
        .meta-chip { background: #1a1d2e; border: 1px solid #2d3748; padding: 4px 12px; border-radius: 6px; font-size: 0.78rem; color: #94a3b8; }
        .meta-chip span { color: #e2e8f0; font-weight: 600; }
        .proposal-link { display: inline-block; margin-top: 14px; background: linear-gradient(135deg, #06b6d4, #6c63ff); color: #fff; padding: 10px 22px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 600; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #06b6d4, #6c63ff); color: #fff; padding: 16px 32px; border-radius: 10px; text-decoration: none; font-size: 1rem; font-weight: 700; margin-bottom: 28px; }
        .footer { background: #0f1117; padding: 24px 48px; border-radius: 0 0 16px 16px; text-align: center; border-top: 1px solid #2d3748; }
        .footer p { font-size: 0.8rem; color: #475569; line-height: 1.6; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="logo">💼</div>
        <h1>ProjectPulse</h1>
        <p>New Project Assignment</p>
    </div>

    <div class="body">
        <div class="greeting">Hi {{ $member->name }}! 🎉</div>

        @php
            $roleLabels = ['developer' => 'Developer', 'designer' => 'Designer', 'qa' => 'QA Engineer'];
            $roleLabel  = $roleLabels[$member->role] ?? ucfirst($member->role);
        @endphp

        <span class="role-badge role-{{ $member->role }}">{{ $roleLabel }}</span>

        <p class="intro">
            You have been assigned to a new project on <strong>ProjectPulse</strong>.
            Your account credentials are below — log in to view your assigned tasks, collaborate with the team, and track progress.
        </p>

        <!-- Login Credentials -->
        <div class="creds-box">
            <div class="creds-title">🔐 Your Login Credentials</div>
            <div class="cred-row">
                <span class="cred-label">Login URL</span>
                <span class="cred-value">{{ config('app.url') }}/login</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Username</span>
                <span class="cred-value">{{ $member->username }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Password</span>
                <span class="cred-value">{{ $plainPassword }}</span>
            </div>
        </div>

        <!-- Project Details -->
        <div class="project-box">
            <h3>📁 {{ $project->title }}</h3>
            <p>{{ $project->description ?? 'Project details will be available on your dashboard.' }}</p>

            <div class="meta-row">
                <div class="meta-chip">Status: <span>{{ $project->status }}</span></div>
                <div class="meta-chip">Project ID: <span>PROJ-{{ 1000 + $project->id }}</span></div>
                @if($project->client)
                    <div class="meta-chip">Client: <span>{{ $project->client->name }}</span></div>
                @endif
            </div>

            @if($project->proposal_file_path)
                <a href="{{ config('app.url') }}/storage/{{ $project->proposal_file_path }}" class="proposal-link">
                    📎 Download Project Proposal
                </a>
            @endif
        </div>

        <!-- CTA -->
        <a href="{{ config('app.url') }}/login" class="cta-btn">
            🚀 Go to Your Dashboard
        </a>

        <p style="font-size: 0.85rem; color: #64748b; line-height: 1.7;">
            Please review your assigned tasks once you log in. Contact your Project Manager if you have any questions.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ProjectPulse · All rights reserved</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</div>
</body>
</html>
