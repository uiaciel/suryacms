<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Forwarded Message From {{ config('app.name') }} </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        p {
            margin-bottom: 0px;
            margin-top: 0px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #222;
        }

        .content {
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .footer {
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
            text-align: left;
            margin-top: 20px;
        }

        .meta {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            {{ config('app.name') }} Inbox
        </div>

        <div class="content">
            <div class="meta">
                <p><strong>From:</strong> {{ $contact->name }} ({{ $contact->email }})</p>
                <p><strong>Subject:</strong> {{ $contact->subject }}</p>
                <p><strong>Date:</strong> {{ $contact->created_at->format('d M Y H:i') }}</p>
            </div>

            <hr>
            <h5>Message: </h5>
            <p>{!! nl2br(e($contact->message)) !!}</p>

            <hr>
            <div class="meta">
                <p><strong>IP:</strong> {{ $contact->ip_address }}</p>
                <p><strong>Referrer:</strong> {{ $contact->referrer }}</p>
                <p><strong>User Agent:</strong> {{ $contact->user_agent }}</p>
            </div>

            <div class="footer">
                <p>Please review this email. It was sent from the website contact form.
                    If you need to reply, please send your response directly to the sender's email address:<strong>{{
                        $contact->email }}</strong></p>
            </div>

        </div>


    </div>
</body>

</html>