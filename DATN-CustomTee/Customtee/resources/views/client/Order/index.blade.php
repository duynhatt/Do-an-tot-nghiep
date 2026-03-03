@include('client.layout.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
                <div>
                    <h1 class="display-6 fw-bold text-dark mb-1">Đơn hàng của tôi</h1>
                    <p class="lead text-muted fs-6 mb-0">Theo dõi trạng thái và chi tiết các đơn hàng bạn đã đặt</p>
                </div>
                <a href="{{ route('home') ?? '/' }}" class="btn btn-outline-primary btn-lg px-4">
                    <i class="bi bi-arrow-left-circle me-2"></i> Tiếp tục mua sắm
                </a>
            </div>

            @if ($donHangs->isEmpty())
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center py-5 px-4">
                    <div class="card-body">
                        <div class="mb-4">
                            <i class="bi bi-bag-x-fill display-1 text-primary opacity-50"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Chưa có đơn hàng nào</h4>
                        <p class="text-muted mb-4 fs-5">Khám phá ngay hàng ngàn sản phẩm chất lượng với ưu đãi hấp dẫn!
                        </p>
                        <a href="{{ route('home') ?? '/' }}" class="btn btn-primary btn-lg px-5 py-3">
                            Bắt đầu mua sắm
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($donHangs as $donHang)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow-lg transition-all">
                                <div
                                    class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                                    <div>
                                        <h5 class="mb-0 fw-semibold text-dark">
                                            Mã đơn: <span class="text-primary">{{ $donHang->ma_don_hang }}</span>
                                        </h5>
                                        <small class="text-muted">
                                            Đặt ngày {{ $donHang->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('order.show', $donHang->id) }}"
                                        class="btn btn-outline-primary btn-sm px-4">
                                        Chi tiết <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>

                                <div class="card-body p-4">
                                    <div class="row align-items-center g-4">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="bi bi-bag-fill text-primary fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">
                                                        {{ $donHang->chiTietDonHangs->count() }} sản phẩm</h6>
                                                    <small class="text-muted">Trong đơn hàng</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-lg-4 text-md-center">
                                            <h5 class="fw-bold text-dark mb-1">
                                                {{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫
                                            </h5>
                                            <small class="text-muted">Tổng thanh toán</small>
                                        </div>

                                        <div class="col-md-4 col-lg-4 d-flex justify-content-md-end">
                                            <div class="d-flex flex-column align-items-md-end">
                                                <div class="mb-2">
                                                    @php
                                                        $statusMap = [
                                                            'cho_xac_nhan' => [
                                                                'Chờ xác nhận',
                                                                'warning',
                                                                'bi bi-hourglass-split',
                                                            ],
                                                            'da_xac_nhan' => [
                                                                'Đã xác nhận',
                                                                'info',
                                                                'bi bi-check-circle',
                                                            ],
                                                            'dang_giao' => ['Đang giao', 'primary', 'bi bi-truck'],
                                                            'da_giao' => ['Đã giao', 'success', 'bi bi-check2-circle'],
                                                            'da_huy' => ['Đã hủy', 'danger', 'bi bi-x-circle'],
                                                        ];
                                                        $current = $statusMap[$donHang->trang_thai] ?? [
                                                            'Khác',
                                                            'secondary',
                                                            'bi bi-question-circle',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $current[1] }} fs-6 px-4 py-2 d-flex align-items-center">
                                                        <i class="{{ $current[2] }} me-2"></i>{{ $current[0] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-light border-0 py-3 px-4">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                        <small class="text-muted">
                                            <i class="bi bi-credit-card-2-front me-1"></i>
                                            {{ $donHang->phuong_thuc_thanh_toan ?? 'Thanh toán khi nhận hàng' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $donHangs->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .hover-shadow-lg:hover {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        transform: translateY(-4px);
        transition: all .3s ease;
    }

    .transition-all {
        transition: all 0.3s ease;
    }
</style>

@include('client.layout.footer')
@include('client.layout.scripts')
