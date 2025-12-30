<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa atrakcji</title>

    <link href="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            overflow: hidden; 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }
        #map { 
            width: 100vw; 
            height: calc(100vh - 64px); 
            position: absolute; 
            top: 64px; 
            left: 0; 
            z-index: 1; 
        }
        .nav-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 64px;
            z-index: 10;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .theme-switcher {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 100;
            background: white;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            padding: 10px 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            user-select: none;
            font-size: 14px;
        }
        .theme-switcher:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            border-color: #007cbf;
        }
        .theme-switcher.dark {
            background: #2d3748;
            color: white;
            border-color: #4a5568;
        }
        .theme-switcher.dark:hover {
            background: #4a5568;
        }
        .theme-icon {
            font-size: 1.2em;
        }
        /* Для підтримки темної теми сторінки */
        body.dark-theme {
            background-color: #1a202c;
            color: #e2e8f0;
        }
        body.dark-theme .nav-header {
            background: #2d3748;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        body.dark-theme .theme-switcher {
            background: #2d3748;
            color: #e2e8f0;
            border-color: #4a5568;
        }
    </style>
</head>
<body class="h-full">
    <div class="nav-header">
        @livewire('navigation-menu')
    </div>

    <div id="theme-switcher" class="theme-switcher" onclick="toggleTheme()">
        <span class="theme-icon">🌙</span>
        <span class="theme-text">Ciemny styl</span>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.js"></script>

    <script>
    let currentTheme = 'light';
    let map;

    const themes = {
        light: {
            mapStyle: 'https://api.maptiler.com/maps/streets-v2/style.json?key=uJDiq16jXWiNZLGeCJ0m',
            buttonIcon: '🌙',
            buttonText: 'Ciemny styl',
            bodyClass: ''
        },
        dark: {
            mapStyle: 'https://api.maptiler.com/maps/streets-v2-dark/style.json?key=uJDiq16jXWiNZLGeCJ0m',  // Ось це — з 3D!
            buttonIcon: '☀️',
            buttonText: 'Jasny styl',
            bodyClass: 'dark-theme'
        }
    };

    function saveTheme(theme) {
        localStorage.setItem('mapTheme', theme);
    }

    function loadTheme() {
        return localStorage.getItem('mapTheme') || 'light';
    }

    function applyTheme(themeName) {
        const theme = themes[themeName];
        const button = document.getElementById('theme-switcher');
        const icon = button.querySelector('.theme-icon');
        const text = button.querySelector('.theme-text');

        currentTheme = themeName;
        map.setStyle(theme.mapStyle);

        icon.textContent = theme.buttonIcon;
        text.textContent = theme.buttonText;

        document.body.className = 'h-full ' + theme.bodyClass;
        button.classList.toggle('dark', themeName === 'dark');

        saveTheme(themeName);
    }

    function toggleTheme() {
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = loadTheme();

        map = new maplibregl.Map({
            container: 'map',
            style: themes[savedTheme].mapStyle,
            center: [21.0122, 52.2297],
            zoom: 14,
            pitch: 60,
            bearing: 0,
            antialias: true
        });

        map.addControl(new maplibregl.NavigationControl({ showCompass: true }), 'top-right');

        applyTheme(savedTheme);

        map.on('load', () => {
            console.log('Mapa załadowana z 3D budynkami!');
        });
    });

    window.addEventListener('resize', () => map?.resize());
</script>

    @livewireScripts
</body>
</html>