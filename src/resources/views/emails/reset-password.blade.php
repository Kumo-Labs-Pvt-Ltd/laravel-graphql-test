<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 20px;
            background-color: #ffffff;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            height: 80px;
            width: auto;
        }

        h1 {
            font-size: 28px;
            font-weight: 400;
            margin: 30px 0 20px 0;
        }

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        .action-box {
            margin: 10px 0;
        }

        .action-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            word-break: break-all;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .warning-box {
            margin: 10px 0;
        }

        .warning-text {
            font-size: 16px;
            margin: 5px 0;
        }

        .signature {
            margin: 20px 0;
        }

        .team-name {
            font-weight: 600;
        }

        .footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            color: #6b7280;
            font-size: 15px;
            margin: 8px 0;
        }

        .contact-info {
            font-weight: 600;
        }

        .disclaimer {
            color: #6b7280;
            font-size: 14px;
            margin: 15px 0 5px 0;
        }

        .copyright {
            color: #6b7280;
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo">
    </div>

    <p>Hi {{ $user->name }},</p>

    <p>You requested to reset your password for your {{ config('app.name') }} account.</p>

    <p>Please click the link below to reset your password:</p>

    <div class="action-box">
        <a href="{{ $resetUrl }}" class="action-link">
            {{ $resetUrl }}
        </a>
    </div>

    <div class="warning-box">
        <p class="warning-text">⏳ This password reset link will expire in <strong>60 minutes</strong>.</p>
        <p class="warning-text">
            If you did not request a password reset, you can safely ignore this email.
        </p>
    </div>

    <div class="signature">
        <p>Best regards,<br>
            <span class="team-name">{{ config('app.name') }} Team</span>
        </p>
    </div>

    <div class="footer">
        <p class="footer-text">
            Feel free to contact us at <span class="contact-info">{{ config('mail.from.address') }}</span>
        </p>

        <p class="disclaimer">This is an automated email. Please do not reply to this message.</p>
        <p class="copyright">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>
