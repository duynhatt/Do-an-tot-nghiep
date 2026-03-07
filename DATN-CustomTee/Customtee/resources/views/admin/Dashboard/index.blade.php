@extends('admin.layout.AdminLayout')

@section('AdminContent')

    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

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

            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-md-6">
                    <div class="card h-100">
                        <div class="stat-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-white-75 fw-medium">Doanh thu</h6>
                                    <h3 class="mb-0 fw-bold">₫ {{ $stats['revenue'] }}</h3>
                                </div>
                                <i class="fas fa-coins fa-2x text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100">
                        <div class="stat-header"
                            style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-white-75 fw-medium">Đơn hàng</h6>
                                    <h3 class="mb-0 fw-bold">{{ $stats['orders_count'] }}</h3>
                                </div>
                                <i class="fas fa-shopping-bag fa-2x text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100">
                        <div class="stat-header" style="background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-white-75 fw-medium">Khách mới</h6>
                                    <h3 class="mb-0 fw-bold">{{ $stats['new_customers'] }}</h3>
                                </div>
                                <i class="fas fa-user-plus fa-2x text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100">
                        <div class="stat-header"
                            style="background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-white-75 fw-medium">Tỷ lệ chuyển đổi</h6>
                                    <h3 class="mb-0 fw-bold">{{ $stats['conversion_rate'] }}</h3>
                                </div>
                                <i class="fas fa-percentage fa-2x text-white opacity-75"></i>
                            </div>
                        </div>
                    </div>
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
        </main>

        <script>
            function toggleTheme() {
                const body = document.body;
                const current = body.getAttribute('data-theme');
                body.setAttribute('data-theme', current === 'dark' ? '' : 'dark');
            }

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
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
@endsection
