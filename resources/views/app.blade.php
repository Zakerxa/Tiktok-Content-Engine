<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Z.A.K.E.R.X.A') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="favicon.png" type="image/x-png">
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    <style>
        #app-loader {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 22px;
            background: #080B14;
            font-family: Inter, "Segoe UI", sans-serif;
            z-index: 9999;
        }

        #app-loader .app-loader-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        #app-loader .app-loader-glow span {
            position: absolute;
            border-radius: 9999px;
            filter: blur(80px);
        }

        #app-loader .app-loader-glow span:nth-child(1) {
            width: 500px;
            height: 500px;
            top: -120px;
            left: -150px;
            background: rgba(124, 58, 237, 0.22);
        }

        #app-loader .app-loader-glow span:nth-child(2) {
            width: 420px;
            height: 420px;
            bottom: -120px;
            right: -120px;
            background: rgba(6, 182, 212, 0.16);
        }

        #app-loader .spinner {
            position: relative;
            z-index: 1;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, #7C3AED, #06B6D4, #F59E0B, #7C3AED);
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 0);
            mask: radial-gradient(farthest-side, transparent calc(100% - 4px), #000 0);
            animation: app-loader-spin 0.9s linear infinite;
        }

        #app-loader .brand {
            position: relative;
            z-index: 1;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #7C3AED, #06B6D4, #F59E0B);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: app-loader-pulse 1.6s ease-in-out infinite;
        }

        @keyframes app-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes app-loader-pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.55;
            }
        }

        #app-loader.app-loader-hidden {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
    </style>
</head>

<body class="font-sans antialiased">

    @inertia
    <div id="app-loader">
        <div class="app-loader-glow">
            <span></span>
            <span></span>
        </div>
        <div class="spinner"></div>
        <div class="brand">{{ config('app.name', 'Z.A.K.E.R.X.A') }}</div>
    </div>
</body>

</html>
