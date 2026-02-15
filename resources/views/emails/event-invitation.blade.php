<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            padding: 16px;
            line-height: 1.5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Card */
        .invitation-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 2px solid #e5e7eb;
        }

        /* Header with gradient */
        .email-header {
            background: linear-gradient(to right, #db2777, #9333ea);
            padding: 20px 24px;
        }

        .email-header h2 {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .email-header p {
            color: #fbcfe8;
            font-size: 14px;
            margin-top: 4px;
        }

        /* Main content area with gradient background */
        .main-content {
            background: linear-gradient(to bottom right, #f9fafb, #f3f4f6);
            padding: 24px;
        }

        /* Invitation Card Preview */
        .preview-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 2px solid #e5e7eb;
            max-width: 448px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
        }

        /* Image Section - 70% */
        .image-section {
            flex: 7;
            position: relative;
            min-height: 350px;
            background: linear-gradient(to bottom right, #6366f1, #a855f7, #ec4899);
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
        }

        /* QR Code positioned at bottom right */
        .qr-code-wrapper {
            position: absolute;
            bottom: 16px;
            right: 16px;
            z-index: 10;
        }

        .qr-code-box {
            background: white;
            padding: 4px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .qr-code-box img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            display: block;
        }

        /* Fallback when no image */
        .image-placeholder {
            height: 100%;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom right, #6366f1, #a855f7, #ec4899);
        }

        .placeholder-content {
            text-align: center;
            color: white;
            padding: 0 24px;
        }

        .placeholder-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 12px;
            opacity: 0.5;
        }

        .placeholder-icon svg {
            width: 100%;
            height: 100%;
        }

        .placeholder-title {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .placeholder-subtitle {
            font-size: 12px;
            opacity: 0.75;
            margin-top: 4px;
        }

        /* Message Section - 30% */
        .message-section {
            flex: 3;
            background: white;
            overflow-y: auto;
            padding: 24px;
        }

        .message-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .event-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .event-date {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            color: #4b5563;
        }

        .event-date svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .event-location {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }

        .event-location svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .divider {
            border-top: 1px solid #f3f4f6;
            margin: 16px 0;
        }

        .message-content {
            font-size: 14px;
            color: #374151;
            line-height: 1.625;
            white-space: pre-wrap;
        }

        /* Guest greeting */
        .guest-greeting {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f3f4f6;
        }

        .guest-name {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .guest-name span {
            color: #db2777;
        }

        /* Helper text */
        .helper-text {
            font-size: 12px;
            text-align: center;
            color: #6b7280;
            margin-top: 16px;
            font-style: italic;
        }

        /* Footer with RSVP link */
        .email-footer {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
        }

        .rsvp-button {
            display: inline-block;
            background: linear-gradient(to right, #db2777, #9333ea);
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 8px;
            margin: 16px 0 8px;
            box-shadow: 0 4px 6px -1px rgba(219, 39, 119, 0.2);
        }

        .footer-note {
            color: #9ca3af;
            font-size: 12px;
            line-height: 1.5;
        }

        .footer-note p {
            margin-bottom: 4px;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .main-content {
                padding: 16px;
            }
            
            .message-section {
                padding: 20px;
            }
            
            .event-title {
                font-size: 22px;
            }
            
            .qr-code-box img {
                width: 70px;
                height: 70px;
            }
        }

        @media (min-width: 768px) {
            .preview-card {
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="invitation-card">
            <!-- Email Header with gradient -->
            <div class="email-header">
                <h2>🎉 You're Invited!</h2>
                <p>Your digital invitation is ready</p>
            </div>

            <!-- Main Content with gradient background -->
            <div class="main-content">
                <!-- Invitation Card Preview -->
                <div class="preview-card">
                    <!-- Image Section with QR Code -->
                    @if(isset($event->cover_image))
                    <div class="image-section">
                        <img src="{{ $event->cover_image }}" alt="Event cover">
                        <div class="image-overlay"></div>
                        
                        <!-- QR Code at bottom right -->
                        @if(isset($qrCodeBase64) || isset($qrCodeSvg))
                        <div class="qr-code-wrapper">
                            <div class="qr-code-box">
                                @if(isset($qrCodeBase64) && Str::startsWith($qrCodeBase64, 'data:image'))
                                    <img src="{{ $qrCodeBase64 }}" alt="Event QR Code">
                                @elseif(isset($qrCodeSvg))
                                    {!! $qrCodeSvg !!}
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <!-- Fallback when no cover image -->
                    <div class="image-section">
                        <div class="image-placeholder">
                            <div class="placeholder-content">
                                <div class="placeholder-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="placeholder-title">{{ $event->title }}</p>
                                <p class="placeholder-subtitle">Your invitation image will appear here</p>
                            </div>
                        </div>
                        
                        <!-- QR Code on placeholder -->
                        @if(isset($qrCodeBase64) || isset($qrCodeSvg))
                        <div class="qr-code-wrapper">
                            <div class="qr-code-box">
                                @if(isset($qrCodeBase64) && Str::startsWith($qrCodeBase64, 'data:image'))
                                    <img src="{{ $qrCodeBase64 }}" alt="Event QR Code">
                                @elseif(isset($qrCodeSvg))
                                    {!! $qrCodeSvg !!}
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Message Section -->
                    <div class="message-section">
                        <!-- Guest Greeting -->
                        <div class="guest-greeting">
                            <div class="guest-name">
                                Dear <span>{{ $guest->first_name }} {{ $guest->last_name }}</span>,
                            </div>
                        </div>

                        <div class="message-header">
                            <h3 class="event-title">{{ $event->title }}</h3>
                            
                            <!-- Date -->
                            <div class="event-date">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($event->start_date)->format('l, F j, Y') }} at {{ \Carbon\Carbon::parse($event->start_date)->format('g:i A') }}</span>
                            </div>

                            <!-- Location -->
                            @if(!empty($event->venue) || !empty($event->city))
                            <div class="event-location">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>
                                    @if(!empty($event->venue)){{ $event->venue }}, @endif
                                    @if(!empty($event->city)){{ $event->city }}@endif
                                    @if(!empty($event->country)) , {{ $event->country }}@endif
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Divider -->
                        <div class="divider"></div>

                        <!-- Message Content -->
                        <div class="message-content">
                            {!! nl2br(e($generatedMessage)) !!}
                        </div>

                        <!-- Plus Ones Info -->
                        @if($guest->plus_ones > 0)
                        <div style="margin-top: 16px; font-size: 13px; color: #6b7280; border-top: 1px dashed #e5e7eb; padding-top: 12px;">
                            <span style="font-weight: 600;">Plus Ones:</span> You may bring {{ $guest->plus_ones }} guest{{ $guest->plus_ones > 1 ? 's' : '' }}
                        </div>
                        @endif

                        <!-- Guest ID -->
                        <div style="margin-top: 12px; font-size: 12px; color: #9ca3af; text-align: right;">
                            Guest ID: <span style="font-family: monospace; font-weight: 600; color: #db2777;">#{{ $guest->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Helper Text -->
                <p class="helper-text">
                    Please present this QR code at the entrance for check-in
                </p>

                <!-- Footer with RSVP Link -->
                <div class="email-footer">
                    <p style="color: #4b5563; margin-bottom: 8px; font-size: 14px;">
                        <strong>Need to make changes?</strong>
                    </p>
                    <a href="{{ $invitationUrl }}" class="rsvp-button">
                        Update RSVP or View Details
                    </a>

                    <div class="footer-note">
                        <p>This invitation was sent to {{ $guest->email }}</p>
                        <p>Please keep this email for event check-in</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>