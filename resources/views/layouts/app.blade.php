<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($booth = config('booth'))
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Kasir Booth') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset($booth['logo']) }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800|sora:400,500,600,700,800" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        @endif

        <style>
            :root {
                --brand-sand: #f7f2e8;
                --brand-cream: #fffaf3;
                --brand-ink: #172033;
                --brand-muted: #64748b;
                --brand-orange: #f97316;
                --brand-amber: #f59e0b;
                --brand-emerald: #059669;
                --brand-navy: #0f172a;
            }

            body {
                font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(249, 115, 22, 0.20), transparent 26%),
                    radial-gradient(circle at right 20%, rgba(245, 158, 11, 0.16), transparent 24%),
                    linear-gradient(180deg, #fffaf3 0%, #fff7ec 45%, #f7f2e8 100%);
                color: var(--brand-ink);
            }

            .font-display {
                font-family: 'Sora', ui-sans-serif, system-ui, sans-serif;
            }

            .mesh-panel {
                background:
                    linear-gradient(135deg, rgba(255, 255, 255, 0.88), rgba(255, 247, 237, 0.78)),
                    radial-gradient(circle at top right, rgba(249, 115, 22, 0.18), transparent 36%);
                backdrop-filter: blur(18px);
            }

            .soft-grid {
                background-image:
                    linear-gradient(rgba(23, 32, 51, 0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(23, 32, 51, 0.05) 1px, transparent 1px);
                background-size: 28px 28px;
            }

            .shadow-panel {
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10);
            }

            .animate-rise {
                animation: rise 0.7s ease both;
            }

            .animate-float {
                animation: float 7s ease-in-out infinite;
            }

            .animate-pulse-soft {
                animation: pulse-soft 2.4s ease-in-out infinite;
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(18px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-10px);
                }
            }

            @keyframes pulse-soft {
                0%, 100% {
                    opacity: 0.85;
                }
                50% {
                    opacity: 1;
                }
            }
        </style>
    </head>
    <body class="min-h-screen antialiased">
        {{ $slot ?? '' }}
        @yield('content')
    </body>
</html>
