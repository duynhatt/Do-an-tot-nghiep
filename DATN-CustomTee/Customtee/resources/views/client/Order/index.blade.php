@include('client.layout.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<div class="container py-5 my-3 my-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mb-md-5 gap-3">
                <div>
                    <h1 class="display-5 fw-bold text-dark mb-1">Đơn hàng của tôi</h1>
                    <p class="lead text-muted fs-6 mb-0">Quản lý và theo dõi tất cả đơn hàng bạn đã đặt</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg px-4 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left-circle me-2"></i> Tiếp tục mua sắm
                </a>
            </div>

            @php
                $statusTabs = [
                    'cho_xac_nhan' => ['Chờ xác nhận', 'warning'],
                    'dang_xu_ly' => ['Đang xử lý', 'info'],
                    'dang_giao' => ['Đang giao', 'primary'],
                    'da_giao' => ['Đã giao', 'success'],
                    'da_huy' => ['Đã hủy', 'danger'],
                    'da_hoan_thanh' => ['Đã nhận hàng', 'success'],
                    'tra_hang' => ['Trả hàng', 'secondary'],
                ];
                $currentStatus = $currentStatus ?? request('trang_thai');
            @endphp

            @if ($donHangs->isEmpty() && !$currentStatus)
                <div class="card border-0 shadow-lg rounded-4 text-center py-5 px-4 bg-gradient-light">
                    <div class="card-body">
                        <i class="bi bi-bag-x-fill display-1 text-primary opacity-75 mb-4"></i>
                        <h4 class="fw-bold mb-3 text-dark">Bạn chưa có đơn hàng nào</h4>
                        <p class="text-muted fs-5 mb-4">Hãy khám phá ngay hàng ngàn sản phẩm chất lượng với nhiều ưu đãi
                            hấp dẫn!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow">
                            Bắt đầu mua sắm ngay
                        </a>
                    </div>
                </div>
            @else
                {{-- Thanh tab lọc (server-side) --}}
                <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto mb-4 shadow-sm rounded-pill bg-white p-2">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link rounded-pill px-4 py-2 {{ !$currentStatus ? 'active' : '' }}"
                            href="{{ route('order') }}">
                            Tất cả
                        </a>
                    </li>

                    @foreach ($statusTabs as $key => $value)
                        <li class="nav-item" role="presentation"><a
                                class="nav-link rounded-pill px-4 py-2 {{ $currentStatus === $key ? 'active' : '' }}"
                                href="{{ route('order', ['trang_thai' => $key]) }}">
                                {{ $value[0] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Danh sách đơn theo filter hiện tại --}}
                <div class="row g-4">
                    @if ($donHangs->isEmpty())
                        <div class="col-12">
                            <div class="alert alert-light border text-center py-5 rounded-4 shadow-sm">
                                <i class="bi bi-info-circle fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="fw-semibold">Chưa có đơn hàng nào phù hợp với bộ lọc này</h5>
                            </div>
                        </div>
                    @else
                        @foreach ($donHangs as $donHang)
                            <div class="col-12">
                                <div class="card border-0 shadow hover-lift rounded-4 overflow-hidden transition-all">
                                    <div
                                        class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 fw-bold text-dark">
                                                Mã đơn: <span class="text-primary">{{ $donHang->ma_don_hang }}</span>
                                            </h5>
                                            <small class="text-muted">
                                                Đặt lúc {{ $donHang->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-2">
                                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                                @if ($donHang->trang_thai === 'da_giao' && !$donHang->yeu_cau_tra)
                                                    <form action="{{ route('order.confirm', $donHang->id) }}"
                                                        method="post"
                                                        class="js-client-confirm-submit"
                                                        data-confirm-message="Bạn xác nhận đã nhận đủ hàng và đồng ý hoàn tất đơn này?">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-success btn-sm px-3 rounded-pill">
                                                            <i class="bi bi-check2-circle me-1"></i>
                                                            Xác nhận nhận hàng
                                                        </button>
                                                    </form>
                                                @endif
                                                @if (
                                                    $donHang->phuong_thuc_thanh_toan === 'vnpay' &&
                                                        in_array($donHang->trang_thai_thanh_toan, ['chua_thanh_toan', 'that_bai'], true) &&
                                                        $donHang->trang_thai !== 'da_huy')
                                                    <a href="{{ route('order.repay', $donHang->id) }}"
                                                        class="btn btn-danger btn-sm px-3 rounded-pill">
                                                        <i class="bi bi-credit-card me-1"></i>
                                                        Thanh toán lại
                                                    </a>
                                                @endif
                                                <a href="{{ route('order.show', $donHang->id) }}"
                                                    class="btn btn-outline-primary btn-sm px-4 rounded-pill">
                                                    Chi tiết <i class="bi bi-arrow-right ms-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <div class="row g-4 align-items-center mb-4">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                                        style="width: 64px; height: 64px;">
                                                        <i class="bi bi-bag-fill text-primary fs-3"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1">
                                                            {{ $donHang->chiTietDonHangs->count() }} sản
                                                            phẩm</h6>
                                                        <small class="text-muted">Tổng cộng</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 text-md-center">
                                                <h5 class="fw-bold text-dark mb-1">
                                                    {{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</h5>
                                                <small class="text-muted">Tổng thanh toán</small>
                                            </div>

                                            <div class="col-md-4 d-flex justify-content-md-end">
                                                @php
                                                    $statusMap = [
                                                        'cho_xac_nhan' => [
                                                            'Chờ xác nhận',
                                                            'warning',
                                                            'bi bi-hourglass-split',
                                                        ],
                                                        'dang_xu_ly' => ['Đang xử lý', 'info', 'bi bi-gear'],
                                                        // Dữ liệu cũ có thể đang nằm ở 'cho_duyet_huy' nhưng hiển thị chung như 'đang xử lý'.
                                                        'cho_duyet_huy' => ['Đang xử lý', 'info', 'bi bi-gear'],
                                                        'dang_giao' => ['Đang giao', 'primary', 'bi bi-truck'],
                                                        'da_giao' => ['Đã giao', 'success', 'bi bi-check2-circle'],
                                                        'da_hoan_thanh' => [
                                                            'Đã hoàn thành',
                                                            'success',
                                                            'bi bi-check2-all',
                                                        ],
                                                        'da_huy' => ['Đã hủy', 'danger', 'bi bi-x-circle'],
                                                    ];
                                                    $st = $statusMap[$donHang->trang_thai] ?? [
                                                        'Không xác định',
                                                        'secondary',
                                                        'bi bi-question-circle',
                                                    ];
                                                    $latestRefund = $donHang->refunds->first();
                                                @endphp

                                                @if ($donHang->trang_thai !== 'da_hoan_thanh' && $latestRefund && $latestRefund->trang_thai === 'da_tu_choi')
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger fs-6 px-4 py-2 d-flex align-items-center rounded-pill">
                                                        <i class="bi bi-x-octagon me-2 fs-5"></i>
                                                        Đã từ chối hoàn tiền
                                                    </span>
                                                @elseif ($donHang->yeu_cau_tra)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border border-secondary fs-6 px-4 py-2 d-flex align-items-center rounded-pill">
                                                        <i class="bi bi-arrow-counterclockwise me-2 fs-5"></i>
                                                        Trả hàng/hoàn tiền
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-{{ $st[1] }}-subtle text-{{ $st[1] }} border border-{{ $st[1] }} fs-6 px-4 py-2 d-flex align-items-center rounded-pill">
                                                        <i class="{{ $st[2] }} me-2 fs-5"></i>
                                                        {{ $st[0] }}
                                                    </span>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <h6 class="fw-semibold mb-3">Sản phẩm trong đơn</h6>
                                            <div class="row g-3">
                                                @foreach ($donHang->chiTietDonHangs as $ct)
                                                    <div class="col-12">
                                                        <div
                                                            class="d-flex align-items-start gap-3 bg-light rounded-3 p-3 hover-bg-white transition-all border">
                                                            @if ($ct->sanPham->hinh_anh_chinh ?? false)
                                                                <img src="{{ asset('storage/' . $ct->sanPham->hinh_anh_chinh) }}"
                                                                    alt="{{ $ct->sanPham->ten_san_pham }}"
                                                                    class="rounded object-fit-cover flex-shrink-0"
                                                                    style="width: 80px; height: 80px; border: 1px solid #e9ecef;">
                                                            @else
                                                                <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                                    style="width: 80px; height: 80px;">
                                                                    <i class="bi bi-image text-secondary fs-4"></i>
                                                                </div>
                                                            @endif

                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-semibold mb-1 text-truncate"
                                                                    style="max-width: 300px;">
                                                                    {{ $ct->sanPham->ten_san_pham ?? 'Sản phẩm' }}
                                                                </h6>

                                                                <div class="d-flex flex-wrap gap-3 mb-2 small">
                                                                    @if ($ct->bienThe && $ct->bienThe->color)
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <div class="rounded-circle border shadow-sm"
                                                                                style="width: 18px; height: 18px; background-color: {{ $ct->bienThe->color->ma_mau ?? '#ccc' }}; border: 1px solid #dee2e6;">
                                                                            </div>
                                                                            <span>{{ $ct->bienThe->color->ten_mau ?? 'Không có màu' }}</span>
                                                                        </div>
                                                                    @endif

                                                                    @if ($ct->bienThe && $ct->bienThe->size)
                                                                        <div>
                                                                            <span
                                                                                class="badge bg-secondary-subtle text-secondary border">
                                                                                Size:
                                                                                {{ $ct->bienThe->size->ten_kich_thuoc ?? '—' }}
                                                                            </span>
                                                                        </div>
                                                                    @endif

                                                                    <div>
                                                                        <span
                                                                            class="badge bg-primary-subtle text-primary border">x{{ $ct->so_luong }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="fw-medium text-dark">
                                                                    {{ number_format($ct->thanh_tien, 0, ',', '.') }}
                                                                    ₫
                                                                    <small class="text-muted ms-2">
                                                                        ({{ number_format($ct->don_gia, 0, ',', '.') }}
                                                                        ₫ × {{ $ct->so_luong }})
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-light border-0 px-4 py-3">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                            <small class="text-muted">
                                                <i class="bi bi-credit-card-2-front me-1"></i>
                                                {{ $donHang->phuong_thuc_thanh_toan === 'cod' ? 'Thanh toán khi nhận hàng' : ucfirst(str_replace('_', ' ', $donHang->phuong_thuc_thanh_toan)) }}
                                            </small>
                                            @if ($donHang->voucher_id)
                                                <small
                                                    class="badge bg-success-subtle text-success border border-success rounded-pill px-3">
                                                    <i class="bi bi-ticket-perforated me-1"></i>Đã áp dụng
                                                    voucher
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $donHangs->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    .transition-all {
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .bg-gradient-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .nav-pills .nav-link {
        color: #6c757d;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        color: white;
        background-color: #0d6efd;
        box-shadow: 0 4px 10px rgba(13, 110, 253, .25);
    }

    .hover-bg-white:hover {
        background-color: white !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
    }
</style>

<div id="clientOrderConfirmModal"
    class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center d-none"
    style="z-index: 10500; background-color: rgba(0, 0, 0, 0.5);"
    role="dialog"
    aria-modal="true"
    aria-labelledby="clientOrderConfirmTitle">
    <div class="card shadow-lg border-0 m-3 rounded-4" style="max-width: 420px; width: 100%;">
        <div class="card-header bg-white border-0 py-3 fw-semibold rounded-top-4" id="clientOrderConfirmTitle">
            Xác nhận nhận hàng
        </div>
        <div class="card-body text-secondary" id="clientOrderConfirmBody"></div>
        <div class="card-footer bg-white border-0 d-flex gap-2 justify-content-end py-3 rounded-bottom-4">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                id="clientOrderConfirmCancel">Hủy</button>
            <button type="button" class="btn btn-success rounded-pill px-4" id="clientOrderConfirmOk">Xác nhận</button>
        </div>
    </div>
</div>

@include('client.layout.footer')
@include('client.layout.scripts')

<script>
    (function() {
        function openClientOrderConfirm(message, onConfirm) {
            const modal = document.getElementById('clientOrderConfirmModal');
            const body = document.getElementById('clientOrderConfirmBody');
            const okBtn = document.getElementById('clientOrderConfirmOk');
            const cancelBtn = document.getElementById('clientOrderConfirmCancel');
            if (!modal || !body || !okBtn || !cancelBtn) {
                if (window.confirm(message)) {
                    onConfirm();
                }
                return;
            }

            body.textContent = message;
            modal.classList.remove('d-none');
            modal.classList.add('d-flex');

            function close() {
                modal.classList.add('d-none');
                modal.classList.remove('d-flex');
                okBtn.removeEventListener('click', handleOk);
                cancelBtn.removeEventListener('click', close);
            }

            function handleOk() {
                close();
                onConfirm();
            }

            okBtn.addEventListener('click', handleOk);
            cancelBtn.addEventListener('click', close);
        }

        function bindOrderConfirmForms() {
            document.querySelectorAll('form.js-client-confirm-submit').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (form.getAttribute('data-confirmed') === '1') {
                        form.removeAttribute('data-confirmed');
                        return;
                    }
                    e.preventDefault();
                    const msg = form.getAttribute('data-confirm-message') ||
                        'Bạn có chắc chắn muốn thực hiện thao tác này?';
                    openClientOrderConfirm(msg, function() {
                        form.setAttribute('data-confirmed', '1');
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    });
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bindOrderConfirmForms);
        } else {
            bindOrderConfirmForms();
        }
    })();
</script>
