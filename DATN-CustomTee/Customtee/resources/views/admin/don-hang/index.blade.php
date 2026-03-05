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

    {{-- Lọc theo trạng thái --}}
    <div class="card shadow mb-3">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <form method="get" action="{{ route('admin.don-hang.index') }}" class="form-inline">
                    <label class="mr-2 mb-0">Trạng thái:</label>
                    <select name="trang_thai" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        @foreach([
                            'cho_xac_nhan' => 'Chờ xác nhận',
                            'dang_xu_ly' => 'Đang xử lý',
                            'dang_giao' => 'Đang giao',
                            'da_giao' => 'Đã giao',
                            'da_huy' => 'Đã hủy',
                            // Bộ lọc bổ sung
                            'da_hoan_thanh' => 'Đã hoàn thành',
                            'tra_hang' => 'Trả hàng',
                        ] as $value => $label)
                            <option value="{{ $value }}" {{ request('trang_thai') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <div class="text-muted small">
                    Tổng: <strong>{{ $donHangs->total() }}</strong> đơn
                </div>
            </div>
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
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th width="12%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
@forelse($donHangs as $index => $donHang)
                    <tr>
                        <td class="text-center align-middle">{{ $donHangs->firstItem() + $index }}</td>
                        <td class="align-middle">
                            <strong>{{ $donHang->ma_don_hang }}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="far fa-clock mr-1"></i>{{ $donHang->created_at->format('d/m/Y H:i') }}
                            </small>
                        </td>
                        <td class="align-middle">
                            {{ $donHang->ten_nguoi_nhan ?? $donHang->nguoiDung->name ?? '—' }}
                            @if($donHang->nguoiDung)
                                <br><small class="text-muted">{{ $donHang->nguoiDung->email ?? '' }}</small>
                            @endif
                        </td>
                        <td class="text-right align-middle">{{ number_format($donHang->tong_tien, 0, ',', '.') }} ₫</td>
                        <td class="text-center align-middle">
                            @php
                                $badge = [
                                    'cho_xac_nhan' => 'warning',
                                    'dang_xu_ly' => 'info',
                                    'dang_giao' => 'primary',
                                    'da_giao' => 'success',
                                    'da_hoan_thanh' => 'success',
                                    'da_huy' => 'danger',
                                ][$donHang->trang_thai] ?? 'secondary';
                            @endphp
                            <div>
                                <span class="badge badge-{{ $badge }}">
                                    {{ \App\Models\DonHang::tenTrangThai($donHang->trang_thai) }}
                                </span>
                            </div>
                            @if($donHang->yeu_cau_tra)
                                <div class="mt-1">
                                    <span class="badge badge-danger">Yêu cầu trả hàng</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex justify-content-center flex-wrap gap-1">
                                @if($donHang->trang_thai === 'da_giao' && !$donHang->yeu_cau_tra)
                                    <form action="{{ route('admin.don-hang.update-status', $donHang) }}"
                                          method="post"
                                          onsubmit="return confirm('Xác nhận chuyển đơn hàng này sang trạng thái \"Đã hoàn thành\"?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="trang_thai" value="da_hoan_thanh">
                                        <button type="submit" class="btn btn-sm btn-success" title="Xác nhận đã hoàn thành">
                                            <i class="fas fa-check mr-1"></i> Hoàn thành
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.don-hang.show', $donHang) }}" class="btn btn-sm btn-info" title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có đơn hàng nào.</td>
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