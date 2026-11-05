<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?: 'Bon Trouver — Temporarily Unavailable' }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2349D17D'><path d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #060D18;
            color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* SuperAdmin Floating Control Bar */
        .superadmin-bar {
            position: fixed;
            top: 14px;
            right: 18px;
            z-index: 99999;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(245, 158, 11, 0.5);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            font-size: 12.5px;
            font-weight: 600;
            color: #F8FAFC;
        }
        .superadmin-bar .badge-lead {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #FBBF24;
        }
        .superadmin-bar a {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
            text-decoration: none;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            transition: all 0.2s ease;
        }
        .superadmin-bar a:hover {
            background: #F59E0B;
            color: #0F172A;
        }

        /* 1. Fullscreen Image Splash Container */
        .splash-viewport {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000;
            overflow: hidden;
        }
        .splash-image-element {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        /* 2. Default Graphic when no image is uploaded */
        .default-splash-wrap {
            max-width: 640px;
            width: 90%;
            text-align: center;
            padding: 48px 36px;
            background: radial-gradient(circle at 50% 0%, rgba(239, 68, 68, 0.12), transparent 70%), #0A1424;
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.7);
        }
        .lock-icon-orb {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.15);
            border: 2px solid rgba(239, 68, 68, 0.4);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: #EF4444;
            margin-bottom: 24px;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);
        }

        /* 3. Branded Notice Container */
        .notice-container {
            max-width: 580px;
            width: 90%;
            background: linear-gradient(145deg, #0A1E33 0%, #06182B 100%);
            border: 1px solid rgba(73, 209, 125, 0.25);
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.6), 0 0 40px rgba(73, 209, 125, 0.08);
            position: relative;
        }
        .logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #fff;
            margin-bottom: 24px;
        }
        .logo-accent {
            color: #49D17D;
        }
        .pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #F59E0B;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 22px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #F59E0B;
            border-radius: 50%;
            box-shadow: 0 0 10px #F59E0B;
            animation: pulseGlow 1.8s infinite;
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }
        h1 {
            font-size: 26px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 14px;
            line-height: 1.3;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #94A3B8;
            margin-bottom: 28px;
            white-space: pre-line;
        }
        .footer-note {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
            font-size: 13px;
            color: #64748B;
        }
    </style>
</head>
<body>

@if(!empty($isSuperAdmin))
    <div class="superadmin-bar">
        <span class="badge-lead"><i class="ri-shield-keyhole-fill"></i> Platform Locked to Public</span>
        <a href="{{ route('admin.platform-control.index') }}"><i class="ri-settings-4-line"></i> Master Switch</a>
        <a href="{{ url('/?bt_master_key=' . $bypassKey) }}" title="Bypass lockdown and preview live site"><i class="ri-eye-line"></i> Preview Site</a>
    </div>
@endif

@if($status === 'image_splash')
    @php
        $splashSrc = '';
        if (!empty($imagePath)) {
            $splashSrc = (str_starts_with($imagePath, 'http') || str_starts_with($imagePath, 'data:'))
                ? $imagePath 
                : asset('storage/' . $imagePath);
        }
    @endphp

    @if(!empty($splashSrc))
        {{-- Fullscreen Custom Image Mode --}}
        <div class="splash-viewport">
            <img src="{{ $splashSrc }}" alt="Website Temporarily Offline" class="splash-image-element">
        </div>
    @else
        {{-- Default Graphic when image is not uploaded yet --}}
        <div class="default-splash-wrap">
            <div class="lock-icon-orb">
                <i class="ri-lock-2-line"></i>
            </div>
            <h1>Website Temporarily Offline</h1>
            <p>{{ $message ?: 'Access to this platform is currently suspended by administration. Please check back later.' }}</p>
            <div class="footer-note">
                Bon Trouver Canada &bull; Secure Canadian Classifieds Network
            </div>
        </div>
    @endif

@else
    {{-- Branded Canadian Maintenance Notice Mode --}}
    <div class="notice-container">
        <div class="logo-wrap">
            <span>BON<span class="logo-accent">TROUVER</span></span> 🇨🇦
        </div>
        
        <div>
            <span class="pulse-badge">
                <span class="pulse-dot"></span>
                System Notice
            </span>
        </div>

        <h1>{{ $title ?: 'Platform Under Scheduled Maintenance' }}</h1>
        <p>{{ $message ?: 'Our engineers are currently performing essential infrastructure upgrades. Bon Trouver will be back online shortly. Thank you for your patience.' }}</p>

        <div class="footer-note">
            Bon Trouver Canada &bull; All Canadian provincial services reserved.
        </div>
    </div>
@endif

</body>
</html>
