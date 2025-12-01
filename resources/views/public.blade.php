<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Libya Knowledge Base</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            /* Simple body font styling */
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8f9fa;
            }
            
            /* Styles for the sticky Table of Contents */
            .sticky-toc {
                position: -webkit-sticky;
                position: sticky;
                top: 2rem; /* Adjust as needed */
                height: calc(100vh - 4rem); /* Full height minus top/bottom margin */
                overflow-y: auto;
            }
        </style>

        <!-- Vite JS Entry Point -->
        @vite('resources/js/app.js')
    </head>
    <body class="antialiased">
        
        <!-- 
          Your Vue.js application will mount here.
          Laravel only provides this single div.
        -->
        <div id="app"></div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>