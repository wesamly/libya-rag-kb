<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Knowledge Base Admin</title>

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #0d1117; /* GitHub dark background */
                color: #c9d1d9; /* GitHub dark text */
            }
        </style>

        <!-- Vite JS Entry Point for Admin -->
        @vite('resources/js/admin.js')
    </head>
    <body class="antialiased">
        <!-- 
          Your Vue.js Admin SPA will mount here.
          The dark theme is set on the <html> tag.
        -->
        <div id="admin-app"></div>
    </body>
</html>