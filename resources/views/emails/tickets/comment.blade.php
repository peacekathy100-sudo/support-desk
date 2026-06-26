<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - New reply</title>
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

        p, blockquote {
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

        blockquote {
            margin: 0;
            padding: 14px 16px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #dbeafe;
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
        <h1>New reply on your ticket</h1>
        <p>Hello {{ $ticket->creator->full_name }},</p>
        <p><strong>{{ $comment->author->full_name }}</strong> has replied to your ticket.</p>

        <div class="panel">
            <span class="label">Ticket</span>
            <div>{{ $ticket->ticket_number }}</div>
            <span class="label">Subject</span>
            <div>{{ $ticket->subject }}</div>
            <span class="label">Reply</span>
            <blockquote>{{ $comment->comment }}</blockquote>
        </div>

        <p>
            <a href="{{ url('/tickets/' . $ticket->ticket_id) }}" class="button">View & reply</a>
        </p>

        <div class="footer">
            Powered by Flaxem Support Desk<br>
            Reply directly to this email if you need a quick response.
        </div>
    </div>
</body>
</html>
