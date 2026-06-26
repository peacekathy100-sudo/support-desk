<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Flaxem Support Desk</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(180deg, #f3f8ff, #ffffff);
            margin: 0;
            padding: 24px;
            color: #0f172a;
        }

        .card {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            border: 1px solid #dbeafe;
            padding: 32px;
            box-shadow: 0 18px 48px rgba(29, 105, 220, 0.12);
        }

        .badge {
            display: inline-block;
            background: #3496D7;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            font-size: 26px;
            margin: 18px 0 12px;
        }

        p {
            color: #334155;
            line-height: 1.7;
        }

        .panel {
            background: #f8fbff;
            border: 1px solid #dbeafe;
            border-left: 4px solid #3496D7;
            border-radius: 18px;
            padding: 18px;
            margin: 22px 0;
        }

        .label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #3496D7;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3496D7, #0f4cb8);
            color: #ffffff;
            text-decoration: none;
            padding: 13px 22px;
            border-radius: 999px;
            font-weight: 700;
            margin-top: 8px;
        }

        .footer {
            margin-top: 26px;
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">Flaxem Support Desk</span>
        <h1>Welcome to Flaxem Support Desk</h1>
        <p>Hello {{ $user->full_name }},</p>
        <p>Your account is ready. Use the details below to access the support desk and start managing tickets securely.</p>

        <div class="panel">
            <span class="label">Login URL</span>
            <div>{{ url('/') }}</div>
            <span class="label">Username</span>
            <div>{{ $user->user_name }}</div>
            <span class="label">Password</span>
            <div>{{ $plainPassword }}</div>
            <span class="label">Department</span>
            <div>{{ $user->department->dept_name ?? 'N/A' }}</div>
            <span class="label">Role</span>
            <div>{{ $user->role->ur_name ?? 'N/A' }}</div>
        </div>

        <p>For security, please update your password after first login.</p>

        <p>
            <a href="{{ url('/') }}" class="button">Login now</a>
        </p>

        <div class="footer">
            Powered by Flaxem Support Desk<br>
            Need help? Contact your IT support administrator.
        </div>
    </div>
</body>
</html>
