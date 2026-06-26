<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deactivated</title>
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

        h1 {
            font-size: 26px;
            margin: 18px 0 12px;
        }

        p {
            color: #334155;
            line-height: 1.7;
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
        <h1>Account Deactivated</h1>
        <p>Hello {{ $user->full_name }},</p>
        <p>Your support desk account has been deactivated. You will no longer be able to sign in or access tickets assigned to this account.</p>
        <p>If you believe this was done in error, please contact your system administrator.</p>

        <div class="footer">
            {{ config('app.name') }}<br>
            This is an automated notification.
        </div>
    </div>
</body>
</html>
