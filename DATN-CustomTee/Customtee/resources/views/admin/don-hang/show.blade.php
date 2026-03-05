@extends('admin.layout.AdminLayout')
@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">
    @php
        $badge = [
            'cho_xac_nhan' => 'warning',
            'dang_xu_ly' => 'info',
            'dang_giao' => 'primary',
            'da_giao' => 'success',
            'da_hoan_thanh' => 'success',
            'da_huy' => 'danger',
        ][$donHang->trang_thai] ?? 'secondary';
        $trangThaiTiepTheo = \App\Models\DonHang::trangThaiTiepTheo($donHang->trang_thai);
        $tenTrangThaiHienTai = \App\Models\DonHang::tenTrangThai($donHang->trang_thai);
    @endphp

    {{-- Header: quay lại + mã đơn + trạng thái --}}
    <div class="row mb-3 align-items-center">
        <div class="col-auto">
            <a href="{{ route('admin.don-hang.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Danh sách đơn hàng
            </a>
        </div>
        <div class="col">
            <h2 class="mb-0 text-dark">Chi tiết đơn hàng <span class="text-primary">#{{ $donHang->ma_don_hang }}</span></h2>
            <small class="text-muted"><i class="far fa-clock mr-1"></i>Đặt lúc {{ $donHang->created_at->format('d/m/Y H:i') }}</small>
        </div>
        <div class="col-auto text-right">
            <span class="badge badge-{{ $badge }} px-3 py-2 mb-1" style="font-size: 0.95rem;">
                {{ $tenTrangThaiHienTai }}
            </span>
            @if($donHang->yeu_cau_tra)
                <div>
                    <span class="badge badge-danger px-3 py-1" style="font-size: 0.85rem;">
                        Yêu cầu trả hàng
                    </span>
                </div>
            @endif
        </div>
    </div>
<hr>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row align-items-stretch">
                        <div class="col-md-6 border-right" style="min-height: 160px;">
                            <h6 class="text-uppercase text-muted small mb-2"><i class="fas fa-user mr-1"></i> Giao hàng</h6>
                            <table class="table table-sm table-borderless mb-0 small">
                                <tr><td class="text-muted" width="100">Người nhận</td><td>{{ $donHang->ten_nguoi_nhan }}</td></tr>
                                <tr><td class="text-muted">SĐT</td><td>{{ $donHang->so_dien_thoai_nhan_hang }}</td></tr><tr><td class="text-muted">Địa chỉ</td><td>{{ $donHang->dia_chi_chi_tiet }}</td></tr>
                                @if($donHang->nguoiDung)
                                    <tr><td class="text-muted">Tài khoản</td><td>{{ $donHang->nguoiDung->name ?? $donHang->nguoiDung->email }}</td></tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6" style="min-height: 160px;">
                            <h6 class="text-uppercase text-muted small mb-2"><i class="fas fa-receipt mr-1"></i> Thanh toán</h6>
                            <table class="table table-sm table-borderless mb-1 small">
                                <tr><td class="text-muted">Tạm tính</td><td class="text-right">{{ number_format($donHang->tam_tinh, 0, ',', '.') }} ₫</td></tr>
                                <tr><td class="text-muted">Giảm giá</td><td class="text-right text-danger">-{{ number_format($donHang->tien_giam ?? 0, 0, ',', '.') }} ₫</td></tr>
                                <tr><td class="text-muted">Phí ship</td><td class="text-right">{{ number_format($donHang->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</td></tr>
                            </table>

                        </div>
                    </div>
                    @if($donHang->ghi_chu)
                        <div class="row mt-2 pt-2 border-top">
                            <div class="col-12">
                                <p class=""><strong class="text-muted">Ghi chú của khách:</strong> {{ $donHang->ghi_chu }}</p>
                            </div>
                        </div>
                        
                    @endif
                    @if($donHang->yeu_cau_tra)
                        <div class="row mt-2 pt-2 border-top">
                            <div class="col-12">
                                <p class="mb-1">
                                    <strong class="text-danger">Khách yêu cầu trả hàng</strong>
                                    @if($donHang->ngay_yeu_cau_tra)
                                        <span class="text-muted small">
                                            (lúc {{ $donHang->ngay_yeu_cau_tra->format('d/m/Y H:i') }})
                                        </span>
                                    @endif
                                </p>
                                @if($donHang->ly_do_tra)
                                    <p class="small mb-0"><strong class="text-muted">Lý do:</strong> {{ $donHang->ly_do_tra }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sản phẩm trong đơn --}}
            <div class="card shadow-sm">
              
                <div class="card-body p-0">
                    <div class="table-responsive"><table class="table table-hover table-sm mb-0">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th width="40">#</th>
                                    <th>Sản phẩm</th>
                                    <th>màu sắc/kích thước</th>
                                    <th width="60">SL</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donHang->chiTietDonHangs as $i => $ct)
                                <tr>
                                    <td class="text-center text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        @if($ct->sanPham->hinh_anh_chinh ?? null)
                                            <img src="{{ asset('storage/' . $ct->sanPham->hinh_anh_chinh) }}" alt="" width="40" height="40" class="rounded mr-2">
                                        @endif
                                        <span class="small">{{ $ct->sanPham->ten_san_pham ?? '—' }}</span>
                                    </td>
                                    <td class="small">
                                        @if($ct->bienThe)
                                            {{ $ct->bienThe->color->ten_mau ?? '—' }} / {{ $ct->bienThe->size->ten_kich_thuoc ?? '—' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $ct->so_luong }}</td>
                                    <td class="text-right">{{ number_format($ct->don_gia, 0, ',', '.') }} ₫</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($ct->thanh_tien, 0, ',', '.') }} ₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                         <div class="d-flex justify-content-between align-items-center py-2 px-2 rounded bg-light">
                                <span class="font-weight-bold text-muted small">Tổng cộng</span>
                                <span class="font-weight-bold text-primary">{{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</span>
                            </div>
                            <p class="small mt-2 mb-0">
                                <span class="text-muted">Thanh toán:</span>
                                {{ $donHang->phuong_thuc_thanh_toan === 'cod' ? 'COD' : ucfirst($donHang->phuong_thuc_thanh_toan) }}@if($donHang->trang_thai_thanh_toan === 'da_thanh_toan')
                                    <span class="badge badge-success ml-1">Đã TT</span>
                                @elseif($donHang->trang_thai_thanh_toan === 'that_bai')
                                    <span class="badge badge-danger ml-1">Thất bại</span>
                                @else
                                    {{-- <span class="badge badge-warning ml-1">Chưa thanh toán</span> --}}
                                @endif
                            </p>
                    </div>
                </div>
            </div>
<div class="card shadow border-0 mt-4">
    <div class="card-header bg-white border-bottom py-3">
       
    </div>

    <div class="card-body py-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between">

           

            {{-- Form chuyển trạng thái --}}
            @if(count($trangThaiTiepTheo) > 0)
                <div>
                    <form action="{{ route('admin.don-hang.update-status', $donHang) }}"
                          method="post"
                          class="d-flex flex-wrap align-items-center">

                        @csrf
                        @method('PATCH')

                        <div class="mr-3">
                            <label class="small text-muted d-block mb-1">
                                Chuyển sang
                            </label>
                                    
                            <select name="trang_thai"
                                    class="form-control form-control-md"
                                    style="min-width: 220px;"
                                    required>
                                @foreach($trangThaiTiepTheo as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                            
                        <div class="mt-4 mt-md-0">
                            <button type="submit"
                                    class="btn btn-success px-4"
                                    onclick="return confirm('Xác nhận chuyển trạng thái đơn hàng?');">
                                <i class="fas fa-check mr-2"></i>
                                Cập nhật
                            </button>
                        </div>

                    </form>

                 
                </div>
            @else
                <div class="w-100 mt-3">
                    <div class="alert alert-secondary mb-0 text-center">
                        Đơn hàng đã hoàn tất hoặc bị huỷ. Không thể chuyển tiếp.
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
        </div>
    </div>

</div>
@endsection
