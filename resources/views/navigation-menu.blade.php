<nav x-data="{ navOpen: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Логотип -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Десктопне меню -->
                <div class="hidden sm:flex items-center space-x-8 sm:-my-px sm:ms-10">
                    <!-- Загальні посилання для всіх авторизованих користувачів -->
                     <x-nav-link href="{{ route('map.index') }}">
                          Mapa
                      </x-nav-link>

                      <x-nav-link href="{{ route('attractions.index') }}">
                          Atrakcje
                      </x-nav-link>

                      <x-nav-link href="{{ route('noclegi.index') }}">
                          Noclegi
                      </x-nav-link>

                      {{-- ТІЛЬКИ ДЛЯ AUTH --}}
                      @auth
                    @if (Auth::user()->isOwner())
                        <x-nav-link href="{{ route('my-noclegi') }}" :active="request()->routeIs('my-noclegi')">
                            Moje noclegi
                        </x-nav-link>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                            {{ __('Użytkownicy') }}
                        </x-nav-link>

                        <x-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')">
                            Kategorie atrakcji
                        </x-nav-link>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                    @click.away="dropdownOpen = false"
                                    class="inline-flex items-center h-16 px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                Zgłoszenia
                                <svg :class="{'rotate-180': dropdownOpen}"
                                     class="ml-1 h-4 w-4 transition-transform duration-200"
                                     xmlns="http://www.w3.org/2000/svg"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="dropdownOpen"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="py-1">
                                    <x-dropdown-link href="{{ route('admin.noclegi.index') }}">
                                        Zgłoszone noclegi
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('admin.owner-requests.index') }}">
                                        Zgłoszenia właścicieli
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('admin.ratings.reports') }}">
                                        Zgłoszone komentarze
                                    </x-dropdown-link>
                                </div>
                            </div>
                        </div>
                    @endif
                  @endauth
                </div>
            </div>

            <!-- Правий бік: сповіщення + профіль АБО кнопки входу/реєстрації -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                @auth
                    <!-- Сповіщення -->
                    <div class="mr-2">
                        <livewire:notification-bell />
                    </div>

                    <!-- Профіль дропдаун -->
                    <div x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false" class="relative">
                        <button @click="open = !open"
                                class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                {{ Auth::user()->name }}
                                <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 origin-top-right bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <div class="block px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <a href="{{ route('profile.show') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ __('Profile') }}
                                </a>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <a href="{{ route('api-tokens.index') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('API Tokens') }}
                                    </a>
                                @endif

                                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-md mr-2">
                        Zaloguj się
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">
                        Zarejestruj się
                    </a>
                @endauth
            </div>

            <div class="sm:hidden flex items-center">
                <button @click="navOpen = !navOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': navOpen, 'inline-flex': !navOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !navOpen, 'inline-flex': navOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="navOpen" x-cloak class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('map.index') }}" :active="request()->routeIs('map.*')">
                    Mapa
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('attractions.index') }}" :active="request()->routeIs('attractions.*')">
                    Atrakcje
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('noclegi.index') }}" :active="request()->routeIs('noclegi.*')">
                    Noclegi
                </x-responsive-nav-link>

                @auth
                    @if (Auth::user()->isOwner())
                        <x-responsive-nav-link href="{{ route('my-noclegi') }}" :active="request()->routeIs('my-noclegi')">
                            Moje noclegi
                        </x-responsive-nav-link>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <x-responsive-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                            Użytkownicy
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')">
                            Kategorie atrakcji
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.noclegi.index') }}" :active="request()->routeIs('admin.noclegi.*')">
                            Zgłoszone noclegi
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.owner-requests.index') }}" :active="request()->routeIs('admin.owner-requests.*')">
                            Zgłoszenia właścicieli
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.ratings.reports') }}" :active="request()->routeIs('admin.ratings.reports')">
                            Zgłoszone komentarze
                        </x-responsive-nav-link>
                    @endif
                @endauth
            </div>

            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                @auth
                    <div class="flex items-center px-4">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <div class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </div>
                        @endif

                        <div>
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link href="{{ route('profile.show') }}">
                            Profil
                        </x-responsive-nav-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-responsive-nav-link href="{{ route('api-tokens.index') }}">
                                API Tokens
                            </x-responsive-nav-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                Wyloguj się
                            </x-responsive-nav-link>
                        </form>
                    </div>
                @else
                    <div class="space-y-2 px-4">
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            Zaloguj się
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md">
                            Zarejestruj się
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>