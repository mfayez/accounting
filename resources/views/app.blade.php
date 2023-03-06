<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', Session()->get('locale', app()->getLocale())) }}"
    dir="{{Session()->get('locale', app()->getLocale()) == 'ar' ? 'rtl' : 'ltr'}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" 
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />

    <!-- Styles/Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @routes
    
</head>

<body class="font-sans antialiased">
    @inertia

    @env ('local')
    <!--<script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>-->
    @endenv
</body>

</html>