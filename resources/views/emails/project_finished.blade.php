<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Finished – ProjectPulse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f1117; color: #e2e8f0; }
        .wrapper { max-width: 600px; margin: 40px auto; }
        .header { background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); padding: 40px 48px; border-radius: 16px 16px 0 0; text-align: center; }
        .logo { font-size: 2.5rem; margin-bottom: 8px; }
        .header h1 { font-size: 1.7rem; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 0.95rem; margin-top: 6px; }
        .body { background: #1a1d2e; padding: 40px 48px; }
        .greeting { font-size: 1.1rem; font-weight: 600; color: #e2e8f0; margin-bottom: 16px; }
        .intro { color: #94a3b8; line-height: 1.7; font-size: 0.95rem; margin-bottom: 28px; }
        .project-box { background: #0f1117; border: 1px solid #2d3748; border-left: 4px solid #10b981; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
        .project-box h3 { font-size: 1.1rem; font-weight: 700; color: #e2e8f0; margin-bottom: 8px; }
        .project-box p { font-size: 0.88rem; color: #94a3b8; line-height: 1.6; }
        .status-chip { display: inline-block; background: rgba(16,185,129,0.15); color: #34d399; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-top: 14px; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #10b981, #06b6d4); color: #fff; padding: 16px 32px; border-radius: 10px; text-decoration: none; font-size: 1rem; font-weight: 700; margin-bottom: 28px; }
        .divider { border: none; border-top: 1px solid #2d3748; margin: 24px 0; }
        .footer { background: #0f1117; padding: 24px 48px; border-radius: 0 0 16px 16px; text-align: center; border-top: 1px solid #2d3748; }
        .footer p { font-size: 0.8rem; color: #475569; line-height: 1.6; }
        .celebration { text-align: center; font-size: 2rem; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="logo">🎉</div>
        <h1>Project Complete!</h1>
        <p>Your project has been signed off by the Project Manager</p>
    </div>

    <div class="body">
        <div class="celebration">✅ 🏆 🎊</div>
        <div class="greeting">Congratulations, {{ $client->name }}!</div>

        <p class="intro">
            We are thrilled to inform you that your project has passed all quality checks and has been officially marked as
            <strong>Finished</strong> by the Project Manager. All tasks have been completed and QA-approved.
        </p>

        <div class="project-box">
            <h3>📁 {{ $project->title }}</h3>
            <p>{{ $project->description ?? 'Thank you for choosing ProjectPulse for your project.' }}</p>
            <span class="status-chip">✅ Status: Finished</span>
        </div>

        <a href="{{ config('app.url') }}/login" class="cta-btn">
            📊 View Final Project Dashboard
        </a>

        <hr class="divider">

        <p style="font-size: 0.88rem; color: #94a3b8; line-height: 1.7;">
            If you have any final feedback or require post-project support, please don't hesitate to reach out to your Project Manager.
            It has been a pleasure working with you!
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ProjectPulse · All rights reserved</p>
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</div>
</body>
</html>
