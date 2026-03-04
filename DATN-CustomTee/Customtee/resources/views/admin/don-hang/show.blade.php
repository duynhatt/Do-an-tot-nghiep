@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('admin.don-hang.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Danh sách đơn hàng
            </a>
        </div>
    </div>

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

    <div class="card shadow mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Đơn hàng #{{ $donHang->ma_don_hang }}</h5>
            @php
                $badge = [
                    'cho_xac_nhan' => 'warning',
                    'dang_xu_ly' => 'info',
                    'dang_giao' => 'primary',
                    'da_giao' => 'success',
                    'da_huy' => 'danger',
                ][$donHang->trang_thai] ?? 'secondary';
            @endphp
            <span class="badge badge-{{ $badge }} badge-lg">{{ \App\Models\DonHang::tenTrangThai($donHang->trang_thai) }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Người nhận:</strong> {{ $donHang->ten_nguoi_nhan }}</p>
                    <p><strong>SĐT:</strong> {{ $donHang->so_dien_thoai_nhan_hang }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $donHang->dia_chi_chi_tiet }}</p>
                    @if($donHang->nguoiDung)
                        <p><strong>Khách hàng (TK):</strong> {{ $donHang->nguoiDung->name ?? $donHang->nguoiDung->email }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Đặt lúc:</strong> {{ $donHang->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Thanh toán:</strong> {{ $donHang->phuong_thuc_thanh_toan === 'cod' ? 'COD' : ucfirst($donHang->phuong_thuc_thanh_toan) }}</p>
                    <p><strong>Tạm tính:</strong> {{ number_format($donHang->tam_tinh, 0, ',', '.') }} ₫</p>
                    <p><strong>Giảm giá:</strong> -{{ number_format($donHang->tien_giam ?? 0, 0, ',', '.') }} ₫</p>
                    <p><strong>Phí ship:</strong> {{ number_format($donHang->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</p>
                    <p><strong>Tổng cộng:</strong> <span class="text-primary font-weight-bold">{{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</span></p>
                </div>
            </div>
            @if($donHang->ghi_chu)
                <p class="mb-1"><strong>Ghi chú:</strong> {{ $donHang->ghi_chu }}</p>
            @endif

            @if($donHang->yeu_cau_tra)
                <hr>
                <div class="alert alert-info mb-0">
                    <p class="mb-1"><strong>Khách đã yêu cầu trả hàng.</strong></p>
                    @if($donHang->ngay_yeu_cau_tra)
                        <p class="mb-1 small"><strong>Thời gian yêu cầu:</strong> {{ $donHang->ngay_yeu_cau_tra->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($donHang->ly_do_tra)
                        <p class="mb-0 small"><strong>Lý do khách cung cấp:</strong> {{ $donHang->ly_do_tra }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Chuyển trạng thái --}}
    @php
        $trangThaiTiepTheo = \App\Models\DonHang::trangThaiTiepTheo($donHang->trang_thai);
        $tenTrangThaiHienTai = \App\Models\DonHang::tenTrangThai($donHang->trang_thai);
    @endphp
    <div class="card shadow mb-3">
        <div class="card-header">
            <h5 class="mb-0">Chuyển trạng thái đơn hàng</h5>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>Trạng thái hiện tại:</strong>
                <span class="badge badge-{{ $badge }} badge-lg">
                    {{ $tenTrangThaiHienTai }}
                </span>
            </p>

            @if(count($trangThaiTiepTheo) > 0)
                <p class="text-muted small mb-3">
                    Chỉ hiển thị những trạng thái hợp lệ có thể chuyển sang từ trạng thái hiện tại.
                </p>
                <form action="{{ route('admin.don-hang.update-status', $donHang) }}" method="post" class="form">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label class="control-label small">Chọn trạng thái mới</label>
                        <select name="trang_thai" class="form-control input-sm" style="max-width: 260px;" required>
                            @foreach($trangThaiTiepTheo as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"
                            onclick="return confirm('Xác nhận chuyển trạng thái đơn hàng sang trạng thái mới?');">
                        <i class="fa fa-refresh"></i> Cập nhật trạng thái
                    </button>
                </form>
            @else
                <div class="alert alert-light border mb-0">
                    Đơn hàng đã ở trạng thái kết thúc (<strong>{{ $tenTrangThaiHienTai }}</strong>), không thể chuyển tiếp.
                </div>
            @endif
        </div>
    </div>

    {{-- Chi tiết sản phẩm --}}
    <div class="card shadow">
        <div class="card-header"><h5 class="mb-0">Sản phẩm trong đơn</h5></div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th>#</th>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th>SL</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donHang->chiTietDonHangs as $i => $ct)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                            @if($ct->sanPham->hinh_anh_chinh ?? null)
                                <img src="{{ asset('storage/' . $ct->sanPham->hinh_anh_chinh) }}" alt="" width="50" height="50" class="rounded mr-2">
                            @endif
                            {{ $ct->sanPham->ten_san_pham ?? '—' }}
                        </td>
                        <td>
                            @if($ct->bienThe)
                                {{ $ct->bienThe->color->ten_mau ?? '—' }} / {{ $ct->bienThe->size->ten_kich_thuoc ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">{{ $ct->so_luong }}</td>
                        <td class="text-right">{{ number_format($ct->don_gia, 0, ',', '.') }} ₫</td>
                        <td class="text-right font-weight-bold">{{ number_format($ct->thanh_tien, 0, ',', '.') }} ₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
