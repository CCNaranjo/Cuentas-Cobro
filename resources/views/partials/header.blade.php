<!-- Top Header -->
<header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm flex-shrink-0">
    <!-- Mobile Menu Button -->
    <button class="lg:hidden text-gray-600 hover:text-primary" onclick="toggleMobileMenu()">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Search Bar -->
    <div class="flex-1 max-w-2xl mx-4">
        <div class="relative">
            <input 
                type="text" 
                placeholder="Buscar contratos, usuarios, documentos..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/20 transition-all"
            >
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-4">
        <!-- Selector de Organización (si no es admin global) -->
        @if(!auth()->user()->esAdminGlobal())
            @php
                $organizaciones = auth()->user()->organizacionesVinculadas;
                $orgActual = session('organizacion_actual');
            @endphp
            @if($organizaciones->count() > 1)
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open"
                    class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors border border-gray-300"
                >
                    <i class="fas fa-building text-primary"></i>
                    <span class="text-sm font-medium text-gray-700">
                        {{ $organizaciones->firstWhere('id', $orgActual)->nombre_oficial ?? 'Seleccionar' }}
                    </span>
                    <i class="fas fa-chevron-down text-xs text-gray-600"></i>
                </button>

                <div 
                    x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                    style="display: none;"
                >
                    @foreach($organizaciones as $org)
                    <a href="{{ route('cambiar-organizacion', $org->id) }}" 
                       class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 transition-colors {{ $org->id == $orgActual ? 'bg-accent/10 text-primary font-semibold' : 'text-gray-700' }}">
                        <i class="fas fa-building mr-2 text-accent"></i>
                        {{ $org->nombre_oficial }}
                        @if($org->id == $orgActual)
                            <i class="fas fa-check ml-auto text-accent"></i>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        @endif

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open"
                class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors"
            >
                <i class="fas fa-bell text-xl"></i>
                @php
                    $notificacionesCount = 3; // TODO: Implementar conteo real
                @endphp
                @if($notificacionesCount > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full animate-pulse"></span>
                @endif
            </button>

            <!-- Dropdown Notificaciones -->
            <div 
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                style="display: none;"
            >
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Notificaciones</h3>
                        <span class="bg-danger text-white text-xs px-2 py-0.5 rounded-full">{{ $notificacionesCount }}</span>
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <!-- Notificación ejemplo -->
                    <a href="#" class="flex items-start p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-user-check text-accent"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium">Nuevo usuario pendiente</p>
                            <p class="text-xs text-secondary mt-1">Juan Pérez solicita vinculación</p>
                            <p class="text-xs text-secondary mt-1">Hace 5 minutos</p>
                        </div>
                    </a>

                    <!-- Estado vacío -->
                    <div class="p-8 text-center">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-secondary">No hay notificaciones nuevas</p>
                    </div>
                </div>

                <div class="p-3 border-t border-gray-200">
                    <a href="#" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center justify-center">
                        Ver todas las notificaciones
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open"
                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
            >
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(auth()->user()->nombre, 0, 1) }}
                </div>
                <span class="text-sm font-medium text-gray-700 hidden md:block">{{ auth()->user()->nombre }}</span>
                <i class="fas fa-chevron-down text-gray-600 text-xs"></i>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                style="display: none;"
            >
                <!-- Info del Usuario -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->nombre }}</p>
                    <p class="text-xs text-secondary truncate">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->esAdminGlobal())
                        <span class="inline-block mt-2 text-xs bg-primary text-white px-2 py-0.5 rounded-full">
                            Admin Global
                        </span>
                    @endif
                </div>

                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user-circle mr-3 text-gray-400 w-5"></i>
                    Mi Perfil
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-cog mr-3 text-gray-400 w-5"></i>
                    Configuración
                </a>

                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-question-circle mr-3 text-gray-400 w-5"></i>
                    Ayuda
                </a>

                <hr class="my-2">

                <a 
                    href="{{ route('logout') }}" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors"
                >
                    <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>