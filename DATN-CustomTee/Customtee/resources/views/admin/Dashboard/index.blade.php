@extends('admin.layout.AdminLayout')

@section('AdminContent')

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="{{ asset('AdminAssets/css/bootstrap.min.css') }}">


        <style>
            :root {
                --primary: #6366f1;
                --primary-dark: #4f46e5;
                --primary-light: #818cf8;
                --success: #10b981;
                --info: #3b82f6;
                --warning: #f59e0b;
                --danger: #ef4444;
                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-200: #e5e7eb;
                --gray-600: #4b5563;
                --gray-800: #1f2937;
                --dark: #111827;
            }

            [data-theme="dark"] {
                --gray-50: #111827;
                --gray-100: #1f2937;
                --gray-200: #374151;
                --gray-600: #d1d5db;
                --dark: #f3f4f6;
                background: #0f172a;
                color: #e5e7eb;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--gray-50);
                color: var(--dark);
                min-height: 100vh;
                transition: background 0.3s, color 0.3s;
            }

            .main-content {
                padding: 2rem 2.5rem;
                transition: margin-left 0.3s;
            }

            .card {
                border: none;
                border-radius: 1.25rem;
                background: white;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            }

            [data-theme="dark"] .card {
                background: #1e293b;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            }

            .stat-header {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                padding: 1.25rem 1.5rem;
                border-bottom: none;
            }

            .chart-container {
                background: white;
                border-radius: 1.25rem;
                padding: 1.75rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
                margin-bottom: 2.5rem;
            }

            [data-theme="dark"] .chart-container {
                background: #1e293b;
            }

            .btn-theme-toggle {
                position: fixed;
                top: 1.5rem;
                right: 2rem;
                z-index: 1000;
            }

            @media (max-width: 992px) {
                .sidebar {
                    width: 0;
                    overflow: hidden;
                }

                .main-content {
                    margin-left: 0;
                    padding: 1.5rem;
                }
            }

            #orderStatusModal .modal-header {
                background: #3c8dbc;
                color: white;
            }

            #orderStatusModal table {
                font-size: 14px;
            }

            #orderStatusModal tbody tr:hover {
                background: #f5f5f5;
                cursor: pointer;
            }

            .badge-status {
                padding: 6px 10px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
            }

            .status-cho_xac_nhan {
                background: #fef3c7;
                color: #b45309;
            }

            .status-dang_xu_ly {
                background: #dbeafe;
                color: #1d4ed8;
            }

            .status-dang_giao {
                background: #e0e7ff;
                color: #4338ca;
            }

            .status-da_giao {
                background: #cffafe;
                color: #0e7490;
            }

            .status-da_hoan_thanh {
                background: #dcfce7;
                color: #15803d;
            }

            .status-da_huy {
                background: #fee2e2;
                color: #b91c1c;
            }

            .badge-payment-paid {
                background: #dcfce7;
                color: #15803d;
                padding: 5px 10px;
                border-radius: 8px;
                font-size: 12px;
            }

            .badge-payment-unpaid {
                background: #fee2e2;
                color: #b91c1c;
                padding: 5px 10px;
                border-radius: 8px;
                font-size: 12px;
            }

            #orderStatusModal table {
                font-size: 14px;
            }

            #orderStatusModal th,
            #orderStatusModal td {
                white-space: nowrap;
                vertical-align: middle;
            }

            #orderStatusModal td:first-child a {
                font-weight: 600;
                color: #2563eb;
            }

            #orderStatusModal table {
                width: 100%;
                table-layout: auto;
            }

            #orderStatusModal th,
            #orderStatusModal td {
                white-space: nowrap;
                vertical-align: middle;
            }
        </style>
    </head>

    <body>
        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-1">Tổng quan doanh thu</h1>
                    <p class="text-muted mb-0">Khoảng thời gian: {{ $startDate->format('d/m/Y') }} →
                        {{ $endDate->format('d/m/Y') }}</p>
                </div>

                <div class="btn-group" role="group">
                    <a href="{{ route('admin.dashboard', ['period' => '7days']) }}"
                        class="btn btn-outline-secondary {{ $period === '7days' ? 'active' : '' }} px-4">7 ngày</a>
                    <a href="{{ route('admin.dashboard', ['period' => '30days']) }}"
                        class="btn btn-outline-secondary {{ $period === '30days' ? 'active' : '' }} px-4">30 ngày</a>
                    <a href="{{ route('admin.dashboard', ['period' => '90days']) }}"
                        class="btn btn-outline-secondary {{ $period === '90days' ? 'active' : '' }} px-4">90 ngày</a>
                </div>
            </div>

            <div class="row mb-5">

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="stat-header">
                            <div class="clearfix">
                                <div style="float:left">
                                    <h6 class="mb-1 text-white-75">Doanh thu</h6>
                                    <h3>₫ {{ $stats['revenue'] }}</h3>
                                </div>
                                <i class="fas fa-coins fa-2x text-white" style="float:right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="stat-header"
                            style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">
                            <div class="clearfix">
                                <div style="float:left">
                                    <h6>Đơn hàng</h6>
                                    <h3>{{ $stats['orders_count'] }}</h3>
                                </div>
                                <i class="fas fa-shopping-bag fa-2x text-white" style="float:right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="stat-header" style="background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);">
                            <div class="clearfix">
                                <div style="float:left">
                                    <h6>Khách mới</h6>
                                    <h3>{{ $stats['new_customers'] }}</h3>
                                </div>
                                <i class="fas fa-user-plus fa-2x text-white" style="float:right"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100">
                        <div class="stat-header"
                            style="background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);">
                            <div class="clearfix">
                                <div style="float:left">
                                    <h6>Tỷ lệ chuyển đổi</h6>
                                    <h3>{{ $stats['conversion_rate'] }}</h3>
                                </div>
                                <i class="fas fa-percentage fa-2x text-white" style="float:right"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mb-5">
                <h5 class="fw-semibold mb-3">Đơn hàng theo trạng thái</h5>
                <div class="row g-3">
                    @php
                        $statusLabels = [
                            'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
                            'dang_xu_ly' => ['Đang xử lý', 'info'],
                            'dang_giao' => ['Đang giao', 'primary'],
                            'da_giao' => ['Đã giao', 'info'],
                            'da_hoan_thanh' => ['Đã hoàn thành', 'success'],
                            'da_huy' => ['Đã hủy', 'danger'],
                        ];
                    @endphp
                    @foreach ($statusLabels as $statusKey => $label)
                        <div class="col-md-2">
                            <div class="card order-status-card" style="cursor:pointer" data-status="{{ $statusKey }}"
                                data-status-name="{{ $label[0] }}" data-toggle="modal" data-target="#orderStatusModal">

                                <div class="card-body text-center">
                                    <div class="text-muted small">{{ $label[0] }}</div>
                                    <div class="fw-bold text-{{ $label[1] }}">
                                        {{ $ordersByStatus[$statusKey] ?? 0 }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="chart-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-semibold mb-0">Doanh thu
                                {{ $period === '7days' ? '7 ngày' : ($period === '30days' ? '30 ngày' : '90 ngày') }} gần
                                nhất</h5>
                            <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                                <option value="{{ route('admin.dashboard', ['period' => '7days']) }}"
                                    {{ $period === '7days' ? 'selected' : '' }}>7 ngày</option>
                                <option value="{{ route('admin.dashboard', ['period' => '30days']) }}"
                                    {{ $period === '30days' ? 'selected' : '' }}>30 ngày</option>
                                <option value="{{ route('admin.dashboard', ['period' => '90days']) }}"
                                    {{ $period === '90days' ? 'selected' : '' }}>90 ngày</option>
                            </select>
                        </div>
                        <canvas id="revenueChart" height="140"></canvas>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="chart-container h-100">
                        <h5 class="fw-semibold mb-4">Cơ cấu doanh thu theo danh mục</h5>
                        <canvas id="categoryChart" height="180"></canvas>
                    </div>
                </div>

                <div class="col-12">
                    <div class="chart-container">
                        <h5 class="fw-semibold mb-4">Top sản phẩm bán chạy</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Số lượng bán</th>
                                        <th class="text-end">Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topProducts as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $product->ten_san_pham }}</strong></td>
                                            <td class="text-end">{{ number_format($product->total_quantity) }}</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($product->total_revenue, 0, ',', '.') }} ₫</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu trong
                                                khoảng thời gian này</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="chart-container">
                        <h5 class="fw-semibold mb-4">Top khách hàng theo doanh thu</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Khách hàng</th>
                                        <th>Đơn hàng</th>
                                        <th>Doanh thu</th>
                                        <th>% Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topCustomers as $index => $customer)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $customer->name }}</strong></td>
                                            <td>{{ number_format($customer->order_count) }}</td>
                                            <td class="fw-bold">{{ number_format($customer->total_revenue, 0, ',', '.') }}
                                                ₫</td>
                                            <td>
                                                <div class="progress" style="height:12px">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ $topCustomers->max('total_revenue') > 0 ? round(($customer->total_revenue / $topCustomers->max('total_revenue')) * 100, 1) : 0 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu khách
                                                hàng trong khoảng thời gian này</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="orderStatusModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-xl" style="width: auto">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                Danh sách đơn hàng - <span id="modalStatusName"></span>
                            </h5>
                        </div>

                        <div class="modal-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Khách hàng</th>
                                            <th>SĐT</th>
                                            <th class="text-end">Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thanh toán</th>
                                            <th>Ngày đặt</th>
                                        </tr>
                                    </thead>

                                    <tbody id="ordersTableBody"></tbody>

                                </table>

                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                <ul class="pagination" id="ordersPagination"></ul>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </main>

        <script>
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: @json($revenueByDate['labels']),
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: @json($revenueByDate['data']),
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: 'rgba(99, 102, 241, 0.15)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: 'rgba(99, 102, 241, 1)',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.04)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            const ctxCategory = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCategory, {
                type: 'doughnut',
                data: {
                    labels: @json($revenueByCategory['labels']),
                    datasets: [{
                        data: @json($revenueByCategory['data']),
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.9)',
                            'rgba(59, 130, 246, 0.9)',
                            'rgba(16, 185, 129, 0.9)',
                            'rgba(245, 158, 11, 0.9)',
                            'rgba(139, 92, 246, 0.9)',
                            'rgba(236, 72, 153, 0.9)',
                            'rgba(34, 197, 94, 0.9)'
                        ],
                        borderWidth: 0,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 13
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value.toLocaleString()} ₫ (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            let currentStatus = '';

            $('.order-status-card').click(function() {

                let status = $(this).data('status');
                let statusName = $(this).data('status-name');

                $('#modalStatusName').text(statusName);

                currentStatus = status;

                loadOrders(status, 1);

            });


            function loadOrders(status, page = 1) {

                $('#ordersTableBody').html(
                    '<tr><td colspan="7" class="text-center py-3">Đang tải...</td></tr>'
                );

                $.get("{{ route('admin.dashboard.orders-by-status') }}", {
                    status: status,
                    page: page
                }, function(res) {

                    let html = '';

                    const statusMap = {
                        'cho_xac_nhan': 'Chờ xác nhận',
                        'dang_xu_ly': 'Đang xử lý',
                        'dang_giao': 'Đang giao',
                        'da_giao': 'Đã giao',
                        'da_hoan_thanh': 'Đã hoàn thành',
                        'da_huy': 'Đã hủy'
                    };

                    if (res.data.length === 0) {

                        html = `<tr>
                    <td colspan="7" class="text-center text-muted py-4">
                    Không có đơn hàng
                    </td>
                    </tr>`;

                    } else {

                        res.data.forEach(order => {

                            let statusBadge = `
                <span class="badge-status status-${order.trang_thai}">
                    ${statusMap[order.trang_thai]}
                </span>
                `;

                            let paymentBadge = order.trang_thai_thanh_toan === 'da_thanh_toan' ?
                                '<span class="badge-payment-paid">Đã thanh toán</span>' :
                                '<span class="badge-payment-unpaid">Chưa thanh toán</span>';

                            html += `
                <tr onclick="window.location='/admin/don-hang/${order.id}'" style="cursor:pointer">

                <td>
                <a class="fw-semibold text-primary" href="/admin/don-hang/${order.id}">
                ${order.ma_don_hang}
                </a>
                </td>

                <td>${order.ten_nguoi_nhan}</td>

                <td>${order.so_dien_thoai_nhan_hang}</td>

                <td class="text-end fw-bold">
                ${Number(order.tong_tien).toLocaleString()} ₫
                </td>

                <td>${statusBadge}</td>

                <td>${paymentBadge}</td>

                <td>
                ${new Date(order.created_at).toLocaleDateString('vi-VN')}
                </td>

                </tr>`;
                        });
                    }

                    $('#ordersTableBody').html(html);

                    renderPagination(res);
                });
            }


            function renderPagination(res) {

                let html = '';

                if (res.last_page > 1) {

                    for (let i = 1; i <= res.last_page; i++) {

                        html += `
                            <li class="page-item ${i === res.current_page ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadOrders('${currentStatus}', ${i}); return false;">
                                ${i}
                                </a>
                            </li>`;
                    }

                }

                $('#ordersPagination').html(html);
            }
        </script>

    </body>
@endsection
