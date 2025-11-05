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
                    // Contar notificaciones reales cuando se implemente
                    $notificacionesCount = 0;
                    if (session('organizacion_actual')) {
                        $notificacionesCount = \App\Models\VinculacionPendiente::where('organizacion_id', session('organizacion_actual'))
                            ->where('estado', 'pendiente')
                            ->count();
                    }
                @endphp
                @if($notificacionesCount > 0)
                <span class="absolute top-1 right-1 w-5 h-5 bg-danger rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ $notificacionesCount }}
                </span>
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
                        @if($notificacionesCount > 0)
                        <span class="bg-danger text-white text-xs px-2 py-0.5 rounded-full">{{ $notificacionesCount }}</span>
                        @endif
                    </div>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    @if($notificacionesCount > 0)
                        <!-- Notificación de usuarios pendientes -->
                        <a href="{{ route('usuarios.pendientes') }}" class="flex items-start p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-user-clock text-accent"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium">Usuarios pendientes</p>
                                <p class="text-xs text-secondary mt-1">{{ $notificacionesCount }} {{ $notificacionesCount == 1 ? 'usuario solicita' : 'usuarios solicitan' }} vinculación</p>
                                <p class="text-xs text-accent mt-1 font-medium">Click para revisar</p>
                            </div>
                        </a>
                    @else
                        <!-- Estado vacío -->
                        <div class="p-8 text-center">
                            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-secondary">No hay notificaciones nuevas</p>
                        </div>
                    @endif
                </div>

                @if($notificacionesCount > 0)
                <div class="p-3 border-t border-gray-200">
                    <a href="{{ route('usuarios.pendientes') }}" class="text-sm text-primary hover:text-primary-dark font-medium flex items-center justify-center">
                        Ver todas las notificaciones
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
                @endif
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
                            <i class="fas fa-crown mr-1"></i>Admin Global
                        </span>
                    @else
                        @php
                            $rolActual = auth()->user()->roles()
                                ->wherePivot('organizacion_id', session('organizacion_actual'))
                                ->first();
                        @endphp
                        @if($rolActual)
                        <span class="inline-block mt-2 text-xs bg-accent text-white px-2 py-0.5 rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $rolActual->nombre)) }}
                        </span>
                        @endif
                    @endif
                </div>

                <a href="{{ route('perfil') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
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

                <!-- Botón de Logout CORREGIDO -->
                <button 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="w-full flex items-center px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors text-left"
                >
                    <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
    // Función para toggle del menú móvil
    function toggleMobileMenu() {
        const sidebar = document.querySelector('aside');
        if (sidebar) {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
        }
    }

    // Cerrar menú móvil al hacer click fuera
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('aside');
        const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
        
        if (sidebar && !sidebar.contains(event.target) && !menuButton && !sidebar.classList.contains('hidden')) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }
    });
</script>
@endpush