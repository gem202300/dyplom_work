<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa atrakcji i noclegów</title>

    <link href="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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
        
        /* Кнопка перемикача теми - ЗМІНИВ: знизу, без написів */
        .theme-switcher {
            position: fixed;
            bottom: 40px;
            right: 10px;
            z-index: 100;
            background: white;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            padding: 10px;
            width: 50px;
            height: 50px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            user-select: none;
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
            font-size: 1.5em;
        }
        
        /* Кнопка відкриття фільтрів (коли панель прихована) */
        .open-filters-btn {
            position: fixed;
            top: 80px;
            left: 20px;
            z-index: 100;
            background: white;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
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
            display: flex; /* Прихована за замовчуванням */
        }
        .open-filters-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
            border-color: #007cbf;
        }
        .open-filters-btn.dark {
            background: #2d3748;
            color: white;
            border-color: #4a5568;
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
            min-width: 280px;
            max-width: 320px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            transition: all 0.3s ease;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .filter-panel.dark .filter-title {
            color: white;
        }
        
        /* Стилі для прокручуваної панелі */
        .filter-panel::-webkit-scrollbar {
            width: 6px;
        }
        .filter-panel::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .filter-panel.dark::-webkit-scrollbar-track {
            background: #374151;
        }
        .filter-panel::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        .filter-panel.dark::-webkit-scrollbar-thumb {
            background: #6b7280;
        }
        
        /* Стилі для випадаючих блоків - ЗМІНИВ: за замовчуванням не відкриті */
        .filter-collapsible {
            width: 100%;
            margin-bottom: 8px;
        }
        .filter-collapsible-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .filter-panel.dark .filter-collapsible-header {
            background: #374151;
            border-color: #4b5563;
            color: white;
        }
        .filter-collapsible-header:hover {
            background: #f1f5f9;
        }
        .filter-panel.dark .filter-collapsible-header:hover {
            background: #4b5563;
        }
        .filter-collapsible-header.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .filter-collapsible-icon {
            transition: transform 0.3s;
            font-size: 12px;
        }
        .filter-collapsible-header.active .filter-collapsible-icon {
            transform: rotate(180deg);
        }
        .filter-collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 12px;
        }
        .filter-collapsible-content.open {
            max-height: 300px;
            padding: 12px 12px 0 12px;
            overflow-y: auto;
        }
        
        /* Стилі для вкладок */
        .filter-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .filter-tab {
            flex: 1;
            padding: 8px 12px;
            text-align: center;
            background: #f1f5f9;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .filter-panel.dark .filter-tab {
            background: #374151;
            color: #d1d5db;
        }
        .filter-tab:hover {
            background: #e2e8f0;
        }
        .filter-panel.dark .filter-tab:hover {
            background: #4b5563;
        }
        .filter-tab.active {
            background: #3b82f6;
            color: white;
        }
        
        /* Стилі для полів введення */
        .filter-input, .filter-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: #333;
            box-sizing: border-box;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .filter-panel.dark .filter-input,
        .filter-panel.dark .filter-select {
            background: #374151;
            color: white;
            border-color: #4b5563;
        }
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Чекбокси категорій */
        .category-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            cursor: pointer;
            font-size: 13px;
            padding: 6px 8px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .category-checkbox:hover {
            background: #f9fafb;
        }
        .filter-panel.dark .category-checkbox:hover {
            background: #4b5563;
        }
        .category-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
        }
        
        /* Кнопки */
        .filter-button {
            width: 100%;
            padding: 10px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            margin-top: 5px;
        }
        .filter-button:hover {
            background: #2563eb;
        }
        .filter-button.reset {
            background: #ef4444;
        }
        .filter-button.reset:hover {
            background: #dc2626;
        }
        .filter-button.apply {
            background: #10b981;
        }
        .filter-button.apply:hover {
            background: #059669;
        }
        
        /* Активні фільтри */
        .active-filters {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
            border-left: 4px solid #3b82f6;
            font-size: 13px;
        }
        .filter-panel.dark .active-filters {
            background: #1e293b;
            border-color: #60a5fa;
        }
        .active-filters-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1e40af;
        }
        .filter-panel.dark .active-filters-title {
            color: #93c5fd;
        }
        .active-filter-item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #4b5563;
        }
        .filter-panel.dark .active-filter-item {
            color: #d1d5db;
        }
        
        /* Лічильники */
        .filter-counters {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 12px;
            color: #6b7280;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        .filter-panel.dark .filter-counters {
            border-top-color: #4b5563;
            color: #d1d5db;
        }
        
        /* Інші стилі (залишаються з оригіналу) */
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
        .maplibregl-popup-close-button {
            display: none !important;
        }
    </style>
</head>
<body class="h-full">
    <div class="nav-header">
        @livewire('navigation-menu')
    </div>

    <!-- Кнопка відкриття фільтрів (показується коли панель прихована) -->
    <button id="open-filters-btn" class="open-filters-btn">
        <i class="fas fa-filter"></i> Filtry
    </button>

    <!-- Панель фільтрів -->
    <div id="filter-panel" class="filter-panel" style="display: none;">
        <div class="filter-title">
            <span>Filtruj obiekty</span>
            <button id="close-filters" class="filter-button" style="width: auto; padding: 6px 12px; font-size: 12px;">
                <i class="fas fa-times"></i> Zamknij
            </button>
        </div>
        
        <!-- Вкладки для типу об'єктів -->
        <div class="filter-tabs">
            <div class="filter-tab active" data-tab="both">
                <i class="fas fa-layer-group"></i> Wszystkie
            </div>
            <div class="filter-tab" data-tab="nocleg">
                <i class="fas fa-bed"></i> Noclegi
            </div>
            <div class="filter-tab" data-tab="attraction">
                <i class="fas fa-landmark"></i> Atrakcje
            </div>
        </div>
        
        <!-- Загальні фільтри (для всіх вкладок) -->
        <div class="filter-collapsible">
            <!-- Фільтр за назвою - ЗМІНИВ: не відкритий за замовчуванням -->
            <div class="filter-collapsible-header">
                <span><i class="fas fa-search"></i> Szukaj po nazwie</span>
                <i class="fas fa-chevron-down filter-collapsible-icon"></i>
            </div>
            <div class="filter-collapsible-content">
                <input type="text" 
                       id="search-name" 
                       placeholder="Wpisz nazwę..." 
                       class="filter-input">
            </div>
        </div>
        
        <div class="filter-collapsible">
            <!-- Фільтр за рейтингом - ЗМІНИВ: не відкритий за замовчуванням -->
            <div class="filter-collapsible-header">
                <span><i class="fas fa-star"></i> Rating</span>
                <i class="fas fa-chevron-down filter-collapsible-icon"></i>
            </div>
            <div class="filter-collapsible-content">
                <select id="filter-rating" class="filter-select">
                    <option value="0">Dowolny rating</option>
                    <option value="1">1+ ★</option>
                    <option value="2">2+ ★</option>
                    <option value="3">3+ ★</option>
                    <option value="4">4+ ★</option>
                    <option value="4.5">4.5+ ★</option>
                </select>
            </div>
        </div>
        
        <!-- Фільтри для ночлегів (ТІЛЬКИ для вкладки Noclegi) -->
        <div class="filter-collapsible nocleg-filters" style="display: none;">
            <div class="filter-collapsible-header">
                <span><i class="fas fa-bed"></i> Noclegi - filtry</span>
                <i class="fas fa-chevron-down filter-collapsible-icon"></i>
            </div>
            <div class="filter-collapsible-content">
                <!-- Вмістимість -->
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">
                        Minimalna pojemność
                    </label>
                    <input type="number" 
                           id="filter-capacity" 
                           min="1" 
                           placeholder="Liczba osób" 
                           class="filter-input">
                </div>
                
                <!-- Тип об'єкту -->
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">
                        Typ obiektu
                    </label>
                    <select id="filter-object-type" class="filter-select">
                        <option value="">Wszystkie typy</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Фільтри для атракцій (ТІЛЬКИ для вкладки Atrakcje) -->
        <div class="filter-collapsible attraction-filters" style="display: none;">
            <div class="filter-collapsible-header">
                <span><i class="fas fa-landmark"></i> Atrakcje - filtry</span>
                <i class="fas fa-chevron-down filter-collapsible-icon"></i>
            </div>
            <div class="filter-collapsible-content">
                <!-- Категорії -->
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px;">
                        Kategorie
                    </label>
                    <div id="categories-checkboxes" style="max-height: 150px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f9fafb;">
                        <div style="text-align: center; padding: 10px; color: #999; font-size: 13px;">
                            Ładowanie kategorii...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Активні фільтри -->
        <div id="active-filters-info" class="active-filters" style="display: none;">
            <div class="active-filters-title">
                <i class="fas fa-filter"></i> Aktywne filtry
            </div>
            <div id="active-filters-list"></div>
        </div>
        
        <!-- Кнопки управління -->
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button id="apply-filters" class="filter-button apply">
                <i class="fas fa-check"></i> Zastosuj
            </button>
            <button id="reset-filters" class="filter-button reset">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>
        
        <!-- Лічильники -->
        <div class="filter-counters">
            <div>
                <i class="fas fa-bed"></i> Noclegi: <span id="nocleg-count">0</span>
            </div>
            <div>
                <i class="fas fa-landmark"></i> Atrakcje: <span id="attraction-count">0</span>
            </div>
            <div>
                <i class="fas fa-eye"></i> Widoczne: <span id="visible-count">0</span>
            </div>
        </div>
    </div>

    <!-- ЗМІНИВ: кнопка знизу, без написів -->
    <div id="theme-switcher" class="theme-switcher" onclick="toggleTheme()">
        <span class="theme-icon">🌙</span>
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
let filterPanelVisible = false;

// Додаємо дані фокусу з сервера
let focusData = @json($focusData ?? null);
let initialLoadComplete = false;

// Змінні для фільтрів
let objectTypes = [];
let categories = [];
let activeFilters = {
    activeTab: 'both',
    showNoclegi: true,
    showAtrakcje: true,
    searchName: '',
    minRating: 0,
    minCapacity: null,
    objectType: '',
    selectedCategories: []
};

const themes = {
    light: {
        mapStyle: 'https://api.maptiler.com/maps/streets-v2/style.json?key=uJDiq16jXWiNZLGeCJ0m',
        buttonIcon: '🌙',
        bodyClass: ''
    },
    dark: {
        mapStyle: 'https://api.maptiler.com/maps/streets-v2-dark/style.json?key=uJDiq16jXWiNZLGeCJ0m',
        buttonIcon: '☀️',
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
    const themeSwitcher = document.getElementById('theme-switcher');
    const openFiltersBtn = document.getElementById('open-filters-btn');
    const filterPanel = document.getElementById('filter-panel');
    const icon = themeSwitcher.querySelector('.theme-icon');
    
    currentTheme = themeName;
    
    if (map && isMapLoaded) {
        map.setStyle(theme.mapStyle);
    }

    icon.textContent = theme.buttonIcon;
    document.body.className = 'h-full ' + theme.bodyClass;
    
    themeSwitcher.classList.toggle('dark', themeName === 'dark');
    openFiltersBtn.classList.toggle('dark', themeName === 'dark');
    filterPanel.classList.toggle('dark', themeName === 'dark');

    saveTheme(themeName);
}

function toggleTheme() {
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    applyTheme(newTheme);
}

// Функції для управління панеллю фільтрів
function toggleFilterPanel() {
    const filterPanel = document.getElementById('filter-panel');
    const openFiltersBtn = document.getElementById('open-filters-btn');
    
    filterPanelVisible = !filterPanelVisible;
    
    if (filterPanelVisible) {
        filterPanel.style.display = 'block';
        openFiltersBtn.style.display = 'none';
    } else {
        filterPanel.style.display = 'none';
        openFiltersBtn.style.display = 'flex';
    }
}

// Функції для фільтрів
async function loadFiltersData() {
    try {
        // Завантажуємо типи об'єктів
        const typesResponse = await fetch('/api/object-types');
        if (typesResponse.ok) {
            objectTypes = await typesResponse.json();
            populateObjectTypes();
        } else {
            console.warn('Nie udało się załadować typów obiektów');
        }
        
        // Завантажуємо категорії
        const categoriesResponse = await fetch('/api/categories');
        if (categoriesResponse.ok) {
            categories = await categoriesResponse.json();
            populateCategories();
        } else {
            console.warn('Nie udało się załadować kategorii');
        }
    } catch (error) {
        console.error('Błąd ładowania danych filtrowania:', error);
    }
}

function populateObjectTypes() {
    const select = document.getElementById('filter-object-type');
    select.innerHTML = '<option value="">Wszystkie typy</option>';
    
    objectTypes.forEach(type => {
        const option = document.createElement('option');
        option.value = type.id;
        option.textContent = type.name;
        select.appendChild(option);
    });
}

function populateCategories() {
    const container = document.getElementById('categories-checkboxes');
    container.innerHTML = '';
    
    if (categories.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 10px; color: #999;">Brak kategorii</div>';
        return;
    }
    
    categories.forEach(category => {
        const label = document.createElement('label');
        label.className = 'category-checkbox';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = category.id;
        checkbox.className = 'category-checkbox-input';
        
        const span = document.createElement('span');
        span.textContent = category.name;
        
        label.appendChild(checkbox);
        label.appendChild(span);
        container.appendChild(label);
    });
}

function setupFilters() {
    // Завантажуємо дані для фільтрів
    loadFiltersData();
    
    // Обробники для випадаючих блоків
    document.querySelectorAll('.filter-collapsible-header').forEach(header => {
        header.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            content.classList.toggle('open');
        });
    });
    
    // Обробники для вкладок
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => {
                t.classList.remove('active');
            });
            
            this.classList.add('active');
            activeFilters.activeTab = this.dataset.tab;
            updateObjectVisibility();
            updateMap();
        });
    });
    
    // Обробники для полів введення
    document.getElementById('search-name').addEventListener('input', (e) => {
        activeFilters.searchName = e.target.value.toLowerCase().trim();
    });
    
    document.getElementById('filter-rating').addEventListener('change', (e) => {
        activeFilters.minRating = parseFloat(e.target.value) || 0;
    });
    
    document.getElementById('filter-capacity').addEventListener('input', (e) => {
        activeFilters.minCapacity = e.target.value ? parseInt(e.target.value) : null;
    });
    
    document.getElementById('filter-object-type').addEventListener('change', (e) => {
        activeFilters.objectType = e.target.value;
    });
    
    document.getElementById('categories-checkboxes').addEventListener('change', (e) => {
        if (e.target.classList.contains('category-checkbox-input')) {
            const checkedBoxes = document.querySelectorAll('.category-checkbox-input:checked');
            activeFilters.selectedCategories = Array.from(checkedBoxes).map(cb => cb.value);
        }
    });
    
    // Кнопка застосування фільтрів
    document.getElementById('apply-filters').addEventListener('click', () => {
        updateMap();
        updateActiveFiltersInfo();
        showFilterNotification();
    });
    
    // Кнопка скидання фільтрів
    document.getElementById('reset-filters').addEventListener('click', resetFilters);
    
    // Кнопка закриття фільтрів
    document.getElementById('close-filters').addEventListener('click', () => {
        toggleFilterPanel();
    });
    
    // Кнопка відкриття фільтрів
    document.getElementById('open-filters-btn').addEventListener('click', () => {
        toggleFilterPanel();
    });
    
    // Ініціалізація видимості об'єктів
    updateObjectVisibility();
}

function updateObjectVisibility() {
    // Показуємо/приховуємо спеціальні фільтри залежно від вкладки
    const noclegFilters = document.querySelector('.nocleg-filters');
    const attractionFilters = document.querySelector('.attraction-filters');
    
    switch (activeFilters.activeTab) {
        case 'nocleg':
            activeFilters.showNoclegi = true;
            activeFilters.showAtrakcje = false;
            noclegFilters.style.display = 'block';
            attractionFilters.style.display = 'none';
            break;
        case 'attraction':
            activeFilters.showNoclegi = false;
            activeFilters.showAtrakcje = true;
            noclegFilters.style.display = 'none';
            attractionFilters.style.display = 'block';
            break;
        case 'both':
        default:
            activeFilters.showNoclegi = true;
            activeFilters.showAtrakcje = true;
            noclegFilters.style.display = 'none';
            attractionFilters.style.display = 'none';
            break;
    }
}

function updateActiveFiltersInfo() {
    const activeFiltersList = document.getElementById('active-filters-list');
    const activeFiltersInfo = document.getElementById('active-filters-info');
    const filters = [];
    
    switch (activeFilters.activeTab) {
        case 'nocleg':
            filters.push({label: 'Tryb', value: 'Tylko noclegi'});
            break;
        case 'attraction':
            filters.push({label: 'Tryb', value: 'Tylko atrakcje'});
            break;
        case 'both':
            filters.push({label: 'Tryb', value: 'Wszystkie obiekty'});
            break;
    }
    
    if (activeFilters.searchName) {
        filters.push({label: 'Nazwa', value: `"${activeFilters.searchName}"`});
    }
    
    if (activeFilters.minRating > 0) {
        filters.push({label: 'Min. rating', value: `${activeFilters.minRating}+ ★`});
    }
    
    if (activeFilters.minCapacity) {
        filters.push({label: 'Min. pojemność', value: `${activeFilters.minCapacity} osób`});
    }
    
    if (activeFilters.objectType) {
        const typeName = objectTypes.find(t => t.id == activeFilters.objectType)?.name || '';
        if (typeName) {
            filters.push({label: 'Typ obiektu', value: typeName});
        }
    }
    
    if (activeFilters.selectedCategories.length > 0) {
        const categoryNames = categories
            .filter(c => activeFilters.selectedCategories.includes(c.id.toString()))
            .map(c => c.name)
            .join(', ');
        filters.push({label: 'Kategorie', value: categoryNames});
    }
    
    if (filters.length > 1) {
        activeFiltersList.innerHTML = filters.map(filter => 
            `<div class="active-filter-item">
                <span>${filter.label}:</span>
                <span style="font-weight: 500;">${filter.value}</span>
            </div>`
        ).join('');
        activeFiltersInfo.style.display = 'block';
    } else {
        activeFiltersInfo.style.display = 'none';
    }
}

function showFilterNotification() {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        bottom: 80px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        font-size: 14px;
        animation: slideIn 0.3s ease;
    `;
    
    notification.innerHTML = `<i class="fas fa-check"></i> Filtry zastosowane!`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

function resetFilters() {
    activeFilters = {
        activeTab: 'both',
        showNoclegi: true,
        showAtrakcje: true,
        searchName: '',
        minRating: 0,
        minCapacity: null,
        objectType: '',
        selectedCategories: []
    };
    
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector('.filter-tab[data-tab="both"]').classList.add('active');
    
    document.getElementById('search-name').value = '';
    document.getElementById('filter-rating').value = '0';
    document.getElementById('filter-capacity').value = '';
    document.getElementById('filter-object-type').value = '';
    
    document.querySelectorAll('.category-checkbox-input').forEach(cb => {
        cb.checked = false;
    });
    
    updateObjectVisibility();
    updateMap();
    updateActiveFiltersInfo();
    showFilterNotification();
}

function updateCounts() {
    const noclegCount = allFeatures.filter(f => f.properties.type === 'nocleg').length;
    const attractionCount = allFeatures.filter(f => f.properties.type === 'attraction').length;
    const visibleCount = currentGeoJSON ? currentGeoJSON.features.length : 0;
    
    document.getElementById('nocleg-count').textContent = noclegCount;
    document.getElementById('attraction-count').textContent = attractionCount;
    document.getElementById('visible-count').textContent = visibleCount;
}

function updateMap() {
    if (!map || !isMapLoaded || !allFeatures.length) return;
    
    if (!activeFilters.showNoclegi && !activeFilters.showAtrakcje) {
        clearMap();
        return;
    }
    
    const filteredFeatures = allFeatures.filter(feature => {
        const props = feature.properties;
        const type = props.type;
        
        if (type === 'nocleg' && !activeFilters.showNoclegi) return false;
        if (type === 'attraction' && !activeFilters.showAtrakcje) return false;
        
        if (activeFilters.searchName && props.title) {
            if (!props.title.toLowerCase().includes(activeFilters.searchName)) {
                return false;
            }
        }
        
        if (activeFilters.minRating > 0) {
            const rating = parseFloat(props.rating) || 0;
            if (rating < activeFilters.minRating) {
                return false;
            }
        }
        
        if (type === 'nocleg' && activeFilters.minCapacity) {
            const capacity = parseInt(props.capacity) || 0;
            if (capacity < activeFilters.minCapacity) {
                return false;
            }
        }
        
        if (type === 'nocleg' && activeFilters.objectType && props.object_type_id) {
            if (props.object_type_id.toString() !== activeFilters.objectType) {
                return false;
            }
        }
        
        if (type === 'attraction' && activeFilters.selectedCategories.length > 0) {
            if (!props.categories || props.categories.length === 0) {
                return false;
            }
            
            const hasSelectedCategory = props.categories.some(catId => 
                activeFilters.selectedCategories.includes(catId.toString())
            );
            
            if (!hasSelectedCategory) {
                return false;
            }
        }
        
        return true;
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
    
    updateCounts();
}

function clearMap() {
    if (map.getSource('places')) {
        map.getSource('places').setData({
            type: 'FeatureCollection',
            features: []
        });
    }
    currentGeoJSON = null;
    updateCounts();
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
        const img = new Image(25, 25);
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
        
        map.easeTo({ center: coordinates });
    });

    // Клік по атракції
    map.on('click', 'attraction-icons', (e) => {
        e.preventDefault();
        const coordinates = e.features[0].geometry.coordinates.slice();
        
        new maplibregl.Popup()
            .setLngLat(coordinates)
            .setHTML(createPopupContent(e.features[0]))
            .addTo(map);
        
        map.easeTo({ center: coordinates });
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
    
    // Додаємо інформацію про рейтинг для ВСІХ об'єктів, якщо він є
    if (props.rating && parseFloat(props.rating) > 0) {
        details += `<div class="popup-details">⭐ Ocena: ${props.rating}/5</div>`;
    }
    
    // Для ночлегів - додаємо додаткову інформацію
    if (type === 'nocleg') {
        if (props.capacity) {
            details += `<div class="popup-details">👥 Pojemność: ${props.capacity} osób</div>`;
        }
        if (props.object_type_name) {
            details += `<div class="popup-details">🏠 Typ: ${props.object_type_name}</div>`;
        }
    }
    
    // Для атракцій - інша додаткова інформація
    // if (type === 'attraction') {
        // Тут можна додати специфічні для атракцій деталі, якщо потрібно
    // }
    
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

function showCustomPopup(data) {
    const typeText = data.type === 'nocleg' ? 'NOCLEG' : 'ATRAKCJA';
    const typeClass = data.type === 'nocleg' ? 'nocleg' : 'attraction';
    const link = data.type === 'nocleg' 
        ? `/noclegi/${data.id}` 
        : `/attractions/${data.id}`;
    
    let details = '';
    
    // Додаємо рейтинг для всіх об'єктів
    if (data.rating && parseFloat(data.rating) > 0) {
        details += `<div class="popup-details">⭐ Ocena: ${data.rating}/5</div>`;
    }
    
    // Додатково для ночлегів
    if (data.type === 'nocleg' && data.capacity) {
        details += `<div class="popup-details">👥 Pojemność: ${data.capacity} osób</div>`;
    }
    
    const popupHTML = `
        <div class="popup-header">
            <div class="popup-type ${typeClass}">${typeText}</div>
            <div class="popup-title">${data.title}</div>
        </div>
        <div class="popup-content">
            ${data.description ? `<p>${data.description.substring(0, 100)}...</p>` : ''}
            ${details}
        </div>
        <a href="${link}" class="popup-link" target="_blank">Zobacz szczegóły</a>
    `;
    
    const coordinates = [parseFloat(data.lng), parseFloat(data.lat)];
    
    new maplibregl.Popup()
        .setLngLat(coordinates)
        .setHTML(popupHTML)
        .addTo(map);
}

function focusOnObject(data) {
    if (!map || !data) return;
    
    const coordinates = [parseFloat(data.lng), parseFloat(data.lat)];
    
    // Відразу центруємо карту на об'єкті
    map.jumpTo({
        center: coordinates,
        zoom: 16
    });
    
    // Показуємо власний поп-ап одразу
    setTimeout(() => {
        showCustomPopup(data);
    }, 300);
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
        
        // Застосовуємо початкові фільтри
        updateMap();
        updateActiveFiltersInfo();
        
        initialLoadComplete = true;
        
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

    let initialCenter = [21.0122, 52.2297];
    let initialZoom = 12;
    
    if (focusData) {
        initialCenter = [parseFloat(focusData.lng), parseFloat(focusData.lat)];
        initialZoom = 16;
    }

    map = new maplibregl.Map({
        container: 'map',
        style: themes[savedTheme].mapStyle,
        center: initialCenter,
        zoom: initialZoom,
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
        
        if (focusData) {
            setTimeout(() => {
                showCustomPopup(focusData);
            }, 500);
        }
        
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

// Додайте анімації для сповіщень
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

    @livewireScripts
</body>
</html>