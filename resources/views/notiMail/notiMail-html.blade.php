<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: auto;
        }
        .header {
            font-size: 20px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 15px;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">SYNERGY TEAM</div>

        <div class="message">
            <p><strong>Notification:</strong> {{ $data['message'] }}</p>
        </div>

        <div class="footer">
            This is a system notification from Synergy. No reply is required.
        </div>
    </div>
</body>
</html>
