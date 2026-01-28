<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'CustomTee')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ICON -->
    <link rel="apple-touch-icon" href="{{ asset('img/apple-icon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/templatemo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- FONT -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">

    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
</head>
<body>

    @include('client.layout.header')  
    
    @yield('content')                  

    @include('client.layout.footer')   

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
