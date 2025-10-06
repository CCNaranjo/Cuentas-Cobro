<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ARCA-D')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#004AAD',
                        'primary-dark': '#003580',
                        'accent': '#00BCD4',
                        'warning': '#FD7E14',
                        'danger': '#DC3545',
                        'secondary': '#6C757D',
                        'bg-main': '#F8F9FA',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Animaciones personalizadas */
        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateX(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }
        
        .animate-slideIn {
            animation: slideIn 0.3s ease-out;
        }

        /* Estilos para sidebar links */
        .sidebar-link {
            @apply relative flex items-center space-x-3 px-4 py-3 text-white rounded-lg transition-all duration-200 mb-1;
        }
        
        .sidebar-link:hover {
            @apply bg-primary-dark/50;
        }
        
        .sidebar-link.active {
            @apply bg-primary-dark/80 font-semibold;
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #004AAD;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #003580;
        }

        /* Efecto hover para cards */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Input focus effect */
        .input-focus:focus {
            border-color: #00BCD4;
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }

        /* Badge styles */
        .badge-primary {
            @apply bg-primary text-white text-xs font-semibold px-2 py-1 rounded-full;
        }

        .badge-success {
            @apply bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full;
        }

        .badge-warning {
            @apply bg-warning text-white text-xs font-semibold px-2 py-1 rounded-full;
        }

        .badge-danger {
            @apply bg-danger text-white text-xs font-semibold px-2 py-1 rounded-full;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-bg-main">
    @yield('content')
    
    @stack('scripts')
</body>
</html>