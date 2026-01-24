<!DOCTYPE html>
<html lang="vi">

<head>
    @include('admin.component.header')
</head>

<body id="page-top">

    <div id="wrapper">

        {{-- SIDEBAR --}}
        @include('admin.component.navbar')

        {{-- CONTENT WRAPPER --}}
        <div id="content-wrapper" class="d-flex flex-column">

            {{-- MAIN CONTENT --}}
            <div id="content">
                <div class="container-fluid pt-4">
                    @yield('content')
                </div>
            </div>

            {{-- FOOTER --}}
            @include('admin.component.footer')

        </div>
    </div>

    {{-- JS --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/Admin/sb-admin-2.min.js') }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "1500",
            "extendedTimeOut": "7000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
    @yield('scripts')

</body>

</html>