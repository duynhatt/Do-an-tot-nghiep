<div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
    <div class="card-header bg-gradient py-3 px-4">
        <h5 class="mb-0 fw-semibold">Thông tin đơn hàng</h5>
    </div>
    <div class="card-body p-4">
        <div class="mb-3">
            <h6 class="fw-semibold small text-uppercase text-muted mb-2">Tổng quan</h6>
            <ul class="list-unstyled small mb-0">
                <li class="d-flex justify-content-between mb-1"><span class="text-muted">Mã đơn</span><span
                        class="fw-medium text-dark">{{ $donHang->ma_don_hang }}</span></li>
                <li class="d-flex justify-content-between mb-1"><span class="text-muted">Ngày
                        đặt</span><span>{{ $donHang->created_at->format('d/m/Y H:i') }}</span></li>
                @if ($donHang->yeu_cau_tra = 0)
                    <li class="d-flex justify-content-between mb-1"><span class="text-muted">Trạng thái</span><span
                            class="badge bg-primary-subtle text-primary border">{{ \App\Models\DonHang::tenTrangThai($donHang->trang_thai) }}</span>
                    </li>
                @endif
            </ul>
        </div>

        <hr class="my-3">

        <div class="mb-3">
            <h6 class="fw-semibold small text-uppercase text-muted mb-2">Thanh toán</h6>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Tạm tính</span><span
                    class="small">{{ number_format($donHang->tam_tinh, 0, ',', '.') }} ₫</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted small">Giảm giá</span><span
                    class="small text-danger">-{{ number_format($donHang->tien_giam ?? 0, 0, ',', '.') }} ₫</span></div>
            <div class="d-flex justify-content-between mb-3"><span class="text-muted small">Phí vận chuyển</span><span
                    class="small">{{ number_format($donHang->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</span></div>
            <div class="p-3 rounded-3 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small text-uppercase">Tổng cộng</span>
                <span class="fw-bold text-primary">{{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        <hr class="my-3">

        <div class="mb-3">
            <h6 class="fw-semibold small text-uppercase text-muted mb-2">Hình thức thanh toán</h6>
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-credit-card-2-front text-primary me-2"></i>
                <span class="small">
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
                </span>
            </div>
            @if ($donHang->yeu_cau_tra = 0)

                <small class="d-block mt-1">
                    @if ($donHang->trang_thai_thanh_toan === 'da_thanh_toan' || $donHang->trang_thai === 'da_hoan_thanh')
                        <span class="badge bg-success-subtle text-success border border-success">Đã thanh toán</span>
                    @elseif($donHang->trang_thai_thanh_toan === 'that_bai')
                        <span class="badge bg-danger-subtle text-danger border border-danger">Thanh toán thất bại</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning">Chưa thanh toán</span>
                    @endif
                </small>
            @endif
        </div>

        <hr class="my-3">

        <div>
            <h6 class="fw-semibold small text-uppercase text-muted mb-2">Giao hàng đến</h6>
            <p class="mb-1 fw-medium">{{ $donHang->ten_nguoi_nhan }}</p>
            <p class="mb-1 small"><i class="bi bi-phone me-1"></i>{{ $donHang->so_dien_thoai_nhan_hang }}</p>
            <p class="mb-0 small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $donHang->dia_chi_chi_tiet }}</p>

            @if ($donHang->ghi_chu)
                <div class="mt-3">
                    <small class="fw-medium text-muted text-uppercase d-block mb-1">Ghi chú của bạn</small>
                    <p class="small text-muted mb-0">{{ $donHang->ghi_chu }}</p>
                </div>
            @endif
        </div>

        @if ($donHang->trang_thai === 'da_hoan_thanh')
            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-header bg-light py-3 px-4">
                    <h5 class="mb-0 fw-semibold">Đánh giá sản phẩm</h5>
                </div>
                <div class="card-body">
                    @foreach ($donHang->chiTietDonHangs as $chiTiet)
                        @php
                            $daDanhGia = \App\Models\BinhLuan::where('user_id', auth()->id())
                                ->where('san_pham_id', $chiTiet->sanPham->id)
                                ->where('don_hang_id', $donHang->id)
                                ->exists();
                        @endphp
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $chiTiet->sanPham->hinh_anh_chinh ? asset('storage/' . $chiTiet->sanPham->hinh_anh_chinh) : 'https://via.placeholder.com/60' }}"
                                    width="60" height="60" class="rounded me-3" style="object-fit:cover">
                                <div><strong>{{ $chiTiet->sanPham->ten_san_pham }}</strong></div>
                            </div>
                            @if ($daDanhGia)
                                <div class="alert alert-success mb-0"><i class="bi bi-check-circle"></i> Bạn đã đánh giá
                                    sản phẩm này</div>
                            @else
                                <form action="{{ route('binh-luan.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="san_pham_id" value="{{ $chiTiet->sanPham->id }}">
                                    <input type="hidden" name="don_hang_id" value="{{ $donHang->id }}">
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Số sao</label>
                                        <select name="so_sao" class="form-select w-auto">
                                            <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>
                                            <option value="4">⭐⭐⭐⭐ (4 sao)</option>
                                            <option value="3">⭐⭐⭐ (3 sao)</option>
                                            <option value="2">⭐⭐ (2 sao)</option>
                                            <option value="1">⭐ (1 sao)</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="noi_dung" class="form-control" rows="3" placeholder="Viết đánh giá của bạn..."></textarea>
                                    </div>
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Gửi đánh
                                        giá</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
