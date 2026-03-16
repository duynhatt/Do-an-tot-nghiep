<!DOCTYPE html>
<html lang="en">

<head>
    <title>FashionTee</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <style>
        .custom-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 250px;
            max-width: 320px;
            padding: 12px 16px;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            animation: slideIn 0.4s ease;
        }

        .custom-toast.success {
            background: #28a745;
        }

        .custom-toast.error {
            background: #dc3545;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-out {
            animation: fadeOut 0.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(50px);
            }
        }
    </style>


    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
        <div class="container d-flex justify-content-between align-items-center">

            <a class="navbar-brand text-success logo h1 align-self-center" href="{{ url('/') }}">
                FashionTee
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between"
                id="templatemo_main_nav">
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/About') }}">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/Shop') }}">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/Contact') }}">Contact</a>
                        </li>
                    </ul>
                </div>

                <div class="navbar align-self-center d-flex">

                    <div class="d-lg-none flex-sm-fill mt-3 mb-4 col-7 col-sm-auto pr-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputMobileSearch" placeholder="Search ...">
                            <div class="input-group-text">
                                <i class="fa fa-fw fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <a class="nav-icon d-none d-lg-inline" href="#" data-bs-toggle="modal"
                        data-bs-target="#templatemo_search">
                        <i class="fa fa-fw fa-search text-dark mr-2"></i>
                    </a>

                    @auth
                        <a class="nav-icon position-relative text-decoration-none" href="{{ route('gio-hang.index') }}"
                            title="Giỏ hàng">
                            <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                            @php
                                $cartCount = \App\Models\GioHang::where('nguoi_dung_id', auth()->id())
                                    ->dangTrongGio()
                                    ->whereNotNull('bien_the_id')
                                    ->count();
                            @endphp
                            @if ($cartCount > 0)
                                <span
                                    class="position-absolute top-0 left-100 translate-middle badge rounded-pill bg-danger">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>
                    @else
                        <a class="nav-icon position-relative text-decoration-none" href="{{ url('/login') }}"
                            title="Đăng nhập để xem giỏ hàng">
                            <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                        </a>
                    @endauth

                    <!-- Auth Links -->
                    @guest
                        <a class="btn btn-outline-success me-2" href="{{ url('/login') }}">Đăng nhập</a>
                        <a class="btn btn-outline-primary me-2" href="{{ url('/register') }}">Đăng ký</a>
                    @else
                        <div class="dropdown">
                            <a class="btn btn-light dropdown-toggle d-flex align-items-center" href="#"
                                id="userDropdown" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('img/default-avatar.png') }}"
                                    class="rounded-circle me-2" width="50" height="50" style="object-fit: cover">
                                {{ auth()->user()->name }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- Thông tin cá nhân -->
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fa fa-user me-2"></i> Thông tin cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('order') }}">
                                        <i class="fa fa-user me-2"></i> Đơn hàng của tôi
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <!-- Đăng xuất -->
                                <li>
                                    <form method="POST" action="{{ url('/logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest






                </div>
            </div>

        </div>
    </nav>
    <!-- Close Header -->

    @if (session('success'))
        <div class="custom-toast success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="custom-toast error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="q"
                        placeholder="Search ...">
                    <button type="submit" class="input-group-text bg-success text-light">
                        <i class="fa fa-fw fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        setTimeout(function() {
            const toasts = document.querySelectorAll('.custom-toast');
            toasts.forEach(function(toast) {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 500);
            });
        }, 3000);
    </script>
</body>
