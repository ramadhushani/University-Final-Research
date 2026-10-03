<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ProjectPulse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f1117; color: #e2e8f0; }
        .wrapper { max-width: 600px; margin: 40px auto; }
        .header { background: linear-gradient(135deg, #6c63ff 0%, #a855f7 100%); padding: 40px 48px; border-radius: 16px 16px 0 0; text-align: center; }
        .logo { font-size: 2rem; margin-bottom: 8px; }
        .header h1 { font-size: 1.7rem; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 0.95rem; margin-top: 6px; }
        .body { background: #1a1d2e; padding: 40px 48px; }
        .greeting { font-size: 1.1rem; font-weight: 600; color: #e2e8f0; margin-bottom: 16px; }
        .intro { color: #94a3b8; line-height: 1.7; font-size: 0.95rem; margin-bottom: 28px; }
        .creds-box { background: #0f1117; border: 1px solid #2d3748; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
        .creds-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #6c63ff; margin-bottom: 14px; }
        .cred-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #1a1d2e; }
        .cred-row:last-child { border-bottom: none; }
        .cred-label { font-size: 0.85rem; color: #64748b; }
        .cred-value { font-size: 0.9rem; font-weight: 700; color: #e2e8f0; font-family: 'Courier New', monospace; background: #1a1d2e; padding: 4px 10px; border-radius: 6px; }
        .project-box { background: #0f1117; border: 1px solid #2d3748; border-left: 4px solid #6c63ff; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
        .project-box h3 { font-size: 1rem; font-weight: 700; color: #e2e8f0; margin-bottom: 10px; }
        .project-box p { font-size: 0.88rem; color: #94a3b8; line-height: 1.6; }
        .proposal-link { display: inline-block; margin-top: 14px; background: linear-gradient(135deg, #6c63ff, #a855f7); color: #fff; padding: 10px 22px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 600; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #6c63ff, #a855f7); color: #fff; padding: 16px 32px; border-radius: 10px; text-decoration: none; font-size: 1rem; font-weight: 700; margin-bottom: 28px; }
        .footer { background: #0f1117; padding: 24px 48px; border-radius: 0 0 16px 16px; text-align: center; border-top: 1px solid #2d3748; }
        .footer p { font-size: 0.8rem; color: #475569; line-height: 1.6; }
        .badge { display: inline-block; background: rgba(108,99,255,0.15); color: #a78bfa; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="logo">📊</div>
        <h1>ProjectPulse</h1>
        <p>Your Project Management Hub</p>
    </div>

    <div class="body">
        <div class="greeting">Hello, {{ $client->name }}! 👋</div>

        <p class="intro">
            Welcome to <strong>ProjectPulse</strong>! Your client account has been set up and a new project has been initiated for you.
            Here are your login credentials and project details — please keep them safe.
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
                <span class="cred-value">{{ $client->username }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Password</span>
                <span class="cred-value">{{ $plainPassword }}</span>
            </div>
        </div>

        <!-- Project Details -->
        <div class="project-box">
            <span class="badge">New Project</span>
            <h3>{{ $project->title }}</h3>
            <p>{{ $project->description ?? 'Your project is now active and the team is being assembled.' }}</p>

            @if($project->proposal_file_path)
                <a href="{{ config('app.url') }}/storage/{{ $project->proposal_file_path }}" class="proposal-link">
                    📎 Download Project Proposal
                </a>
            @endif
        </div>

        <!-- CTA -->
        <a href="{{ config('app.url') }}/login" class="cta-btn">
            🚀 Access Your Dashboard
        </a>

        <p style="font-size: 0.85rem; color: #64748b; line-height: 1.7;">
            You can log in at any time to view your project status, review progress updates, and communicate with the project team.
            If you have any issues logging in, please contact your Project Manager.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ProjectPulse · All rights reserved</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</div>
</body>
</html>
