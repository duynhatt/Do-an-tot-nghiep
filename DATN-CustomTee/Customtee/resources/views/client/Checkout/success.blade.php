@include('client.layout.header')

<div class="container py-5 text-center">
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 600px;">
        <div class="card-body p-5">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h2 class="mt-4 fw-bold text-success">Đặt hàng thành công!</h2>
            <p class="lead mt-3">Cảm ơn bạn đã đặt hàng tại Customtee.</p>
            
            <div class="mt-4">
                <h5>Mã đơn hàng: <strong>{{ $donHang->ma_don_hang }}</strong></h5>
                <p class="text-muted">Tổng tiền: <strong class="text-primary">{{ number_format($donHang->tong_tien) }} ₫</strong></p>
                <p class="text-muted">Phương thức: 
                    {{ $donHang->phuong_thuc_thanh_toan === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Thanh toán online' }}
                </p>
                <p class="text-muted">Trạng thái: <span class="badge bg-warning">Chờ xác nhận</span></p>
            </div>

            <p class="mt-4">Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng.</p>

            <a href="{{ route('home') }}" class="btn btn-primary btn-lg mt-3">Quay về trang chủ</a>
            <a href="{{ route('order') }}" class="btn btn-outline-primary mt-3">Xem đơn hàng của tôi</a>
        </div>
    </div>
</div>

@include('client.layout.footer')