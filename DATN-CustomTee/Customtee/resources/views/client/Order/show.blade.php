@include('client.layout.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('order') ?? route('client.order.list') }}"
                            class="text-primary text-decoration-none fw-medium">
                            <i class="bi bi-arrow-left-short me-1"></i> Đơn hàng của tôi
                        </a>
                    </li>
                    <li class="breadcrumb-item active fw-medium" aria-current="page">
                        Chi tiết #{{ $donHang->ma_don_hang }}
                    </li>
                </ol>
            </nav>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">Chi tiết đơn hàng #{{ $donHang->ma_don_hang }}</h1>
                    <small class="text-muted">Đặt ngày {{ $donHang->created_at->format('d/m/Y - H:i') }}</small>
                </div>
                <a href="{{ route('order') ?? route('client.order.list') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div
                            class="card-header bg-gradient text-white py-3 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">Trạng thái đơn hàng</h5>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $statusMap = [
                                    'cho_xac_nhan' => [
                                        'Chờ xác nhận',
                                        'warning',
                                        'bi bi-hourglass-split',
                                        'Đang chờ xác nhận từ cửa hàng',
                                    ],
                                    'dang_xu_ly' => ['Đang xử lý', 'info', 'bi bi-gear', 'Đang chuẩn bị và đóng gói'],
                                    'dang_giao' => [
                                        'Đang giao',
                                        'primary',
                                        'bi bi-truck',
                                        'Đơn hàng đang được vận chuyển',
                                    ],
                                    'da_giao' => [
                                        'Đã giao',
                                        'success',
                                        'bi bi-check-circle-fill',
                                        'Giao hàng thành công',
                                    ],
                                    'da_huy' => ['Đã hủy', 'danger', 'bi bi-x-circle-fill', 'Đơn hàng đã bị hủy'],
                                ];
                                $current = $statusMap[$donHang->trang_thai] ?? [
                                    'Khác',
                                    'secondary',
                                    'bi bi-question-circle',
                                    'Trạng thái không xác định',
                                ];
                            @endphp

                            <div class="d-flex align-items-center mb-4">
                                <span class="badge bg-{{ $current[1] }} fs-5 px-4 py-2 d-flex align-items-center">
                                    <i class="{{ $current[2] }} me-2 fs-4"></i> {{ $current[0] }}
                                </span>
                            </div>

                            <p class="text-muted mb-4">{{ $current[3] }}</p>

                            <div class="d-flex justify-content-between position-relative mt-4 timeline-compact">
                                <div
                                    class="timeline-step {{ in_array($donHang->trang_thai, ['cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'da_giao']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="bi bi-check-circle"></i></div>
                                    <small>Xác nhận</small>
                                </div>
                                <div
                                    class="timeline-step {{ in_array($donHang->trang_thai, ['dang_xu_ly', 'dang_giao', 'da_giao']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="bi bi-gear"></i></div>
                                    <small>Xử lý</small>
                                </div>
                                <div
                                    class="timeline-step {{ in_array($donHang->trang_thai, ['dang_giao', 'da_giao']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="bi bi-truck"></i></div>
                                    <small>Giao hàng</small>
                                </div>
                                <div class="timeline-step {{ $donHang->trang_thai === 'da_giao' ? 'active' : '' }}">
                                    <div class="step-icon"><i class="bi bi-check2-all"></i></div>
                                    <small>Hoàn tất</small>
                                </div>
                            </div>

                            @if ($donHang->trang_thai === 'da_huy')
                                <div class="alert alert-danger mt-4 d-flex align-items-center small" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Đơn hàng đã bị hủy. Vui lòng liên hệ hỗ trợ nếu cần.
                                </div>
                            @endif

                            @if ($donHang->trang_thai === 'cho_xac_nhan')
                                <div class="mt-4 d-flex flex-wrap gap-2">
                                    <form action="{{ route('order.cancel', $donHang->id) }}" method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($donHang->trang_thai === 'da_giao')
                                <div class="mt-4">
                                    @if ($donHang->yeu_cau_tra)
                                        <div class="alert alert-info small mb-2" role="alert">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                                            Bạn đã gửi yêu cầu trả hàng{{ $donHang->ngay_yeu_cau_tra ? ' vào ' . $donHang->ngay_yeu_cau_tra->format('d/m/Y H:i') : '' }}.
                                        </div>
                                        @if ($donHang->ly_do_tra)
                                            <p class="small mb-0"><strong>Lý do đã gửi:</strong> {{ $donHang->ly_do_tra }}</p>
                                        @endif
                                    @else
                                        <form action="{{ route('order.return', $donHang->id) }}" method="post">
                                            @csrf
                                            <div class="mb-2">
                                                <label for="ly_do_tra" class="form-label small fw-semibold">Lý do trả hàng (tuỳ chọn)</label>
                                                <textarea name="ly_do_tra" id="ly_do_tra" class="form-control form-control-sm" rows="2" maxlength="1000" placeholder="Nhập lý do (sai size, lỗi sản phẩm, ... nếu có)"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Gửi yêu cầu trả hàng cho đơn này?');">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Yêu cầu trả hàng
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-light py-3 px-4">
                            <h5 class="mb-0 fw-semibold">Sản phẩm ({{ $donHang->chiTietDonHangs->count() }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Biến thể</th>
                                            <th class="text-center">SL</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($donHang->chiTietDonHangs as $chiTiet)
                                            <tr>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $chiTiet->sanPham->hinh_anh_chinh ? asset('storage/' . $chiTiet->sanPham->hinh_anh_chinh) : 'https://via.placeholder.com/60' }}"
                                                            alt="" class="rounded me-3" width="60"
                                                            height="60" style="object-fit: cover;">
                                                        <div>
                                                            <div class="fw-medium">
                                                                {{ $chiTiet->sanPham->ten_san_pham }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    @if ($chiTiet->bienThe)
                                                        <span class="badge bg-light border text-dark px-2 py-1">
                                                            {{ $chiTiet->bienThe->color->ten_mau ?? '—' }} /
                                                            {{ $chiTiet->bienThe->size->ten_kich_thuoc ?? '—' }}
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-center py-3">{{ $chiTiet->so_luong }}</td>
                                                <td class="text-end py-3">
                                                    {{ number_format($chiTiet->don_gia, 0, ',', '.') }} ₫</td>
                                                <td class="text-end py-3 fw-medium text-primary">
                                                    {{ number_format($chiTiet->thanh_tien, 0, ',', '.') }} ₫</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                        <div class="card-header bg-gradient text-white py-3 px-4">
                            <h5 class="mb-0 fw-semibold" style="color: black">Tóm tắt thanh toán</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Tạm tính</span>
                                <span>{{ number_format($donHang->tam_tinh, 0, ',', '.') }} ₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Giảm giá</span>
                                <span class="text-danger">-{{ number_format($donHang->tien_giam ?? 0, 0, ',', '.') }}
                                    ₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Phí vận chuyển</span>
                                <span>{{ number_format($donHang->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                                <span>Tổng cộng</span>
                                <span class="text-primary">{{ number_format($donHang->tong_tien, 0, ',', '.') }}
                                    ₫</span>
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <h6 class="fw-semibold small mb-2">Phương thức thanh toán</h6>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card-2-front text-primary me-2"></i>
                                    @switch($donHang->phuong_thuc_thanh_toan)
                                        @case('cod')
                                            COD (Thanh toán khi nhận hàng)
                                        @break

                                        @case('zalo_pay')
                                            ZaloPay
                                        @break

                                        @case('momo')
                                            MoMo
                                        @break

                                        @case('vnpay')
                                            VNPay
                                        @break

                                        @default
                                            Thanh toán trực tuyến
                                    @endswitch
                                </div>
                                <small class="d-block mt-1">
                                    @if ($donHang->trang_thai_thanh_toan === 'da_thanh_toan')
                                        <span class="text-success">Đã thanh toán</span>
                                    @elseif($donHang->trang_thai_thanh_toan === 'that_bai')
                                        <span class="text-danger">Thất bại</span>
                                    @else
                                        <span class="text-warning">Chưa thanh toán</span>
                                    @endif
                                </small>
                            </div>

                            <div>
                                <h6 class="fw-semibold small mb-2">Giao hàng đến</h6>
                                <p class="mb-1 fw-medium">{{ $donHang->ten_nguoi_nhan }}</p>
                                <p class="mb-1 small">{{ $donHang->so_dien_thoai_nhan_hang }}</p>
                                <p class="mb-0 small text-muted">{{ $donHang->dia_chi_chi_tiet }}</p>

                                @if ($donHang->ghi_chu)
                                    <div class="mt-3">
                                        <small class="fw-medium">Ghi chú:</small>
                                        <p class="small text-muted mt-1 mb-0">{{ $donHang->ghi_chu }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    }

    .timeline-compact {
        padding: 0 15px;
    }

    .timeline-step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step-icon {
        width: 48px;
        height: 48px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
        font-size: 1.4rem;
        color: #adb5bd;
        transition: all 0.3s;
    }

    .timeline-step.active .step-icon {
        background: #0d6efd;
        color: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, .25);
    }

    .timeline-step small {
        font-size: 0.8rem;
        font-weight: 500;
        color: #495057;
    }

    .timeline-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 24px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #dee2e6;
        z-index: -1;
    }

    .card {
        border-radius: 12px;
    }

    table th,
    table td {
        vertical-align: middle;
    }

    .sticky-top {
        top: 20px;
        z-index: 100;
    }
</style>

@include('client.layout.footer')
@include('client.layout.scripts')
