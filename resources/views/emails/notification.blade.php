<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Notification - EMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.5;
            color: #1f2937;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
        }

        /* Mobile-first styles */
        .email-wrapper {
            max-width: 100%;
            width: 100%;
            min-height: 100vh;
            background: #ffffff;
            position: relative;
        }

        .email-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 25%, #10b981 50%, #f59e0b 75%, #ef4444 100%);
            z-index: 10;
        }

        .header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            color: white;
            padding: 32px 20px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            line-height: 1.2;
        }

        .header-tagline {
            font-size: 15px;
            opacity: 0.9;
            font-weight: 500;
            margin-top: 6px;
            color: #dbeafe;
            padding: 0 10px;
        }

        .notification-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .content {
            padding: 32px 20px;
        }

        .greeting {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .greeting::before {
            content: '👋';
            font-size: 22px;
            flex-shrink: 0;
        }

        .intro-text {
            color: #475569;
            font-size: 16px;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .notification-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0 32px;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
        }

        .notification-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
        }

        .notification-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.3;
        }

        .notification-title::before {
            content: '📢';
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .notification-content {
            color: #475569;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .notification-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
            color: #64748b;
            font-size: 14px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: #f1f5f9;
            border-radius: 10px;
            font-weight: 500;
            width: 100%;
        }

        .meta-item i {
            flex-shrink: 0;
            width: 18px;
            text-align: center;
        }

        .action-container {
            text-align: center;
            margin: 32px 0;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            padding: 18px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.25);
            border: none;
            cursor: pointer;
            width: 100%;
            max-width: 320px;
        }

        .action-button:active {
            transform: scale(0.98);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
        }

        .action-button i {
            font-size: 16px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 32px 0;
            position: relative;
        }

        .divider::before {
            content: '✦';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 0 12px;
            color: #94a3b8;
            font-size: 14px;
        }

        .additional-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #bae6fd;
            margin-top: 32px;
        }

        .info-title {
            font-size: 17px;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-title::before {
            content: '💡';
            font-size: 18px;
            flex-shrink: 0;
        }

        .info-content {
            color: #0c4a6e;
            font-size: 14px;
            line-height: 1.6;
        }

        .info-content a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
            word-break: break-all;
        }

        .info-content a:active {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .footer {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #cbd5e1;
            padding: 32px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .footer-logo {
            color: white;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-tagline {
            opacity: 0.9;
            margin-bottom: 12px;
            line-height: 1.5;
            font-size: 15px;
            color: #e2e8f0;
            padding: 0 10px;
        }

        .footer-links {
            margin-top: 24px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            text-align: center;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-link {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-link:active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .social-links {
            margin-top: 24px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .social-icon:active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: scale(0.95);
        }

        .copyright {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #334155;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Tablet and larger mobile devices */
        @media (min-width: 375px) {
            .header {
                padding: 36px 24px 30px;
            }
            
            .content {
                padding: 36px 24px;
            }
            
            .footer {
                padding: 36px 24px;
            }
        }

        @media (min-width: 425px) {
            .notification-meta {
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            .meta-item {
                width: auto;
                flex: 1;
                min-width: 140px;
            }
            
            .footer-links {
                grid-template-columns: repeat(4, 1fr);
                max-width: 100%;
            }
        }

        @media (min-width: 640px) {
            body {
                padding: 20px;
            }
            
            .email-wrapper {
                max-width: 600px;
                margin: 0 auto;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 5px 20px rgba(0, 0, 0, 0.03);
                border: 1px solid #f1f5f9;
                min-height: auto;
            }
            
            .header {
                padding: 40px 48px 36px;
            }
            
            .content {
                padding: 48px;
            }
            
            .footer {
                padding: 48px;
            }
            
            .logo-container {
                gap: 16px;
                margin-bottom: 16px;
            }
            
            .logo-icon {
                width: 56px;
                height: 56px;
                font-size: 24px;
            }
            
            .logo-text {
                font-size: 32px;
            }
            
            .header-tagline {
                font-size: 16px;
            }
            
            .notification-card {
                padding: 32px;
                margin: 32px 0 40px;
                border-radius: 18px;
            }
            
            .notification-title {
                font-size: 24px;
            }
            
            .action-button {
                width: auto;
                min-width: 220px;
                padding: 18px 40px;
            }
            
            .action-button:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            }
            
            .footer-link:hover {
                color: white;
                background: rgba(255, 255, 255, 0.1);
            }
            
            .social-icon:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                transform: translateY(-3px);
            }
            
            .info-content a:hover {
                color: #1d4ed8;
                text-decoration: underline;
            }
            
            .notification-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                border-color: #c7d2fe;
            }
        }

        /* Touch-friendly improvements */
        button, a {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
            touch-action: manipulation;
        }

        /* Improve readability on small screens */
        @media (max-width: 374px) {
            .greeting {
                font-size: 18px;
            }
            
            .notification-title {
                font-size: 20px;
            }
            
            .footer-links {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        /* Dark mode support for email clients that support it */
        @media (prefers-color-scheme: dark) {
            .email-wrapper {
                background: #1a1a1a;
                border-color: #333;
            }
            
            .content {
                color: #e5e5e5;
            }
            
            .greeting {
                color: #f3f4f6;
            }
            
            .intro-text, .notification-content {
                color: #d1d5db;
            }
            
            .notification-card {
                background: linear-gradient(135deg, #2d2d2d 0%, #262626 100%);
                border-color: #404040;
            }
            
            .notification-title {
                color: #f9fafb;
            }
            
            .meta-item {
                background: #333;
                color: #9ca3af;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="logo-text">EMS</div>
            </div>
            <div class="header-tagline">Event Management System</div>
            <div class="notification-badge">
                <i class="fas fa-bell"></i> Event Notification
            </div>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $recipientName ?? 'Event Organizer' }},</p>
            
            <p class="intro-text">
                You have a new event notification from EMS. Here's the important update regarding your event:
            </p>
            
            <div class="notification-card">
                <h2 class="notification-title">{{ $title ?? "Event Update" }}</h2>
                <div class="notification-content">
                    {{ $content ?? "There's an important update regarding your event. Please review the details below." }}
                </div>
                <div class="notification-meta">
                    <span class="meta-item">
                        <i class="far fa-calendar"></i> {{ date('F j, Y') }}
                    </span>
                    <span class="meta-item">
                        <i class="far fa-clock"></i> {{ date('g:i A') }}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-tag"></i> Event Update
                    </span>
                </div>
            </div>
            
            @if(isset($actionUrl) && $actionUrl)
            <div class="action-container">
                <a href="{{ $actionUrl }}" class="action-button">
                    <i class="fas fa-external-link-alt"></i> View Event Details
                </a>
            </div>
            @endif
            
            <div class="divider"></div>
            
            <div class="additional-info">
                <p class="info-title">Need assistance with your event?</p>
                <p class="info-content">
                    Our event management team is here to help you with planning, coordination, and any questions you may have. 
                    Contact us at <a href="mailto:support@ems.example.com">support@ems.example.com</a> or visit our 
                    <a href="https://ems.example.com/help">help center</a> for resources and guides.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-logo">EMS</div>
            <p class="footer-tagline">
                Streamlining event planning and management with powerful tools and seamless coordination.
            </p>
            
            <div class="footer-links">
                <a href="https://ems.example.com" class="footer-link">Dashboard</a>
                <a href="https://ems.example.com/events" class="footer-link">My Events</a>
                <a href="https://ems.example.com/contact" class="footer-link">Support</a>
                <a href="https://ems.example.com/help" class="footer-link">Help</a>
            </div>
            
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
            
            <div class="copyright">
                &copy; {{ date('Y') }} Event Management System (EMS). All rights reserved.<br>
                <small style="font-size: 13px; opacity: 0.7; margin-top: 8px; display: inline-block;">
                    This is an automated notification. Please do not reply to this email.
                </small>
            </div>
        </div>
    </div>
</body>
</html>