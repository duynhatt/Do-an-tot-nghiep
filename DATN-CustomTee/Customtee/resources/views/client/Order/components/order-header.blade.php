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
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Chi tiết đơn hàng #{{ $donHang->ma_don_hang }}</h1>
        <small class="text-muted">Đặt ngày {{ $donHang->created_at->format('d/m/Y - H:i') }}</small>
    </div>
    <a href="{{ route('order') ?? route('client.order.list') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-arrow-left me-2"></i> Quay lại
    </a>
</div>