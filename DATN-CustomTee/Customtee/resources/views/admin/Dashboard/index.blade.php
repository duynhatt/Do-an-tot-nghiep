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

            .status-da_nhan_hang {
                background: #e0e7ff;
                color: #3730a3;
            }

            .status-da_hoan_thanh {
                background: #dcfce7;
                color: #15803d;
            }

            .status-da_huy {
                background: #fee2e2;
                color: #b91c1c;
            }

            .status-tra_hang {
                background: #f3f4f6;
                color: #374151;
            }

            @media (min-width: 1200px) {
                .status-summary-row {
                    display: flex;
                    flex-wrap: nowrap;
                }

                .status-summary-col {
                    flex: 0 0 12.5%;
                    max-width: 12.5%;
                }
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

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle px-4" type="button"
                            id="revenueFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Tùy chọn
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="revenueFilterDropdown"
                            style="min-width: 560px; z-index: 2050;">
                            <form class="row g-3 align-items-end" method="GET"
                                action="{{ route('admin.dashboard') }}">
                                <input type="hidden" name="group" value="{{ $groupBy }}">
                                <div class="col-md-6 col-12">
                                    <label class="form-label mb-1">Từ ngày</label>
                                    <input type="date" name="from" class="form-control form-control-sm"
                                        value="{{ $startDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label mb-1">Đến ngày</label>
                                    <input type="date" name="to" class="form-control form-control-sm"
                                        value="{{ $endDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Áp dụng</button>
                                    <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                                        class="btn btn-outline-secondary btn-sm px-4">Đặt lại bộ lọc</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <a href="{{ route('admin.dashboard', ['period' => 'today']) }}"
                        class="btn btn-outline-secondary {{ $period === 'today' ? 'active' : '' }} px-4">Hôm nay</a>
                    <a href="{{ route('admin.dashboard', ['period' => '7days']) }}"
                        class="btn btn-outline-secondary {{ $period === '7days' ? 'active' : '' }} px-4">7 ngày</a>
                    <a href="{{ route('admin.dashboard', ['period' => '30days']) }}"
                        class="btn btn-outline-secondary {{ $period === '30days' ? 'active' : '' }} px-4">30 ngày</a>
                    <a href="{{ route('admin.dashboard', ['period' => '90days']) }}"
                        class="btn btn-outline-secondary {{ $period === '90days' ? 'active' : '' }} px-4">90 ngày</a>
                    <a href="{{ route('admin.dashboard', ['period' => 'thisyear']) }}"
                        class="btn btn-outline-secondary {{ $period === 'thisyear' ? 'active' : '' }} px-4">Năm nay</a>
                </div>
            </div>

            <div class="row mb-5">

                <div class="col">
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

                <div class="col">
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

                <div class="col">
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

                <div class="col">

                    <div class="card h-100 low-stock-card" style="cursor:pointer" data-toggle="modal"
                        data-target="#lowStockModal">

                        <div class="stat-header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">

                            <div class="clearfix">

                                <div style="float:left">
                                    <h6>Sắp hết hàng</h6>
                                    <h3>{{ $lowStockCount }}</h3>
                                </div>

                                <i class="fas fa-exclamation-triangle fa-2x text-white" style="float:right"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="mb-5">
                <h5 class="fw-semibold mb-3">Đơn hàng theo trạng thái</h5>
                <div class="row g-3 status-summary-row">
                    @php
                        $statusLabels = [
                            'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
                            'dang_xu_ly' => ['Đang xử lý', 'info'],
                            'dang_giao' => ['Đang giao', 'primary'],
                            'da_giao' => ['Đã giao', 'info'],
                            'da_nhan_hang' => ['Đã nhận hàng', 'primary'],
                            'da_hoan_thanh' => ['Đã hoàn thành', 'success'],
                            'da_huy' => ['Đã hủy', 'danger'],
                            'tra_hang' => ['Trả hàng', 'secondary'],
                        ];
                    @endphp
                    @foreach ($statusLabels as $statusKey => $label)
                        <div class="col-md-4 col-sm-6 status-summary-col">
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
                            @php
                                $currentGroupBy = (string) ($groupBy ?? 'day');
                                $groupLabels = [
                                    'day' => 'Ngày',
                                    'week' => 'Tuần',
                                    'month' => 'Tháng',
                                    'year' => 'Năm',
                                ];
                                $groupLabel = $groupLabels[$currentGroupBy] ?? 'Ngày';
                            @endphp
                            <h5 class="fw-semibold mb-0">Doanh thu theo {{ $groupLabel }}
                                ({{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }})</h5>
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                                    <option
                                        value="{{ route('admin.dashboard', ['from' => $startDate->format('Y-m-d'), 'to' => $endDate->format('Y-m-d'), 'group' => $currentGroupBy]) }}"
                                        {{ $period === 'custom' ? 'selected' : '' }}>Tùy chọn</option>
                                    <option value="{{ route('admin.dashboard', ['period' => 'today', 'group' => $currentGroupBy]) }}"
                                        {{ $period === 'today' ? 'selected' : '' }}>Hôm nay</option>
                                    <option value="{{ route('admin.dashboard', ['period' => '7days', 'group' => $currentGroupBy]) }}"
                                        {{ $period === '7days' ? 'selected' : '' }}>7 ngày</option>
                                    <option value="{{ route('admin.dashboard', ['period' => '30days', 'group' => $currentGroupBy]) }}"
                                        {{ $period === '30days' ? 'selected' : '' }}>30 ngày</option>
                                    <option value="{{ route('admin.dashboard', ['period' => '90days', 'group' => $currentGroupBy]) }}"
                                        {{ $period === '90days' ? 'selected' : '' }}>90 ngày</option>
                                    <option value="{{ route('admin.dashboard', ['period' => 'thisyear', 'group' => $currentGroupBy]) }}"
                                        {{ $period === 'thisyear' ? 'selected' : '' }}>Năm nay</option>
                                </select>

                                <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                                    @php
                                        $presetPeriods = ['today', '7days', '30days', '90days', 'thisyear'];
                                        $isPresetPeriod = in_array($period, $presetPeriods, true);
                                    @endphp
                                    <option
                                        value="{{ $isPresetPeriod
                                            ? route('admin.dashboard', ['period' => $period, 'group' => 'day'])
                                            : route('admin.dashboard', ['from' => $startDate->format('Y-m-d'), 'to' => $endDate->format('Y-m-d'), 'group' => 'day'])
                                        }}"
                                        {{ $currentGroupBy === 'day' ? 'selected' : '' }}>Ngày</option>
                                    <option
                                        value="{{ $isPresetPeriod
                                            ? route('admin.dashboard', ['period' => $period, 'group' => 'week'])
                                            : route('admin.dashboard', ['from' => $startDate->format('Y-m-d'), 'to' => $endDate->format('Y-m-d'), 'group' => 'week'])
                                        }}"
                                        {{ $currentGroupBy === 'week' ? 'selected' : '' }}>Tuần</option>
                                    <option
                                        value="{{ $isPresetPeriod
                                            ? route('admin.dashboard', ['period' => $period, 'group' => 'month'])
                                            : route('admin.dashboard', ['from' => $startDate->format('Y-m-d'), 'to' => $endDate->format('Y-m-d'), 'group' => 'month'])
                                        }}"
                                        {{ $currentGroupBy === 'month' ? 'selected' : '' }}>Tháng</option>
                                    <option
                                        value="{{ $isPresetPeriod
                                            ? route('admin.dashboard', ['period' => $period, 'group' => 'year'])
                                            : route('admin.dashboard', ['from' => $startDate->format('Y-m-d'), 'to' => $endDate->format('Y-m-d'), 'group' => 'year'])
                                        }}"
                                        {{ $currentGroupBy === 'year' ? 'selected' : '' }}>Năm</option>
                                </select>
                            </div>
                        </div>
                        <canvas id="revenueChart" height="140"></canvas>

                        @php
                            $timeLabels = $revenueByDateTable['labels'] ?? [];
                            $timeData = $revenueByDateTable['data'] ?? [];
                            // Tổng doanh thu theo toàn bộ kỳ (chart vẫn hiển thị full data).
                            $timeTotal = array_sum($revenueByDate['data'] ?? []);
                        @endphp
                        <div class="table-responsive mt-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Kỳ</th>
                                        <th class="text-end">Doanh thu</th>
                                        <th class="text-end">% tổng</th>
                                    </tr>
                                </thead>
                                <tbody id="revenueTimeTableBody">
                                    @forelse ($timeLabels as $idx => $label)
                                        @php
                                            $value = isset($timeData[$idx]) ? (float) $timeData[$idx] : 0;
                                            $percent = $timeTotal > 0 ? round(($value / $timeTotal) * 100, 2) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ (($timePage ?? 1) - 1) * ($timePerPage ?? 10) + $idx + 1 }}</td>
                                            <td>{{ $label }}</td>
                                            <td class="text-end fw-bold">{{ number_format($value, 0, ',', '.') }} ₫</td>
                                            <td class="text-end">{{ number_format($percent, 2, ',', '.') }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <nav aria-label="Phân trang doanh thu theo thời gian">
                                <ul class="pagination mb-0" id="revenueTimePagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="chart-container h-100">
                        <h5 class="fw-semibold mb-4">Cơ cấu doanh thu theo danh mục</h5>
                        <canvas id="categoryChart" height="180"></canvas>

                        @php
                            $categoryLabels = $revenueByCategory['labels'] ?? [];
                            $categoryData = $revenueByCategory['data'] ?? [];
                            $categoryTotal = array_sum($categoryData);
                        @endphp
                        <div class="table-responsive mt-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Danh mục</th>
                                        <th class="text-end">Doanh thu</th>
                                        <th class="text-end">% tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categoryLabels as $idx => $label)
                                        @php
                                            $value = isset($categoryData[$idx]) ? (float) $categoryData[$idx] : 0;
                                            $percent = $categoryTotal > 0 ? round(($value / $categoryTotal) * 100, 2) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td class="text-end fw-bold">{{ number_format($value, 0, ',', '.') }} ₫</td>
                                            <td class="text-end">{{ number_format($percent, 2, ',', '.') }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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
                                        <th class="text-center">Ảnh</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Số lượng bán</th>
                                        <th class="text-end">Doanh thu</th>
                                        <th class="text-end">% tổng doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topProducts as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            @php
                                                $productImage = $product->hinh_anh_chinh
                                                    ? '/storage/' . $product->hinh_anh_chinh
                                                    : '/images/no-image.png';
                                            @endphp
                                            <td class="text-center">
                                                <img src="{{ $productImage }}" alt="{{ $product->ten_san_pham }}"
                                                    style="width:70px;height:70px;object-fit:cover;border-radius:12px">
                                            </td>
                                            <td><strong>{{ $product->ten_san_pham }}</strong></td>
                                            <td class="text-end">{{ number_format($product->total_quantity) }}</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($product->total_revenue, 0, ',', '.') }} ₫</td>
                                            <td class="text-end">
                                                {{ number_format($product->percent_total_revenue ?? 0, 2, ',', '.') }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu trong
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

            {{-- Modal các đơn hàng theo trạng thái --}}
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

            {{-- Modal các biến thể sản phẩm có số lượng < 10 --}}
            <div class="modal fade" id="lowStockModal" tabindex="-1">

                <div class="modal-dialog modal-dialog-centered modal-xl" style="width: auto">

                    <div class="modal-content">

                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold">
                                Sản phẩm sắp hết hàng
                            </h5>
                        </div>

                        <div class="modal-body">

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>Ảnh</th>
                                            <th>Sản phẩm</th>
                                            <th>Danh mục</th>
                                            <th>Màu</th>
                                            <th>Kích thước</th>
                                            <th>Giá</th>
                                            <th>Giá KM</th>
                                            <th>Số lượng</th>
                                        </tr>
                                    </thead>

                                    <tbody id="lowStockTableBody"></tbody>

                                </table>

                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                <ul class="pagination" id="lowStockPagination"></ul>
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
            const ordersByStatusFilters = {
                from: @json($startDate->format('Y-m-d')),
                to: @json($endDate->format('Y-m-d')),
            };

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
                    page: page,
                    from: ordersByStatusFilters.from,
                    to: ordersByStatusFilters.to
                }, function(res) {

                    let html = '';

                    const statusMap = {
                        'cho_xac_nhan': 'Chờ xác nhận',
                        'dang_xu_ly': 'Đang xử lý',
                        'dang_giao': 'Đang giao',
                        'da_giao': 'Đã giao',
                        'da_nhan_hang': 'Đã nhận hàng',
                        'da_hoan_thanh': 'Đã hoàn thành',
                        'da_huy': 'Đã hủy',
                        'tra_hang': 'Trả hàng'
                    };

                    if (res.data.length === 0) {

                        html = `<tr>
                    <td colspan="7" class="text-center text-muted py-4">
                    Không có đơn hàng
                    </td>
                    </tr>`;

                    } else {

                        res.data.forEach(order => {

                            let badgeKey = order.yeu_cau_tra ? 'tra_hang' : order.trang_thai;
                            if (!order.yeu_cau_tra && order.trang_thai === 'da_giao' && order.da_nhan_hang_at) {
                                badgeKey = 'da_nhan_hang';
                            }
                            let statusBadge = `
                <span class="badge-status status-${badgeKey}">
                    ${statusMap[badgeKey] ?? statusMap[order.trang_thai]}
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

            let lowStockPage = 1;

            $('.low-stock-card').click(function() {

                $('#lowStockModal').modal('show');

                loadLowStock(1);

            });


            function loadLowStock(page = 1) {

                $('#lowStockTableBody').html(
                    '<tr><td colspan="8" class="text-center py-3">Đang tải...</td></tr>'
                );

                $.get("{{ route('admin.dashboard.low-stock-variants') }}", {
                    page: page
                }, function(res) {

                    let html = '';

                    if (res.data.length === 0) {

                        html = `
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Không có sản phẩm sắp hết hàng
                                </td>
                            </tr>`;

                    } else {

                        res.data.forEach(variant => {

                            let image = variant.product?.hinh_anh_chinh ?
                                `/storage/${variant.product.hinh_anh_chinh}` :
                                '/images/no-image.png';

                            let category = variant.product?.category?.ten_danh_muc ?? '-';

                            let colorName = variant.color?.ten_mau ?? '-';
                            let colorCode = variant.color?.ma_mau ?? '#ccc';

                            let size = variant.size?.ten_kich_thuoc ?? '-';

                            let price = Number(variant.gia).toLocaleString();

                            let salePrice = variant.gia_khuyen_mai ?
                                Number(variant.gia_khuyen_mai).toLocaleString() + ' ₫' :
                                '-';

                            let stockClass = variant.so_luong < 5 ? 'text-danger' : 'text-warning';

                            html += `
                                <tr onclick="window.location='{{ url('admin/variants/edit') }}/${variant.id}'"
                                    style="cursor:pointer">

                                    <td>
                                        <img src="${image}"
                                            style="width:50px;height:50px;object-fit:cover;border-radius:6px">
                                    </td>

                                    <td>
                                        <strong>${variant.product?.ten_san_pham ?? '-'}</strong>
                                    </td>

                                    <td>
                                        ${category}
                                    </td>

                                    <td>
                                        <span style="
                                            display:inline-block;
                                            width:18px;
                                            height:18px;
                                            background:${colorCode};
                                            border-radius:4px;
                                            margin-right:6px;
                                            border:1px solid #ddd;
                                        "></span>
                                        ${colorName}
                                    </td>

                                    <td>
                                        ${size}
                                    </td>

                                    <td class="fw-bold">
                                        ${price} ₫
                                    </td>

                                    <td class="text-success fw-bold">
                                        ${salePrice}
                                    </td>

                                    <td class="fw-bold ${stockClass}">
                                        ${variant.so_luong}
                                    </td>

                                </tr>`;
                        });

                    }

                    $('#lowStockTableBody').html(html);

                    renderLowStockPagination(res);

                });

            }

            function renderLowStockPagination(res) {

                let html = '';

                if (res.last_page > 1) {

                    for (let i = 1; i <= res.last_page; i++) {

                        html += `
                            <li class="page-item ${i === res.current_page ? 'active' : ''}">
                                <a class="page-link" href="#" onclick="loadLowStock(${i}); return false;">
                                ${i}
                                </a>
                            </li>`;
                    }

                }

                $('#lowStockPagination').html(html);

            }

            // -------------------------------
            // Doanh thu theo thời gian (AJAX)
            // -------------------------------
            const revenueTimeTableAjaxUrl = "{{ route('admin.dashboard.revenue-time-table') }}";
            const revenueTimeFilters = {
                group: @json($groupBy),
                from: @json($startDate->format('Y-m-d')),
                to: @json($endDate->format('Y-m-d')),
            };

            let revenueTimePerPage = {{ (int) ($timePerPage ?? 10) }};
            let revenueTimeCurrentPage = {{ (int) ($timePage ?? 1) }};
            let revenueTimeLastPage = {{ (int) ($timeLastPage ?? 1) }};

            function renderRevenueTimeRows(rows) {
                if (!rows || rows.length === 0) {
                    return `<tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>`;
                }

                return rows.map(r => {
                    const value = Math.round(Number(r.value ?? 0)).toLocaleString('vi-VN');
                    const percent = typeof r.percent === 'number'
                        ? r.percent.toFixed(2).replace('.', ',')
                        : '0,00';

                    return `
                        <tr>
                            <td>${r.row_no ?? '-'}</td>
                            <td>${r.label ?? '-'}</td>
                            <td class="text-end fw-bold">${value} ₫</td>
                            <td class="text-end">${percent}%</td>
                        </tr>
                    `;
                }).join('');
            }

            function renderRevenueTimePagination(current, last) {
                const $container = $('#revenueTimePagination');
                if (last <= 1) {
                    $container.html('');
                    return;
                }

                const windowSize = 2;
                const startPage = Math.max(1, current - windowSize);
                const endPage = Math.min(last, current + windowSize);

                let html = '';

                // Prev
                html += `<li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#"
                        ${current <= 1 ? '' : 'onclick="loadRevenueTimeTable(' + (current - 1) + '); return false;"'}>
                        Prev
                    </a>
                </li>`;

                if (startPage > 1) {
                    html += `<li class="page-item ${1 === current ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadRevenueTimeTable(1); return false;">1</a>
                    </li>`;

                    if (startPage > 2) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === current ? 'active' : ''}">
                        <a class="page-link" href="#"
                            onclick="${i === current ? '' : 'loadRevenueTimeTable(' + i + '); return false;'}">
                            ${i}
                        </a>
                    </li>`;
                }

                if (endPage < last) {
                    if (endPage < last - 1) {
                        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }

                    html += `<li class="page-item ${last === current ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadRevenueTimeTable(${last}); return false;">${last}</a>
                    </li>`;
                }

                // Next
                html += `<li class="page-item ${current >= last ? 'disabled' : ''}">
                    <a class="page-link" href="#"
                        ${current >= last ? '' : 'onclick="loadRevenueTimeTable(' + (current + 1) + '); return false;"'}>
                        Next
                    </a>
                </li>`;

                $container.html(html);
            }

            function loadRevenueTimeTable(page = 1) {
                if (page < 1 || page > revenueTimeLastPage) return;

                $('#revenueTimeTableBody').html(
                    '<tr><td colspan="4" class="text-center py-3">Đang tải...</td></tr>'
                );

                $.get(revenueTimeTableAjaxUrl, {
                    ...revenueTimeFilters,
                    time_page: page,
                    time_per_page: revenueTimePerPage,
                }, function(res) {
                    const rows = res.rows || [];
                    const meta = res.meta || {};

                    revenueTimeCurrentPage = meta.current_page || page;
                    revenueTimeLastPage = meta.last_page || revenueTimeLastPage;

                    $('#revenueTimeTableBody').html(renderRevenueTimeRows(rows));
                    renderRevenueTimePagination(revenueTimeCurrentPage, revenueTimeLastPage);
                }).fail(function() {
                    $('#revenueTimeTableBody').html(
                        '<tr><td colspan="4" class="text-center text-danger py-3">Lỗi tải dữ liệu</td></tr>'
                    );
                });
            }

            // Render pagination ban đầu (không reload)
            renderRevenueTimePagination(revenueTimeCurrentPage, revenueTimeLastPage);
        </script>

    </body>
@endsection
