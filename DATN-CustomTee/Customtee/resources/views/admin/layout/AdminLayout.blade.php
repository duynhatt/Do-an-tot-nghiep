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
    <section id="container">
        <!-- Header -->
        <header class="header fixed-top clearfix">
            <div class="brand">
                <a href="{{ url('DashBoard') }}" class="logo">ADMIN</a>
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
                    <li>
                        <input type="text" class="form-control search" placeholder="Search">
                    </li>

                    <!-- User Dropdown -->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="username">John Doe</span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="#"><i class="fa fa-suitcase"></i> Profile</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                            <li><a href="login.html"><i class="fa fa-key"></i> Log Out</a></li>
                        </ul>
                    </li>
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
                            <a class="active" href="{{ url('DashBoard') }}">
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
                                <li><a href="#">Thêm mới biến thể</a></li>
                                <li><a href="#">Danh sách biến thể</a></li>
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

                        <!-- UI Elements -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-book"></i>
                                <span>UI Elements</span>
                            </a>
                            <ul class="sub">
                                <li><a href="typography.html">Typography</a></li>
                                <li><a href="glyphicon.html">Glyphicons</a></li>
                                <li><a href="grids.html">Grids</a></li>
                            </ul>
                        </li>

                        <!-- Font Awesome -->
                        <li>
                            <a href="fontawesome.html">
                                <i class="fa fa-bullhorn"></i>
                                <span>Font Awesome</span>
                            </a>
                        </li>

                        <!-- Data Tables -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-th"></i>
                                <span>Data Tables</span>
                            </a>
                            <ul class="sub">
                                <li><a href="basic_table.html">Basic Table</a></li>
                                <li><a href="responsive_table.html">Responsive Table</a></li>
                            </ul>
                        </li>

                        <!-- Form Components -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-tasks"></i>
                                <span>Form Components</span>
                            </a>
                            <ul class="sub">
                                <li><a href="form_component.html">Form Elements</a></li>
                                <li><a href="form_validation.html">Form Validation</a></li>
                                <li><a href="dropzone.html">Dropzone</a></li>
                            </ul>
                        </li>

                        <!-- Mail -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-envelope"></i>
                                <span>Mail</span>
                            </a>
                            <ul class="sub">
                                <li><a href="mail.html">Inbox</a></li>
                                <li><a href="mail_compose.html">Compose Mail</a></li>
                            </ul>
                        </li>

                        <!-- Charts -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Charts</span>
                            </a>
                            <ul class="sub">
                                <li><a href="chartjs.html">Chart.js</a></li>
                                <li><a href="flot_chart.html">Flot Charts</a></li>
                            </ul>
                        </li>

                        <!-- Maps -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Maps</span>
                            </a>
                            <ul class="sub">
                                <li><a href="google_map.html">Google Map</a></li>
                                <li><a href="vector_map.html">Vector Map</a></li>
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
                        <li>
                            <a href="login.html">
                                <i class="fa fa-user"></i>
                                <span>Login Page</span>
                            </a>
                        </li>
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