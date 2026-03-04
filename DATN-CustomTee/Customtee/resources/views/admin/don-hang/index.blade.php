@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="container-fluid" style="margin-top: 30px;">

    <div class="row mb-3">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h4>
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

    {{-- Lọc theo trạng thái & trả hàng --}}
    <div class="card shadow mb-3">
        <div class="card-body py-2">
            <form method="get" action="{{ route('admin.don-hang.index') }}" class="form-inline">
                <label class="mr-2 mb-0">Trạng thái:</label>
                <select name="trang_thai" class="form-control form-control-sm mr-3" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    @foreach([
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'dang_xu_ly' => 'Đang xử lý',
                        'dang_giao' => 'Đang giao',
                        'da_giao' => 'Đã giao',
                        'da_huy' => 'Đã hủy',
                    ] as $value => $label)
                        <option value="{{ $value }}" {{ request('trang_thai') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                {{-- <div class="form-check form-check-inline align-middle">
                    <input class="form-check-input" type="checkbox" name="yeu_cau_tra" value="1"
                           id="filterYeuCauTra" onchange="this.form.submit()" {{ request('yeu_cau_tra') ? 'checked' : '' }}>
                    <label class="form-check-label small mb-0" for="filterYeuCauTra">
                        Chỉ đơn có yêu cầu trả hàng
                    </label>
                </div> --}}
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th width="4%">#</th>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Trả hàng</th>
                        <th width="12%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donHangs as $index => $donHang)
                    <tr>
                        <td class="text-center">{{ $donHangs->firstItem() + $index }}</td>
                        <td><strong>{{ $donHang->ma_don_hang }}</strong></td>
                        <td>
                            {{ $donHang->ten_nguoi_nhan ?? $donHang->nguoiDung->name ?? '—' }}
                            @if($donHang->nguoiDung)
                                <br><small class="text-muted">{{ $donHang->nguoiDung->email ?? '' }}</small>
                            @endif
                        </td>
                        <td>{{ $donHang->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">{{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</td>
                        <td class="text-center">
                            @php
                                $badge = [
                                    'cho_xac_nhan' => 'warning',
                                    'dang_xu_ly' => 'info',
                                    'dang_giao' => 'primary',
                                    'da_giao' => 'success',
                                    'da_huy' => 'danger',
                                ][$donHang->trang_thai] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $badge }}">{{ \App\Models\DonHang::tenTrangThai($donHang->trang_thai) }}</span>
                        </td>
                        <td class="text-center">
                            @if($donHang->yeu_cau_tra)
                                <span class="badge badge-warning">
                                    <i class="fa fa-undo mr-1"></i> Yêu cầu trả hàng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.don-hang.show', $donHang) }}" class="btn btn-sm btn-info" title="Chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Chưa có đơn hàng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($donHangs->hasPages())
                <div class="d-flex justify-content-center mt-2">
                    {{ $donHangs->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
