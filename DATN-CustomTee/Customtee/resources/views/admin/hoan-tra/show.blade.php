@extends('admin.layout.AdminLayout')
@section('AdminContent')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    @php
        $refundInfoLabel = ($refund->donHang?->trang_thai === \App\Models\DonHang::TRANG_THAI_DA_GIAO)
            ? 'Thông tin yêu cầu hoàn trả'
            : 'Thông tin yêu cầu hoàn tiền';
    @endphp
    <div class="container-fluid px-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Chi tiết yêu cầu hoàn trả #{{ $refund->id }}</h1>
                <div class="text-muted small">
                    Yêu cầu ngày: <strong>{{ $refund->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if ($refund->trang_thai === 'cho_xu_ly')
                    <form action="{{ route('admin.hoan-tra.accept', $refund) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1"
                            onclick="return confirm('Xác nhận chấp nhận yêu cầu hoàn trả này?')">
                            <i class="bi bi-check-circle"></i> Chấp nhận
                        </button>
                    </form>

                    @if (!$isForcedAcceptCase)
                        <form action="{{ route('admin.hoan-tra.reject', $refund) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-1"
                                onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?')">
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>
                        </form>
                    @endif
                @elseif ($refund->trang_thai === 'da_chap_nhan')
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                        data-toggle="modal" data-target="#modalHoanTien">
                        <i class="bi bi-currency-exchange"></i> Hoàn tiền
                    </button>
                @elseif ($refund->trang_thai === 'da_hoan_tien')
                    <span class="badge bg-success px-3 py-2 fs-6 d-flex align-items-center gap-1">
                        <i class="bi bi-check2-all"></i> Đã hoàn tiền
                    </span>
                @elseif ($refund->trang_thai === 'da_tu_choi')
                    <span class="badge bg-danger px-3 py-2 fs-6 d-flex align-items-center gap-1">
                        <i class="bi bi-x-circle"></i> Đã từ chối
                    </span>
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
                            <i class="bi bi-info-circle me-2"></i>{{ $refundInfoLabel }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-2">Trạng thái</label>
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
                                <label class="form-label small fw-medium text-muted mb-2">Số tiền yêu cầu</label>
                                <h4 class="fw-bold text-danger mb-0">
                                    {{ number_format($refund->so_tien_yeu_cau, 0, ',', '.') }} ₫
                                </h4>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-2">Phương thức hoàn tiền</label>
                                <p class="mb-0 fw-medium text-uppercase">{{ $refund->phuong_thuc_thanh_toan ?? 'Không xác định' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted mb-2">Khách hàng</label>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-person-circle fs-4 text-secondary"></i>
                                    <div>
                                        <div class="fw-medium">{{ $refund->donHang?->ten_nguoi_nhan ?? 'Khách vãng lai' }}</div>
                                        <div class="small text-muted">
                                            {{ $refund->donHang?->so_dien_thoai_nhan_hang ?? $refund->user?->phone ?? 'Chưa có số điện thoại' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-medium text-muted mb-2">Lý do yêu cầu</label>
                                <div class="bg-light rounded-3 p-3 border">
                                    {{ $refund->ly_do ?? 'Không có lý do cụ thể' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-primary">
                            <i class="bi bi-box-seam me-2"></i>Sản phẩm yêu cầu
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                                                    <div class="">
                                                        @if ($sanPham?->hinh_anh_chinh)
                                                            <img src="{{ asset('storage/' . $sanPham->hinh_anh_chinh) }}"
                                                                alt="{{ $sanPham->ten_san_pham }}" width="180"
                                                                height="130" class="rounded shadow-sm refund-product-thumb">
                                                        @else
                                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                                                style="width:180px;height:130px;">
                                                                <i class="bi bi-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold fs-5">{{ $sanPham?->ten_san_pham ?? 'N/A' }}</div>
                                                        @if ($bienThe)
                                                            <div class="text-muted fs-6 d-block">
                                                                {{ $bienThe->color->ten_mau ?? 'Mặc định' }} -
                                                                {{ $bienThe->size->ten_kich_thuoc ?? 'Mặc định' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($chiTiet?->don_gia ?? 0, 0, ',', '.') }}
                                                ₫</td>
                                            <td class="text-center">{{ $item->so_luong_yeu_cau }}</td>
                                            <td class="text-end fw-bold text-danger">
                                                {{ number_format($item->thanh_tien_yeu_cau ?? 0, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Không có sản phẩm nào.
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
                                {{ \App\Models\DonHang::tenTrangThai($refund->donHang?->trang_thai ?? '') }}</dd>

                            <dt class="col-sm-5 text-muted">Tổng tiền đơn</dt>
                            <dd class="col-sm-7 fw-bold text-end">
                                {{ number_format($refund->donHang?->tong_tien ?? 0, 0, ',', '.') }} ₫</dd>

                            <dt class="col-sm-5 text-muted">Tạm tính hàng</dt>
                            <dd class="col-sm-7 text-end">
                                {{ number_format($refund->donHang?->tam_tinh ?? 0, 0, ',', '.') }} ₫</dd>

                            <dt class="col-sm-5 text-muted">Giảm giá</dt>
                            <dd class="col-sm-7 text-end">
                                -{{ number_format($refund->donHang?->tien_giam ?? 0, 0, ',', '.') }} ₫</dd>

                            <dt class="col-sm-5 text-muted">Phí vận chuyển</dt>
                            <dd class="col-sm-7 text-end">
                                {{ number_format($refund->donHang?->phi_van_chuyen ?? 0, 0, ',', '.') }} ₫</dd>

                            <dt class="col-sm-5 text-muted">Phương thức TT</dt>
                            <dd class="col-sm-7 text-end">{{ $refund->donHang?->phuong_thuc_thanh_toan ?? 'N/A' }}</dd>

                            <dt class="col-sm-5 text-muted">Ngày đặt</dt>
                            <dd class="col-sm-7 text-end">
                                {{ $refund->donHang?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                        </dl>
                        <div class="alert alert-light border small mt-3 mb-0">
                            @if ($refund->donHang?->trang_thai === \App\Models\DonHang::TRANG_THAI_DA_GIAO)
                                Quy tắc áp dụng: đơn đã giao chỉ hoàn phần tiền hàng thực trả sau giảm giá, không hoàn phí vận chuyển.
                            @else
                                Quy tắc áp dụng: đơn chưa giao hoàn theo tổng tiền khách đã thanh toán.
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $bankImages = $refund->images->filter(
                        fn($image) => str_contains((string) $image->path, 'refund_bank_info/'),
                    );
                    $proofImages = $refund->images->filter(
                        fn($image) => str_contains((string) $image->path, 'refund_images/'),
                    );
                    $hasManualBankInfo =
                        $refund->ngan_hang || $refund->so_tai_khoan || $refund->chi_nhanh || $refund->ten_chu_tk;
                    $hasUploadedBankImages = $bankImages->isNotEmpty();
                @endphp
                @if ($hasManualBankInfo || $hasUploadedBankImages)
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="bi bi-bank me-2"></i>Thông tin nhận hoàn tiền
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($hasUploadedBankImages)
                                <div class="alert alert-info small">
                                    <i class="bi bi-upload me-1"></i> Khách hàng đã tải lên ảnh thông tin tài khoản / QR
                                    Code
                                </div>
                                <div class="row g-3 mb-3">
                                    @foreach ($bankImages as $image)
                                        <div class="col-6 col-md-4">
                                            <a href="{{ $image->url }}" target="_blank" class="d-block refund-image-link"
                                                title="Mở ảnh kích thước lớn">
                                                <img src="{{ $image->url }}" alt="{{ $image->original_name }}"
                                                    class="img-fluid rounded shadow-sm refund-thumb-lg" loading="lazy">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($hasManualBankInfo)
                                <div class="alert alert-primary small">
                                    <i class="bi bi-pencil-square me-1"></i> Khách hàng đã nhập thông tin thủ công
                                </div>
                            @endif

                            <dl class="row mb-0 gy-3">

                                @if ($refund->ngan_hang)
                                    <dt class="col-sm-5 text-muted">Ngân hàng</dt>
                                    <dd class="col-sm-7 fw-medium text-end">
                                        {{ $refund->ngan_hang }}
                                    </dd>
                                @endif

                                @if ($refund->so_tai_khoan)
                                    <dt class="col-sm-5 text-muted">Số tài khoản</dt>
                                    <dd class="col-sm-7 fw-medium text-end">
                                        <span class="badge bg-light text-dark px-2 py-1">
                                            {{ $refund->so_tai_khoan }}
                                        </span>
                                    </dd>
                                @endif

                                @if ($refund->chi_nhanh)
                                    <dt class="col-sm-5 text-muted">Chi nhánh</dt>
                                    <dd class="col-sm-7 text-end">
                                        {{ $refund->chi_nhanh }}
                                    </dd>
                                @endif

                                @if ($refund->ten_chu_tk)
                                    <dt class="col-sm-5 text-muted">Chủ tài khoản</dt>
                                    <dd class="col-sm-7 fw-medium text-end">
                                        {{ $refund->ten_chu_tk }}
                                    </dd>
                                @endif

                                @if ($refund->hinh_anh_xac_nhan)
                                    <dt class="col-sm-12 text-primary fw-semibold">Ảnh xác nhận</dt>
                                    <dd class="col-sm-12 mt-2">
                                        <a href="{{ asset('storage/' . $refund->hinh_anh_xac_nhan) }}" target="_blank"
                                            class="refund-image-link" title="Mở ảnh xác nhận kích thước lớn">
                                            <img src="{{ asset('storage/' . $refund->hinh_anh_xac_nhan) }}"
                                                class="img-thumbnail shadow-sm refund-thumb-confirm">
                                        </a>
                                    </dd>
                                @endif

                            </dl>
                        </div>
                    </div>
                @endif

                @if ($proofImages->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold text-primary">
                                <i class="bi bi-images me-2"></i>Hình ảnh minh chứng đơn hàng hoàn trả
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($proofImages as $image)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ $image->url }}" target="_blank" class="d-block refund-image-link"
                                            title="Mở ảnh kích thước lớn">
                                            <img src="{{ $image->url }}" alt="{{ $image->original_name }}"
                                                class="img-fluid rounded shadow-sm refund-thumb-lg" loading="lazy">
                                        </a>
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

        .refund-image-link {
            border-radius: 10px;
            overflow: hidden;
            display: block;
        }

        .refund-image-link:hover img {
            transform: scale(1.03);
            filter: brightness(1.03);
        }

        .refund-thumb-lg {
            width: 100%;
            height: 140px;
            object-fit: cover;
            transition: transform 0.2s ease, filter 0.2s ease;
            border: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .refund-thumb-confirm {
            width: 190px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.2s ease, filter 0.2s ease;
            background: #f8f9fa;
        }

        .refund-product-thumb {
            object-fit: cover;
        }
    </style>

    <form action="{{ route('admin.hoan-tra.complete', $refund) }}" method="POST" style="margin-bottom: 0;"
        enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="modal fade" id="modalHoanTien" tabindex="-1" role="dialog" aria-labelledby="modalHoanTienLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">

                    <div class="modal-body">

                        <div class="alert alert-info" style="border-radius: 6px;">
                            <i class="glyphicon glyphicon-info-sign"></i>
                            Vui lòng kiểm tra kỹ thông tin trước khi xác nhận hoàn tiền.
                        </div>

                        <div class="text-center mb-4">
                            <p class="text-muted small" style="margin-bottom: 5px;">Số tiền cần hoàn tiền</p>
                            <h2 class="text-success" style="font-weight: bold; margin: 0;">
                                {{ number_format($refund->so_tien_yeu_cau ?? 0) }} <small
                                    style="font-size: 18px;">VND</small>
                            </h2>
                        </div>

                        <div class="well well-sm" style="background: #f8f9fa; border-radius: 6px; padding: 15px;">
                            <h5 style="margin-top: 0; color: #337ab7;">
                                <i class="glyphicon glyphicon-user"></i> Thông tin tài khoản nhận tiền
                            </h5>

                            @if (!empty($refund->qr_code_bank))
                                <div class="text-center">
                                    <p class="small text-muted">Quét mã QR để chuyển khoản nhanh</p>
                                    <div
                                        style="display: inline-block; background: white; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                        {!! $refund->qr_code_bank !!}
                                    </div>
                                </div>
                            @else
                                <table class="table table-condensed" style="margin-bottom: 0;">
                                    <tbody>
                                        @if ($refund->ngan_hang)
                                            <tr>
                                                <td width="40%" class="text-muted">Ngân hàng:</td>
                                                <td><strong>{{ $refund->ngan_hang }}</strong></td>
                                            </tr>
                                        @endif

                                        @if ($refund->so_tai_khoan)
                                            <tr>
                                                <td class="text-muted">Số tài khoản:</td>
                                                <td><strong class="font-monospace">{{ $refund->so_tai_khoan }}</strong>
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($refund->ten_chu_tk)
                                            <tr>
                                                <td class="text-muted">Chủ tài khoản:</td>
                                                <td><strong>{{ $refund->ten_chu_tk }}</strong></td>
                                            </tr>
                                        @endif

                                        @if ($refund->chi_nhanh)
                                            <tr>
                                                <td class="text-muted">Chi nhánh:</td>
                                                <td><strong>{{ $refund->chi_nhanh }}</strong></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="form-group mt-3">
                            <label class="fw-medium">Ảnh xác nhận chuyển khoản</label>
                            <input type="file" name="hinh_anh_xac_nhan" class="form-control" accept="image/*"
                                required>

                            <small class="text-muted">
                                Upload ảnh bill chuyển khoản để xác nhận hoàn tiền
                            </small>
                        </div>

                        <div class="alert alert-danger mt-4" style="border-radius: 6px;">
                            <i class="glyphicon glyphicon-exclamation-sign"></i>
                            <strong>Hành động này không thể hoàn tác!</strong><br>
                            <small>Hãy chắc chắn bạn đã chuyển khoản đúng số tiền và đúng thông tin cho khách hàng.</small>
                        </div>

                    </div>

                    <div class="modal-footer"
                        style="background: #f8f9fa; border-top: 1px solid #ddd; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">


                        <button type="submit" class="btn btn-success"
                            onclick="return confirm('XÁC NHẬN ĐÃ HOÀN TIỀN?\n\nSố tiền: {{ number_format($refund->so_tien_yeu_cau ?? 0) }} VND')">
                            <i class="glyphicon glyphicon-ok"></i>
                            Xác nhận đã hoàn tiền
                        </button>
    </form>
    </div>

    </div>
    </div>
    </div>
@endsection
