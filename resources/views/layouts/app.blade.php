<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SurePrice') }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/infoman.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        main {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: #f5f5f5;
            min-height: 100vh;
            width: calc(100% - 250px);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            main {
                margin-left: 0;
                width: 100%;
            }
            
            body {
                flex-direction: column;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        @include('include.header')
    </div>

    <main>
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js', 'resources/js/info-man.js'])
    
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
    
    <!-- Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    @stack('scripts')
</body>
</html>
