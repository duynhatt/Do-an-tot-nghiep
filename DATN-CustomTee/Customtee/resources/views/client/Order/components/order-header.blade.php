<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-transparent p-0 m-0">
        <li class="breadcrumb-item">
            <a href="{{ route('order') ?? route('client.order.list') }}" class="text-primary text-decoration-none fw-medium">
                <i class="bi bi-arrow-left-short me-1"></i> Đơn hàng của tôi
            </a>
        </li>
        <li class="breadcrumb-item active fw-medium" aria-current="page">
            Chi tiết #{{ $donHang->ma_don_hang }}
        </li>
    </ol>
</nav>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    @php
        $canShowReturnCountdown =
            $donHang->trang_thai === \App\Models\DonHang::TRANG_THAI_DA_GIAO
            && !$donHang->yeu_cau_tra
            && !empty($donHang->da_nhan_hang_at);
        $returnDeadlineTs = $canShowReturnCountdown
            ? $donHang->da_nhan_hang_at->copy()->addDays(3)->getTimestampMs()
            : null;
    @endphp
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Chi tiết đơn hàng #{{ $donHang->ma_don_hang }}</h1>
        <small class="text-muted">Đặt ngày {{ $donHang->created_at->format('d/m/Y - H:i') }}</small>
        @if ($canShowReturnCountdown)
            <div class="mt-1">
                <small class="text-danger fw-semibold" id="returnCountdown" data-deadline-ts="{{ $returnDeadlineTs }}">
                    Bạn còn -- ngày -- giờ -- phút để trả hàng/hoàn tiền
                </small>
            </div>
        @endif
    </div>
    <a href="{{ route('order') ?? route('client.order.list') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-arrow-left me-2"></i> Quay lại
    </a>
</div>

@if ($canShowReturnCountdown)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownEl = document.getElementById('returnCountdown');
            if (!countdownEl) return;

            const deadlineTs = Number(countdownEl.dataset.deadlineTs || 0);
            if (!deadlineTs) return;

            const formatLeft = (msLeft) => {
                const totalMinutes = Math.max(0, Math.floor(msLeft / 60000));
                const days = Math.floor(totalMinutes / (24 * 60));
                const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
                const minutes = totalMinutes % 60;
                return `${days} ngày ${String(hours).padStart(2, '0')} giờ ${String(minutes).padStart(2, '0')} phút`;
            };

            const tick = () => {
                const now = Date.now();
                const msLeft = deadlineTs - now;
                if (msLeft <= 0) {
                    countdownEl.textContent = 'Đã hết thời gian trả hàng/hoàn tiền (3 ngày).';
                    countdownEl.classList.remove('text-danger');
                    countdownEl.classList.add('text-muted');
                    return false;
                }

                countdownEl.textContent = `Bạn còn ${formatLeft(msLeft)} để trả hàng/hoàn tiền`;
                return true;
            };

            if (!tick()) return;
            const timer = setInterval(() => {
                if (!tick()) clearInterval(timer);
            }, 30000);
        });
    </script>
@endif