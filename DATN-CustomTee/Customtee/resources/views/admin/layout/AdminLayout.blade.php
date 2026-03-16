<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
        Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    <!-- JavaScript Hide URL Bar -->
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('AdminAssets/css/bootstrap.min.css') }}">

    <!-- Custom CSS -->
    <link href="{{ asset('AdminAssets/css/style.css') }}" rel='stylesheet' type='text/css' />
    <link href="{{ asset('AdminAssets/css/style-responsive.css') }}" rel="stylesheet" />

    <!-- Font CSS -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

    <!-- Font Awesome & Icons -->
    <link rel="stylesheet" href="{{ asset('AdminAssets/css/font.css') }}" type="text/css" />
    <link href="{{ asset('AdminAssets/css/font-awesome.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('AdminAssets/css/morris.css') }}" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Calendar CSS -->
    <link rel="stylesheet" href="{{ asset('AdminAssets/css/monthly.css') }}">

    <!-- jQuery & Required Scripts -->
    <script src="{{ asset('AdminAssets/js/jquery2.0.3.min.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/raphael-min.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/morris.js') }}"></script>
</head>

<body>
    <style>
        #sidebar{
    width: 219px;
}
.brand{
    width: 219px;
}
    </style>
    <section id="container">
    
        <!-- Header -->
        <header class="header fixed-top clearfix">
            <div class="brand">
                <a href="{{ url('admin') }}" class="logo">ADMIN</a>
                <div class="sidebar-toggle-box">
                    <div class="fa fa-bars"></div>
                </div>
            </div>

            <div class="nav notify-row" id="top_menu">
                <ul class="nav top-menu">
                    <!-- Notifications will be added here -->
                </ul>
            </div>

            <div class="top-nav clearfix">
                <ul class="nav pull-right top-menu">
                    <a class="active" href="{{ url('/') }}">
                                <i class="fa fa-dashboard"></i>
                                <span>fashitonTee</span>
                            </a>
                </ul>
            </div>
        </header>

        <!-- Sidebar -->
        <aside>
            <div id="sidebar" class="nav-collapse">
                <div class="leftside-navigation">
                    <ul class="sidebar-menu" id="nav-accordion">
                        <!-- Dashboard -->
                        <li>
                            <a class="active" href="{{ url('admin') }}">
                                <i class="fa fa-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <!-- Categories -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-list"></i>
                                <span>Danh mục</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('admin.danh-muc.index') }}">Danh mục</a></li>
                            </ul>
                        </li>

                        <!-- Attributes -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-tags"></i>
                                <span>Thuộc tính</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('admin.mau-sac.index') }}">Màu sắc</a></li>
                                <li><a href="{{ route('admin.kich-thuoc.index') }}">Kích thước</a></li>
                            </ul>
                        </li>

                        <!-- Variants -->
                     <li class="sub-menu">
    <a href="javascript:;">
        <i class="fa fa-random"></i>
        <span>Biến thể</span>
    </a>
    <ul class="sub">
        <li>
            <a href="{{ route('variants.create') }}">
                Thêm mới biến thể
            </a>
        </li>
        <li>
            <a href="{{ route('variants.index') }}">
                Danh sách biến thể
            </a>
        </li>
    </ul>
</li>

                        <!-- Products -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-shopping-bag"></i>
                                <span>Sản phẩm</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('admin.san-pham.index') }}">Danh sách sản phẩm</a></li>
                            </ul>
                        </li>

                        <!-- Orders -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Đơn hàng</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('admin.don-hang.index') }}">Danh sách đơn hàng</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-shopping-cart"></i>
                                <span>bình luận</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{ route('admin.binh-luan.index') }}"> bình luận</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
    <a href="javascript:;">
        <i class="fa fa-ticket"></i>
        <span>Quản lý Voucher</span>
    </a>
    <ul class="sub">
        <li><a href="{{ route('admin.vouchers.index') }}">Danh sách Voucher</a></li>
        <li><a href="{{ route('admin.vouchers.create') }}">Thêm Voucher mới</a></li>
    </ul>
</li>
                        <!-- Extra Pages -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-glass"></i>
                                <span>Extra</span>
                            </a>
                            <ul class="sub">
                                <li><a href="gallery.html">Gallery</a></li>
                                <li><a href="404.html">404 Error</a></li>
                                <li><a href="registration.html">Registration</a></li>
                            </ul>
                        </li>
                        <!-- Login -->
                        
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <section id="main-content">
            <section class="wrapper">
                @yield('AdminContent')
            </section>
        </section>
    </section>

    <!-- Core Scripts -->
    <script src="{{ asset('AdminAssets/js/bootstrap.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/jquery.dcjqaccordion.2.7.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/scripts.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('AdminAssets/js/jquery.scrollTo.js') }}"></script>

    <!-- Toastr Notifications -->
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

    <!-- Calendar -->
    <script type="text/javascript" src="{{ asset('AdminAssets/js/monthly.js') }}"></script>
    <script type="text/javascript">
        $(window).load(function() {
            $('#mycalendar').monthly({
                mode: 'event'
            });

            $('#mycalendar2').monthly({
                mode: 'picker',
                target: '#mytarget',
                setWidth: '250px',
                startHidden: true,
                showTrigger: '#mytarget',
                stylePast: true,
                disablePast: true
            });

            // Protocol check for local development
            switch (window.location.protocol) {
                case 'http:':
                case 'https:':
                    break;
                case 'file:':
                    alert('Just a heads-up, events will not work when run locally.');
                    break;
            }
        });
    </script>

    <!-- Custom Dashboard Scripts -->
    <script>
        $(document).ready(function() {
            // Box Button Show/Hide Animation
            jQuery('.small-graph-box').hover(function() {
                jQuery(this).find('.box-button').fadeIn('fast');
            }, function() {
                jQuery(this).find('.box-button').fadeOut('fast');
            });

            jQuery('.small-graph-box .box-close').click(function() {
                jQuery(this).closest('.small-graph-box').fadeOut(200);
                return false;
            });

            // Morris Area Chart
            function gd(year, day, month) {
                return new Date(year, month - 1, day).getTime();
            }

            var graphArea2 = Morris.Area({
                element: 'hero-area',
                padding: 10,
                behaveLikeLine: true,
                gridEnabled: false,
                gridLineColor: '#dddddd',
                axes: true,
                resize: true,
                smooth: true,
                pointSize: 0,
                lineWidth: 0,
                fillOpacity: 0.85,
                data: [{
                        period: '2015 Q1',
                        iphone: 2668,
                        ipad: null,
                        itouch: 2649
                    },
                    {
                        period: '2015 Q2',
                        iphone: 15780,
                        ipad: 13799,
                        itouch: 12051
                    },
                    {
                        period: '2015 Q3',
                        iphone: 12920,
                        ipad: 10975,
                        itouch: 9910
                    },
                    {
                        period: '2015 Q4',
                        iphone: 8770,
                        ipad: 6600,
                        itouch: 6695
                    },
                    {
                        period: '2016 Q1',
                        iphone: 10820,
                        ipad: 10924,
                        itouch: 12300
                    },
                    {
                        period: '2016 Q2',
                        iphone: 9680,
                        ipad: 9010,
                        itouch: 7891
                    },
                    {
                        period: '2016 Q3',
                        iphone: 4830,
                        ipad: 3805,
                        itouch: 1598
                    },
                    {
                        period: '2016 Q4',
                        iphone: 15083,
                        ipad: 8977,
                        itouch: 5185
                    },
                    {
                        period: '2017 Q1',
                        iphone: 10697,
                        ipad: 4470,
                        itouch: 2038
                    }
                ],
                lineColors: ['#eb6f6f', '#926383', '#eb6f6f'],
                xkey: 'period',
                redraw: true,
                ykeys: ['iphone', 'ipad', 'itouch'],
                labels: ['All Visitors', 'Returning Visitors', 'Unique Visitors'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });
        });
    </script>

    @yield('scripts')

</body>

</html>