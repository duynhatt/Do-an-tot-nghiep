<!DOCTYPE html>
<html lang="vi">

<head>
    @include('admin.component.header')
</head>

<body>

    @include('admin.component.navbar')

    <main>
        @yield('content')
    </main>

    @include('admin.component.footer')

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <script src="{{ asset('js/Admin/sb-admin-2.min.js') }}"></script>

</body>

</html>