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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
        
        /* Efecto glassmorphism para cards */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Input focus effect */
        .input-focus:focus {
            border-color: #00BCD4;
            box-shadow: 0 0 0 3px rgba(0, 188, 212, 0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased">
    @yield('content')
    
    @stack('scripts')
</body>
</html>