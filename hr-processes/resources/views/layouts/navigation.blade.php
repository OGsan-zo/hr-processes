<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- LIENS RH - VISIBLE POUR ADMIN ET MANAGER --}}
                    @auth
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <x-nav-link :href="route('candidats.index')" :active="request()->routeIs('candidats.*')">
                                📋 Candidats
                            </x-nav-link>
                            
                            <x-nav-link :href="route('candidats.classify')" :active="request()->routeIs('candidats.classify')">
                                📊 Classement
                            </x-nav-link>
                        @endif

                        {{-- Annonces - VISIBLE POUR ADMIN ET MANAGER --}}
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <x-nav-link :href="route('annonces.index')" :active="request()->routeIs('annonces.*')">
                                📢 Annonces
                            </x-nav-link>
                        @endif

                        {{-- Candidatures/Sélections - VISIBLE POUR ADMIN ET MANAGER --}}
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <x-nav-link :href="route('candidatures.selection')" :active="request()->routeIs('candidatures.selection')">
                                ✅ Sélections
                            </x-nav-link>
                        @endif

                        {{-- Employés - VISIBLE POUR ADMIN SEULEMENT --}}
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('employes.create')" :active="request()->routeIs('employes.*')">
                                👥 Employés
                            </x-nav-link>
                        @endif

                        {{-- Profil Employé - VISIBLE POUR EMPLOYÉ SEULEMENT --}}
                        @if(auth()->user()->hasRole('employe'))
                            <x-nav-link :href="route('employes.profile')" :active="request()->routeIs('employes.profile')">
                                👤 Mon Profil
                            </x-nav-link>
                        @endif

                        {{-- Entretiens - VISIBLE POUR ADMIN SEULEMENT --}}
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('entretiens.index')" :active="request()->routeIs('entretiens.*')">
                                📅 Entretiens
                            </x-nav-link>
                        @endif

                        {{-- Contrats - VISIBLE POUR ADMIN SEULEMENT --}}
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('contrats.index')" :active="request()->routeIs('contrats.*')">
                                📋 Contrats
                            </x-nav-link>
                        @endif

                        {{-- CV Analysis - Visible pour Admin et Manager --}}
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <x-nav-link :href="route('cvs.index')" :active="request()->routeIs('cvs.*')">
                                📄 Analyse CV
                            </x-nav-link>
                        @endif

                        {{-- Tests QCM - Visible pour Admin et Manager --}}
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <x-nav-link :href="route('tests.index')" :active="request()->routeIs('tests.*')">
                                🧪 Tests QCM
                            </x-nav-link>
                        @endif

                        {{-- Affiliations - Visible pour Admin seulement --}}
                        @if(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('affiliations.index')" :active="request()->routeIs('affiliations.*')">
                                🏢 Affiliations
                            </x-nav-link>
                        @endif

                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 rounded-md focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- RÔLE UTILISATEUR --}}
                            <x-dropdown-link class="text-sm text-gray-900 dark:text-gray-100 leading-4 font-medium">
                                Rôle : {{ auth()->user()->roles->first()->name ?? 'Non assigné' }}
                            </x-dropdown-link>
                            <hr class="my-2 border-gray-200 dark:border-gray-600">
                            
                            {{-- LIENS SUPPLÉMENTAIRES --}}
                            <x-dropdown-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            {{-- LIENS ADMIN SPÉCIFIQUES --}}
                            @if(auth()->user()->hasRole('admin'))
                                <x-dropdown-link href="{{ route('contrats.index') }}">
                                    📋 Tous les contrats
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('entretiens.index') }}">
                                    📅 Tous les entretiens
                                </x-dropdown-link>
                            @endif

                            {{-- SÉPARATEUR ET LOGOUT --}}
                            <hr class="my-2 border-gray-200 dark:border-gray-600">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- LIENS RH RESPONSIVE --}}
            @auth
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <x-responsive-nav-link :href="route('candidats.index')" :active="request()->routeIs('candidats.*')">
                        📋 Candidats
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('candidats.classify')" :active="request()->routeIs('candidats.classify')">
                        📊 Classement
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <x-responsive-nav-link :href="route('annonces.index')" :active="request()->routeIs('annonces.*')">
                        📢 Annonces
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <x-responsive-nav-link :href="route('candidatures.selection')" :active="request()->routeIs('candidatures.selection')">
                        ✅ Sélections
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('employes.create')" :active="request()->routeIs('employes.*')">
                        👥 Employés
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('employe'))
                    <x-responsive-nav-link :href="route('employes.profile')" :active="request()->routeIs('employes.profile')">
                        👤 Mon Profil
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('entretiens.index')" :active="request()->routeIs('entretiens.*')">
                        📅 Entretiens
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('contrats.index')" :active="request()->routeIs('contrats.*')">
                        📋 Contrats
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <x-responsive-nav-link :href="route('cvs.index')" :active="request()->routeIs('cvs.*')">
                        📄 Analyse CV
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tests.index')" :active="request()->routeIs('tests.*')">
                        🧪 Tests QCM
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('affiliations.index')" :active="request()->routeIs('affiliations.*')">
                        🏢 Affiliations
                    </x-responsive-nav-link>
                @endif

            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                {{-- RÔLE --}}
                <div class="px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-md">
                    Rôle: {{ auth()->user()->roles->first()->name ?? 'Non assigné' }}
                </div>

                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link href="{{ route('contrats.index') }}">
                        📋 Tous les contrats
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>