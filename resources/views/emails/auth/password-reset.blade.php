<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(180deg, #f3f8ff, #ffffff);
            margin: 0;
            padding: 24px;
            color: #111827;
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
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        h1 {
            font-size: 26px;
            margin: 18px 0 12px;
            color: #0f172a;
        }

        p {
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #3496D7, #0f4cb8);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 999px;
            font-weight: 700;
            margin-top: 12px;
        }

        .footer {
            margin-top: 28px;
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">Flaxem Support Desk</span>
        <h1>Reset your password</h1>
        <p>Hello {{ $user->full_name ?? $user->user_name }},</p>
        <p>We received a request to reset your password for Flaxem Support Desk. Use the secure button below to continue.</p>
        <p>If you did not request this, you can safely ignore this message.</p>
        <p>
            <a href="{{ $resetUrl }}" class="button">Reset password</a>
        </p>
        <div class="footer">
            This request will expire shortly. If you need help, contact your support administrator.
        </div>
    </div>
</body>
</html>
