@extends('admin.layout.AdminLayout')

@section('AdminContent')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <div class="container-fluid px-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Chi tiết yêu cầu hoàn trả #{{ $refund->id }}</h1>
                <div class="text-muted small">
                    Yêu cầu ngày: <strong>{{ $refund->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if (!$refund->da_xu_ly)
                    <form action="{{ route('admin.hoan-tra.accept', $refund) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                            onclick="return confirm('Xác nhận chấp nhận yêu cầu hoàn trả này?')">
                            <i class="bi bi-check-circle"></i> Chấp nhận
                        </button>
                    </form>

                    <form action="{{ route('admin.hoan-tra.reject', $refund) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-1"
                            onclick="return confirm('Bạn chắc chắn muốn từ chối? Vui lòng ghi chú lý do trong controller nếu cần.')">
                            <i class="bi bi-x-circle"></i> Từ chối
                        </button>
                    </form>
                @else
                    <span class="badge bg-secondary px-3 py-2 fs-6">Đã xử lý</span>
                @endif

                <a href="{{ route('admin.hoan-tra.index') }}"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-primary">
                            <i class="bi bi-info-circle me-2"></i>Thông tin yêu cầu hoàn trả
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-1">Trạng thái</label>
                                <div>
                                    @php
                                        $statusClasses = [
                                            'cho_xu_ly' => 'bg-warning text-dark',
                                            'da_chap_nhan' => 'bg-success text-white',
                                            'da_tu_choi' => 'bg-danger text-white',
                                            'da_hoan_tien' => 'bg-info text-white',
                                        ];
                                        $currentClass =
                                            $statusClasses[$refund->trang_thai] ?? 'bg-secondary text-white';
                                    @endphp
                                    <span class="badge rounded-pill px-4 py-2 fs-6 fw-medium {{ $currentClass }}">
                                        {{ $refund->trang_thai_text }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-1">Số tiền yêu cầu</label>
                                <h5 class="fw-bold text-danger mb-0">
                                    {{ number_format($refund->so_tien_yeu_cau, 0, ',', '.') }} ₫
                                </h5>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-medium text-muted mb-1">Lý do hoàn trả</label>
                                <p class="mb-0">{{ $refund->ly_do ?? 'Không có lý do cụ thể' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-1">Phương thức hoàn tiền</label>
                                <p class="mb-0 fw-medium">
                                    {{ $refund->phuong_thuc_thanh_toan ?? 'Không xác định' }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-1">Khách hàng</label>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle fs-4 text-secondary"></i>
                                    <div>
                                        <div class="fw-medium">{{ $refund->user->name ?? 'Khách vãng lai' }}</div>
                                        <small class="text-muted">{{ $refund->user->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-primary">
                            <i class="bi bi-box-seam me-2"></i>Sản phẩm yêu cầu hoàn trả
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th class="ps-4 py-3">Sản phẩm</th>
                                        <th class="py-3 text-end">Đơn giá</th>
                                        <th class="py-3 text-center">SL yêu cầu</th>
                                        <th class="py-3 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($refund->items as $item)
                                        @php
                                            $chiTiet = $item->chiTietDonHang;
                                            $sanPham = $chiTiet?->sanPham;
                                            $bienThe = $chiTiet?->bienThe;
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="flex-shrink-0">
                                                        @if ($sanPham?->hinh_anh_chinh)
                                                            <img src="{{ asset('storage/' . $sanPham->hinh_anh_chinh) }}"
                                                                alt="{{ $sanPham->ten_san_pham }}" width="50"
                                                                class="rounded">
                                                        @else
                                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                                                style="width:50px;height:50px;">
                                                                <i class="bi bi-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $sanPham?->ten_san_pham ?? 'N/A' }}</div>
                                                        @if ($bienThe)
                                                            <small class="text-muted d-block">
                                                                <span class="fw-medium">
                                                                    {{ $bienThe->color->ten_mau ?? 'Mặc định' }}
                                                                    -
                                                                    {{ $bienThe->size->ten_kich_thuoc ?? 'Mặc định' }}
                                                                </span>
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($chiTiet?->don_gia ?? 0, 0, ',', '.') }} ₫
                                            </td>
                                            <td class="text-center">{{ $item->so_luong_yeu_cau }}</td>
                                            <td class="text-end fw-bold text-danger">
                                                {{ number_format($item->thanh_tien_yeu_cau ?? $item->tinhThanhTien(), 0, ',', '.') }}
                                                ₫
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                Không có sản phẩm nào trong yêu cầu này.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold text-primary">
                            <i class="bi bi-receipt me-2"></i>Đơn hàng liên quan
                            <a href="{{ route('admin.don-hang.show', $refund->donHang?->id) }}">
                                #{{ $refund->donHang?->ma_don_hang ?? 'N/A' }}
                            </a>
                        </h6>
                    </div>
                    <div class="card-body small">
                        <dl class="row mb-0 gy-2">
                            <dt class="col-sm-5 text-muted">Trạng thái đơn</dt>
                            <dd class="col-sm-7 fw-medium text-end">
                                {{ \App\Models\DonHang::tenTrangThai($refund->donHang?->trang_thai ?? '') }}
                            </dd>

                            <dt class="col-sm-5 text-muted">Tổng tiền đơn</dt>
                            <dd class="col-sm-7 fw-bold text-end">
                                {{ number_format($refund->donHang?->tong_tien ?? 0, 0, ',', '.') }} ₫
                            </dd>

                            <dt class="col-sm-5 text-muted">Phương thức TT</dt>
                            <dd class="col-sm-7 text-end">
                                {{ $refund->donHang?->phuong_thuc_thanh_toan ?? 'N/A' }}
                            </dd>

                            <dt class="col-sm-5 text-muted">Ngày đặt</dt>
                            <dd class="col-sm-7 text-end">
                                {{ $refund->donHang?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </dd>
                        </dl>
                    </div>
                </div>

                @if ($refund->images->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="bi bi-images me-2"></i>Hình ảnh minh chứng
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach ($refund->images as $image)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $image->url }}" target="_blank" class="d-block">
                                            <img src="{{ $image->url }}" alt="{{ $image->original_name }}"
                                                class="img-fluid rounded shadow-sm" loading="lazy">
                                        </a>
                                        <small class="d-block text-center text-muted mt-1">
                                            {{ $image->human_size }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        dt {
            font-weight: 500;
        }

        .badge.rounded-pill {
            min-width: 140px;
            text-align: center;
        }

        tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
            transition: background-color 0.15s;
        }
    </style>
@endsection
