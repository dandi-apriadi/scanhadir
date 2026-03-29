<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'ScanHadir' }} - Solusi Presensi QR Modern</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2f1bc8",
                        "primary-container": "#493fdf",
                        "on-primary": "#ffffff",
                        "on-primary-container": "#d0cdff",
                        "on-primary-fixed": "#0f0069",
                        "on-primary-fixed-variant": "#3323cc",
                        "secondary": "#4648d4",
                        "secondary-container": "#6063ee",
                        "on-secondary": "#ffffff",
                        "on-secondary-container": "#fffbff",
                        "tertiary": "#6c3400",
                        "tertiary-container": "#8f4700",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-container": "#ffc7a2",
                        "surface": "#f8f9ff",
                        "surface-bright": "#f8f9ff",
                        "surface-container": "#e6eeff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d5e3fc",
                        "surface-container-low": "#eff4ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#ccdbf3",
                        "surface-variant": "#d5e3fc",
                        "on-surface": "#0d1c2e",
                        "on-surface-variant": "#454652",
                        "background": "#f8f9ff",
                        "on-background": "#0d1c2e",
                        "outline": "#757684",
                        "outline-variant": "#c5c5d4",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a",
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .mesh-gradient-bg {
            background-color: #f8f9ff;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,100%,93%,1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(225,100%,91%,1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(245,76%,85%,1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(253,77%,90%,1) 0px, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body selection:bg-primary-container selection:text-white">
    @yield('content')
</body>
</html>
