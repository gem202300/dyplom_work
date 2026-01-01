<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa atrakcji i noclegów</title>

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
        
        /* Панель фільтрів */
        .filter-panel {
            position: fixed;
            top: 80px;
            left: 20px;
            z-index: 100;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 220px;
            max-width: 300px;
        }
        .filter-panel.dark {
            background: #2d3748;
            color: white;
        }
        .filter-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .filter-panel.dark .filter-title {
            color: white;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .filter-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .filter-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #3b82f6;
        }
        .filter-count {
            background: #e5e7eb;
            color: #6b7280;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: auto;
        }
        .filter-panel.dark .filter-count {
            background: #4b5563;
            color: #d1d5db;
        }
        
        /* Стилі для поп-апів */
        .mapboxgl-popup-content {
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            max-width: 280px;
        }
        .popup-header {
            margin-bottom: 10px;
        }
        .popup-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .popup-type.nocleg {
            background-color: #dcfce7;
            color: #15803d;
        }
        .popup-type.attraction {
            background-color: #fef3c7;
            color: #92400e;
        }
        .popup-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1f2937;
        }
        .popup-content {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
            margin-bottom: 12px;
        }
        .popup-details {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 10px;
        }
        .popup-link {
            display: block;
            text-align: center;
            background-color: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .popup-link:hover {
            background-color: #2563eb;
        }
        
        /* Стилі для кластерів */
        .cluster-popup {
            font-size: 14px;
        }
        .cluster-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .cluster-counts {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .cluster-count-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 10px;
            border-radius: 6px;
            background-color: #f9fafb;
        }
        .cluster-count-type {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cluster-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .cluster-dot.nocleg {
            background-color: #10b981;
        }
        .cluster-dot.attraction {
            background-color: #f59e0b;
        }
        .cluster-dot.mixed {
            background-color: #9333ea;
        }
        
        /* Темна тема */
        .dark-theme .mapboxgl-popup-content {
            background: #2d3748;
            border: 1px solid #4a5568;
        }
        .dark-theme .popup-type.nocleg {
            background-color: #14532d;
            color: #86efac;
        }
        .dark-theme .popup-type.attraction {
            background-color: #92400e;
            color: #fbbf24;
        }
        .dark-theme .popup-title {
            color: #f3f4f6;
        }
        .dark-theme .popup-content {
            color: #d1d5db;
        }
        .dark-theme .popup-details {
            color: #9ca3af;
        }
        .dark-theme .popup-link {
            background-color: #4f46e5;
        }
        .dark-theme .popup-link:hover {
            background-color: #4338ca;
        }
        .dark-theme .cluster-count-item {
            background-color: #374151;
        }
        .dark-theme .cluster-title {
            color: #f3f4f6;
        }
    </style>
</head>
<body class="h-full">
    <div class="nav-header">
        @livewire('navigation-menu')
    </div>

    <!-- Панель фільтрів -->
    <div id="filter-panel" class="filter-panel">
        <div class="filter-title">Filtruj obiekty</div>
        
        <div class="filter-group">
            <label class="filter-label">
                <input type="checkbox" class="filter-checkbox" id="toggle-noclegi" checked>
                <span>Noclegi</span>
                <span class="filter-count" id="nocleg-count">0</span>
            </label>
            
            <label class="filter-label">
                <input type="checkbox" class="filter-checkbox" id="toggle-atrakcje" checked>
                <span>Atrakcje</span>
                <span class="filter-count" id="attraction-count">0</span>
            </label>
        </div>
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
    let isMapLoaded = false;
    let allFeatures = [];
    let currentGeoJSON = null;
    let loadedIcons = new Set();

    const themes = {
        light: {
            mapStyle: 'https://api.maptiler.com/maps/streets-v2/style.json?key=uJDiq16jXWiNZLGeCJ0m',
            buttonIcon: '🌙',
            buttonText: 'Ciemny styl',
            bodyClass: ''
        },
        dark: {
            mapStyle: 'https://api.maptiler.com/maps/streets-v2-dark/style.json?key=uJDiq16jXWiNZLGeCJ0m',
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
        const filterPanel = document.getElementById('filter-panel');
        const icon = button.querySelector('.theme-icon');
        const text = button.querySelector('.theme-text');
        
        currentTheme = themeName;
        
        if (map && isMapLoaded) {
            map.setStyle(theme.mapStyle);
        }

        icon.textContent = theme.buttonIcon;
        text.textContent = theme.buttonText;

        document.body.className = 'h-full ' + theme.bodyClass;
        button.classList.toggle('dark', themeName === 'dark');
        filterPanel.classList.toggle('dark', themeName === 'dark');

        saveTheme(themeName);
    }

    function toggleTheme() {
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
    }

    function setupFilters() {
        const toggleNoclegi = document.getElementById('toggle-noclegi');
        const toggleAtrakcje = document.getElementById('toggle-atrakcje');
        
        toggleNoclegi.addEventListener('change', updateMap);
        toggleAtrakcje.addEventListener('change', updateMap);
    }

    function updateCounts() {
        const noclegCount = allFeatures.filter(f => f.properties.type === 'nocleg').length;
        const attractionCount = allFeatures.filter(f => f.properties.type === 'attraction').length;
        
        document.getElementById('nocleg-count').textContent = noclegCount;
        document.getElementById('attraction-count').textContent = attractionCount;
    }

    function updateMap() {
        if (!map || !isMapLoaded || !allFeatures.length) return;
        
        const showNoclegi = document.getElementById('toggle-noclegi').checked;
        const showAtrakcje = document.getElementById('toggle-atrakcje').checked;
        
        if (!showNoclegi && !showAtrakcje) {
            clearMap();
            return;
        }
        
        const filteredFeatures = allFeatures.filter(feature => {
            const type = feature.properties.type;
            return (type === 'nocleg' && showNoclegi) || (type === 'attraction' && showAtrakcje);
        });
        
        currentGeoJSON = {
            type: 'FeatureCollection',
            features: filteredFeatures
        };
        
        if (map.getSource('places')) {
            map.getSource('places').setData(currentGeoJSON);
        } else {
            createMapSource(currentGeoJSON);
            setupMapLayers();
        }
    }

    function clearMap() {
        if (map.getSource('places')) {
            map.getSource('places').setData({
                type: 'FeatureCollection',
                features: []
            });
        }
        currentGeoJSON = null;
    }

    function createMapSource(geoJSON) {
        if (map.getSource('places')) {
            map.removeSource('places');
        }
        
        map.addSource('places', {
            type: 'geojson',
            data: geoJSON,
            cluster: true,
            clusterMaxZoom: 14,
            clusterRadius: 50,
            clusterProperties: {
                'noclegCount': ['+', ['case', ['==', ['get', 'type'], 'nocleg'], 1, 0]],
                'attractionCount': ['+', ['case', ['==', ['get', 'type'], 'attraction'], 1, 0]]
            }
        });
    }

    async function loadIconImage(iconUrl, iconName) {
        if (!iconUrl || loadedIcons.has(iconName)) {
            return Promise.resolve();
        }
        
        return new Promise((resolve, reject) => {
            const img = new Image(32, 32);
            img.crossOrigin = 'anonymous';
            img.src = iconUrl;
            
            img.onload = () => {
                if (!map.hasImage(iconName)) {
                    map.addImage(iconName, img);
                    loadedIcons.add(iconName);
                }
                resolve();
            };
            
            img.onerror = (err) => {
                console.warn('Nie udało się załadować ikony:', iconUrl, err);
                resolve();
            };
        });
    }

    function setupMapLayers() {
        if (!map.getSource('places')) {
            console.warn('Źródło "places" nie istnieje');
            return;
        }
        
        const layersToRemove = ['clusters', 'cluster-count', 'nocleg-icons', 'attraction-icons'];
        layersToRemove.forEach(layerId => {
            if (map.getLayer(layerId)) {
                map.removeLayer(layerId);
            }
        });
        
        // Основний шар для кластерів
        map.addLayer({
            id: 'clusters',
            type: 'circle',
            source: 'places',
            filter: ['has', 'point_count'],
            paint: {
                'circle-color': [
                    'case',
                    ['all', ['>', ['get', 'noclegCount'], 0], ['>', ['get', 'attractionCount'], 0]],
                    '#9333ea',
                    ['>', ['get', 'noclegCount'], 0],
                    '#10b981',
                    '#f59e0b'
                ],
                'circle-radius': [
                    'step',
                    ['get', 'point_count'],
                    20,
                    10, 30,
                    30, 40
                ],
                'circle-stroke-width': 2,
                'circle-stroke-color': '#ffffff'
            }
        });

        // Число в кластері
        map.addLayer({
            id: 'cluster-count',
            type: 'symbol',
            source: 'places',
            filter: ['has', 'point_count'],
            layout: {
                'text-field': '{point_count_abbreviated}',
                'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
                'text-size': 14
            },
            paint: {
                'text-color': '#ffffff'
            }
        });

        // Ночлеги (іконки)
        map.addLayer({
            id: 'nocleg-icons',
            type: 'symbol',
            source: 'places',
            filter: ['all',
                ['!', ['has', 'point_count']],
                ['==', ['get', 'type'], 'nocleg']
            ],
            layout: {
                'icon-image': ['case',
                    ['!=', ['get', 'icon_url'], null],
                    ['get', 'icon_url'],
                    'default-nocleg-icon'
                ],
                'icon-size': 0.6,
                'icon-allow-overlap': false
            }
        });

        // Атракції (іконки)
        map.addLayer({
            id: 'attraction-icons',
            type: 'symbol',
            source: 'places',
            filter: ['all',
                ['!', ['has', 'point_count']],
                ['==', ['get', 'type'], 'attraction']
            ],
            layout: {
                'icon-image': ['case',
                    ['!=', ['get', 'icon_url'], null],
                    ['get', 'icon_url'],
                    'default-attraction-icon'
                ],
                'icon-size': 0.6,
                'icon-allow-overlap': false
            }
        });

        setupMapEvents();
    }

    function setupMapEvents() {
        map.off('click', 'clusters');
        map.off('click', 'nocleg-icons');
        map.off('click', 'attraction-icons');
        map.off('dblclick', 'clusters');
        map.off('mouseenter');
        map.off('mouseleave');

        // Клік по кластеру
        map.on('click', 'clusters', (e) => {
            e.preventDefault();
            const features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
            if (features.length === 0) return;
            
            const popupContent = createClusterPopupContent(features[0]);
            const coordinates = e.features[0].geometry.coordinates.slice();
            
            new maplibregl.Popup()
                .setLngLat(coordinates)
                .setHTML(popupContent)
                .addTo(map);
        });

        // Клік по ночлегу
        map.on('click', 'nocleg-icons', (e) => {
            e.preventDefault();
            const coordinates = e.features[0].geometry.coordinates.slice();
            
            new maplibregl.Popup()
                .setLngLat(coordinates)
                .setHTML(createPopupContent(e.features[0]))
                .addTo(map);
        });

        // Клік по атракції
        map.on('click', 'attraction-icons', (e) => {
            e.preventDefault();
            const coordinates = e.features[0].geometry.coordinates.slice();
            
            new maplibregl.Popup()
                .setLngLat(coordinates)
                .setHTML(createPopupContent(e.features[0]))
                .addTo(map);
        });

        // Розгортання кластера при подвійному кліку
        map.on('dblclick', 'clusters', (e) => {
            const features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
            if (features.length === 0) return;
            
            const clusterId = features[0].properties.cluster_id;
            const source = map.getSource('places');
            
            source.getClusterExpansionZoom(clusterId, (err, zoom) => {
                if (err) {
                    console.error('Błąd rozwijania klastra:', err);
                    return;
                }
                
                map.easeTo({
                    center: features[0].geometry.coordinates,
                    zoom: zoom
                });
            });
        });

        // Зміна курсору
        map.on('mouseenter', ['clusters', 'nocleg-icons', 'attraction-icons'], () => {
            map.getCanvas().style.cursor = 'pointer';
        });
        
        map.on('mouseleave', ['clusters', 'nocleg-icons', 'attraction-icons'], () => {
            map.getCanvas().style.cursor = '';
        });
    }

    function createPopupContent(feature) {
        const props = feature.properties;
        const type = props.type;
        const typeText = type === 'nocleg' ? 'NOCLEG' : 'ATRAKCJA';
        const typeClass = type === 'nocleg' ? 'nocleg' : 'attraction';
        const link = type === 'nocleg' ? `/noclegi/${props.id}` : `/attractions/${props.id}`;
        
        let details = '';
        if (type === 'nocleg') {
            details = props.capacity ? `<div class="popup-details">👥 Pojemność: ${props.capacity} osób</div>` : '';
        } else {
            details = props.rating ? `<div class="popup-details">⭐ Ocena: ${props.rating}/5</div>` : '';
        }
        
        // Відображаємо іконку
        if (props.icon_url) {
            const iconName = props.icon_url;
            details += `<div class="popup-details" style="margin-top: 8px; display: flex; align-items: center; gap: 5px;">
                <span style="font-weight: 500;">Ikona:</span>
                <span style="font-size: 12px; color: #6b7280;">${iconName.replace(/_/g, ' ')}</span>
            </div>`;
        }
        
        return `
            <div class="popup-header">
                <div class="popup-type ${typeClass}">${typeText}</div>
                <div class="popup-title">${props.title}</div>
            </div>
            <div class="popup-content">
                ${props.address || ''}
                ${props.description ? `<p>${props.description.substring(0, 100)}...</p>` : ''}
                ${details}
            </div>
            <a href="${link}" class="popup-link" target="_blank">Zobacz szczegóły</a>
        `;
    }

    function createClusterPopupContent(feature) {
        const pointCount = feature.properties.point_count;
        const noclegCount = feature.properties.noclegCount || 0;
        const attractionCount = feature.properties.attractionCount || 0;
        
        let clusterTypeClass = 'mixed';
        let clusterTypeText = 'Klaster mieszany';
        
        if (noclegCount > 0 && attractionCount === 0) {
            clusterTypeClass = 'nocleg';
            clusterTypeText = 'Klaster noclegów';
        } else if (attractionCount > 0 && noclegCount === 0) {
            clusterTypeClass = 'attraction';
            clusterTypeText = 'Klaster atrakcji';
        }
        
        return `
            <div class="cluster-popup">
                <div class="popup-type ${clusterTypeClass}">${clusterTypeText}</div>
                <div class="cluster-title">${pointCount} obiektów</div>
                <div class="cluster-counts">
                    ${noclegCount > 0 ? `
                    <div class="cluster-count-item">
                        <div class="cluster-count-type">
                            <div class="cluster-dot nocleg"></div>
                            <span>Noclegi:</span>
                        </div>
                        <span>${noclegCount}</span>
                    </div>
                    ` : ''}
                    ${attractionCount > 0 ? `
                    <div class="cluster-count-item">
                        <div class="cluster-count-type">
                            <div class="cluster-dot attraction"></div>
                            <span>Atrakcje:</span>
                        </div>
                        <span>${attractionCount}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    async function loadMapData() {
        try {
            console.log('Pobieranie danych mapy...');
            const response = await fetch('/map-data');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Dane mapy załadowane:', data);
            
            if (!data.features || data.features.length === 0) {
                console.warn('Brak danych do wyświetlenia na mapie');
                return;
            }

            allFeatures = data.features;
            
            // Спочатку завантажуємо всі унікальні іконки
            const iconPromises = [];
            const uniqueIcons = new Set();
            
            allFeatures.forEach(feature => {
                if (feature.properties.icon_url) {
                    const iconUrl = feature.properties.icon_url;
                    if (!uniqueIcons.has(iconUrl)) {
                        uniqueIcons.add(iconUrl);
                        // Створюємо унікальне ім'я для іконки
                        const iconName = 'icon_' + iconUrl.replace(/[^a-zA-Z0-9]/g, '_');
                        iconPromises.push(loadIconImage(iconUrl, iconName));
                        // Оновлюємо властивість для використання в layer
                        feature.properties.icon_url = iconName;
                    } else {
                        // Знаходимо вже завантажене ім'я
                        const iconName = 'icon_' + iconUrl.replace(/[^a-zA-Z0-9]/g, '_');
                        feature.properties.icon_url = iconName;
                    }
                }
            });

            // Завантажуємо дефолтні іконки
            await loadIconImage('/images/map-icons/icons8-hotel-50.png', 'default-nocleg-icon');
            await loadIconImage('/images/map-icons/icons8-museum-50.png', 'default-attraction-icon');
            
            // Чекаємо завантаження всіх іконок
            await Promise.all(iconPromises);
            
            updateCounts();
            
            const showNoclegi = document.getElementById('toggle-noclegi').checked;
            const showAtrakcje = document.getElementById('toggle-atrakcje').checked;
            
            const filteredFeatures = allFeatures.filter(feature => {
                const type = feature.properties.type;
                return (type === 'nocleg' && showNoclegi) || (type === 'attraction' && showAtrakcje);
            });
            
            currentGeoJSON = {
                type: 'FeatureCollection',
                features: filteredFeatures
            };
            
            createMapSource(currentGeoJSON);
            setupMapLayers();
            
        } catch (error) {
            console.error('Błąd ładowania danych mapy:', error);
        }
    }

    function restoreMapLayers() {
        if (!map || !isMapLoaded || !currentGeoJSON) return;
        
        if (!map.getSource('places')) {
            createMapSource(currentGeoJSON);
        }
        
        setupMapLayers();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = loadTheme();

        map = new maplibregl.Map({
            container: 'map',
            style: themes[savedTheme].mapStyle,
            center: [21.0122, 52.2297],
            zoom: 12,
            pitch: 0,
            bearing: 0,
            antialias: true
        });

        map.addControl(new maplibregl.NavigationControl({ showCompass: true }), 'top-right');
        map.addControl(new maplibregl.ScaleControl(), 'bottom-left');

        applyTheme(savedTheme);
        setupFilters();

        map.on('load', async () => {
            console.log('Mapa załadowana');
            isMapLoaded = true;
            await loadMapData();
        });

        map.on('style.load', () => {
            console.log('Styl mapy załadowany, przywracam warstwy...');
            setTimeout(() => {
                restoreMapLayers();
            }, 100);
        });

        map.on('styledata', () => {
            if (isMapLoaded) {
                console.log('Dane stylu załadowane, przywracam warstwy...');
                setTimeout(() => {
                    restoreMapLayers();
                }, 100);
            }
        });
    });

    window.addEventListener('resize', () => {
        if (map && isMapLoaded) {
            map.resize();
        }
    });
    </script>

    @livewireScripts
</body>
</html>