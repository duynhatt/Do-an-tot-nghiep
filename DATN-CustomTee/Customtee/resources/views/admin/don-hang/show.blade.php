@extends('admin.layout.AdminLayout')
@section('AdminContent')
@section('AdminContent')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    @php
        $badge =
            [
                'cho_xac_nhan' => 'warning',
                'dang_xu_ly' => 'info',
                'dang_giao' => 'primary',
                'da_giao' => 'success',
                'da_hoan_thanh' => 'success',
                'da_huy' => 'danger',
            ][$donHang->trang_thai] ?? 'secondary';

        $trangThaiTiepTheo = \App\Models\DonHang::trangThaiTiepTheo($donHang->trang_thai);

        unset($trangThaiTiepTheo[\App\Models\DonHang::TRANG_THAI_DA_HOAN_THANH]);

        if ($donHang->phuong_thuc_thanh_toan === 'vnpay' && $donHang->trang_thai_thanh_toan !== 'da_thanh_toan') {
            $trangThaiTiepTheo = array_filter(
                $trangThaiTiepTheo,
                fn($key) => $key === \App\Models\DonHang::TRANG_THAI_DA_HUY,
                ARRAY_FILTER_USE_KEY,
            );
        }

        $tenTrangThaiHienTai = \App\Models\DonHang::tenTrangThai($donHang->trang_thai);
    @endphp
    <div class="container-fluid" style="margin-top: 30px;">
        <div class="row mb-4 align-items-center">
            <div class="col-auto">
                <a href="{{ route('admin.don-hang.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
                </a>
            </div>
            <div class="col">
                <h1 class="h3 mb-0 fw-bold text-dark">
                    Chi tiết đơn hàng
                    <span class="text-primary">#{{ $donHang->ma_don_hang }}</span>
                </h1>
                <p class="text-muted mb-0">
                    Đặt lúc {{ $donHang->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="col-auto text-end">
                <span class="badge bg-{{ $badge }} fs-5 px-4 py-2">
                    {{ $tenTrangThaiHienTai }}
                </span>
                @if ($donHang->yeu_cau_tra)
                    <span class="badge bg-danger fs-6 px-3 py-2 mt-2 d-inline-block">
                        <i class="fas fa-undo-alt me-1"></i> Yêu cầu trả hàng
                    </span>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-info-circle text-primary me-2"></i> Thông tin đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                    <i class="fas fa-truck me-2"></i> Thông tin giao hàng
                                </h6>
                                <div class="bg-light rounded-3 p-3">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted w-35">Người nhận:</td>
                                            <td class="fw-medium">{{ $donHang->ten_nguoi_nhan }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Số điện thoại:</td>
                                            <td class="fw-medium">{{ $donHang->so_dien_thoai_nhan_hang }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted align-top">Địa chỉ:</td>
                                            <td class="fw-medium">{{ $donHang->dia_chi_chi_tiet }}</td>
                                        </tr>
                                        @if ($donHang->nguoiDung)
                                            <tr>
                                                <td class="text-muted">Tài khoản:</td>
                                                <td>{{ $donHang->nguoiDung->name ?? $donHang->nguoiDung->email }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="text-uppercase text-muted small fw-semibold mb-3">
                                    <i class="fas fa-receipt me-2"></i> Thông tin thanh toán
                                </h6>
                                <div class="bg-light rounded-3 p-3">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted">Tạm tính:</td>
                                            <td class="text-end">{{ number_format($donHang->tam_tinh, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Giảm giá:</td>
                                            <td class="text-end text-danger">
                                                -{{ number_format($donHang->tien_giam ?? 0, 0, ',', '.') }} ₫</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phí vận chuyển:</td>
                                            <td class="text-end">
                                                {{ number_format($donHang->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="fw-bold">Tổng thanh toán:</td>
                                            <td class="text-end fw-bold text-primary fs-5">
                                                {{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="mt-3 small">
                                    <span class="text-muted">Phương thức:</span>
                                    <strong>
                                        {{ $donHang->phuong_thuc_thanh_toan === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'VNPay' }}
                                    </strong>
                                    @if ($donHang->trang_thai_thanh_toan === 'da_thanh_toan' || $donHang->trang_thai === 'da_hoan_thanh')
                                        <span class="badge bg-success ms-2">Đã thanh toán</span>
                                    @elseif($donHang->trang_thai_thanh_toan === 'that_bai')
                                        <span class="badge bg-danger ms-2">Thanh toán thất bại</span>
                                    @else
                                        <span class="badge bg-warning ms-2">Chưa thanh toán</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-boxes me-2"></i> Sản phẩm trong đơn hàng
                        </h5>
                        <span class="badge bg-primary">{{ count($donHang->chiTietDonHangs) }} sản phẩm</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th>Sản phẩm</th>
                                        <th>Biến thể</th>
                                        <th width="80" class="text-center">Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($donHang->chiTietDonHangs as $i => $ct)
                                        <tr>
                                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($ct->sanPham->hinh_anh_chinh ?? null)
                                                        <img src="{{ asset('storage/' . $ct->sanPham->hinh_anh_chinh) }}"
                                                            alt="" width="45" height="45"
                                                            class="rounded me-3 object-fit-cover">
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $ct->sanPham->ten_san_pham }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small text-muted">
                                                @if ($ct->bienThe)
                                                    {{ $ct->bienThe->color->ten_mau ?? '—' }} /
                                                    {{ $ct->bienThe->size->ten_kich_thuoc ?? '—' }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-center fw-medium">{{ $ct->so_luong }}</td>
                                            <td class="text-end">{{ number_format($ct->don_gia, 0, ',', '.') }} ₫</td>
                                            <td class="text-end fw-bold text-primary">
                                                {{ number_format($ct->thanh_tien, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-light p-4 border-top">
                            <div class="d-flex justify-content-between align-items-center fs-5">
                                <span class="fw-bold text-dark">Tổng tiền đơn hàng</span>
                                <span class="fw-bold text-primary">
                                    {{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($donHang->ghi_chu || $donHang->yeu_cau_tra)
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            @if ($donHang->ghi_chu)
                                <div class="mb-4">
                                    <h6 class="fw-semibold text-muted mb-2">
                                        <i class="fas fa-comment-dots me-2"></i> Ghi chú của khách hàng
                                    </h6>
                                    <div class="bg-light p-3 rounded-3">
                                        {{ $donHang->ghi_chu }}
                                    </div>
                                </div>
                            @endif

                            @if ($donHang->yeu_cau_tra)
                                <div>
                                    <h6 class="fw-semibold text-danger mb-2">
                                        <i class="fas fa-undo-alt me-2"></i> Yêu cầu trả hàng từ khách
                                    </h6>
                                    <div class="alert alert-danger border-0">
                                        <strong>Thời gian:</strong>
                                        {{ $donHang->ngay_yeu_cau_tra?->format('d/m/Y H:i') }}<br>
                                        <strong>Lý do:</strong> {{ $donHang->ly_do_tra }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-exchange-alt me-2"></i> Cập nhật trạng thái đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (count($trangThaiTiepTheo) > 0)
                            <form action="{{ route('admin.don-hang.update-status', $donHang) }}" method="post"
                                class="row g-3 align-items-end">
                                @csrf
                                @method('PATCH')

                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-medium text-muted">Chuyển sang trạng thái</label>
                                    <select name="trang_thai" class="form-select form-select-lg" required>
                                        @foreach ($trangThaiTiepTheo as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-3">
                                    <button type="submit" class="btn btn-success btn-lg px-5 w-100"
                                        onclick="return confirm('Xác nhận thay đổi trạng thái đơn hàng?')">
                                        <i class="fas fa-check me-2"></i> Cập nhật trạng thái
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-secondary text-center py-4">
                                <i class="fas fa-info-circle fa-2x mb-3 text-muted"></i>
                                <p class="mb-0">Đơn hàng đã hoàn tất hoặc bị hủy. Không thể chuyển trạng thái tiếp theo.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
