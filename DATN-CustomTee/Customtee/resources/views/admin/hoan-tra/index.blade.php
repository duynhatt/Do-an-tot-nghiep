@extends('admin.layout.AdminLayout')

@section('AdminContent')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <div class="container-fluid px-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Yêu cầu hoàn trả / hoàn tiền</h1>
                <div class="text-muted small">
                    Tổng cộng: <strong class="text-primary">{{ $refunds->total() }}</strong> yêu cầu
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body pb-2">
                <form method="GET" action="{{ route('admin.hoan-tra.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <select name="trang_thai" class="form-select form-select-sm border-primary focus-ring-primary">
                            <option value="">Tất cả trạng thái</option>
                            <option value="cho_xu_ly" {{ request('trang_thai') == 'cho_xu_ly' ? 'selected' : '' }}>Chờ xử lý
                            </option>
                            <option value="da_chap_nhan" {{ request('trang_thai') == 'da_chap_nhan' ? 'selected' : '' }}>Đã
                                chấp nhận</option>
                            <option value="da_tu_choi" {{ request('trang_thai') == 'da_tu_choi' ? 'selected' : '' }}>Đã từ
                                chối</option>
                            <option value="da_hoan_tien" {{ request('trang_thai') == 'da_hoan_tien' ? 'selected' : '' }}>Đã
                                hoàn tiền</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-lg-2">
                        <button type="submit"
                            class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                    </div>

                    @if (request('trang_thai'))
                        <div class="col-auto">
                            <a href="{{ route('admin.hoan-tra.index') }}"
                                class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle"></i> Xóa lọc
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small text-nowrap">
                        <thead class="bg-light-subtle table-borderless">
                            <tr>
                                <th class="ps-4 py-3 fw-semibold text-uppercase small">ID</th>
                                <th class="py-3 fw-semibold text-uppercase small">Mã đơn hàng</th>
                                <th class="py-3 fw-semibold text-uppercase small">Khách hàng</th>
                                <th class="py-3 fw-semibold text-uppercase small text-end">Số tiền</th>
                                <th class="py-3 fw-semibold text-uppercase small">Trạng thái</th>
                                <th class="py-3 fw-semibold text-uppercase small">Ngày gửi</th>
                                <th class="py-3 fw-semibold text-uppercase small text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($refunds as $refund)
                                <tr class="hover-bg-light">
                                    <td class="ps-4 fw-medium">{{ $refund->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.don-hang.show', $refund->donHang->id ?? null) }}"
                                            class="text-primary text-decoration-none fw-medium hover-underline">
                                            #{{ $refund->donHang->ma_don_hang ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-circle fs-5 text-secondary"></i>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="fw-medium">{{ $refund->user->name ?? 'Khách vãng lai' }}</span>
                                                <small class="text-muted">{{ $refund->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($refund->so_tien_yeu_cau, 0, ',', '.') }} ₫
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'cho_xu_ly' => 'bg-warning text-dark',
                                                'da_chap_nhan' => 'bg-success text-white',
                                                'da_tu_choi' => 'bg-danger text-white',
                                                'da_hoan_tien' => 'bg-info text-white',
                                            ];
                                        @endphp
                                        <span
                                            class="badge rounded-pill px-3 py-2 fs-6 fw-medium {{ $statusClasses[$refund->trang_thai] ?? 'bg-secondary' }}">
                                            {{ $refund->trang_thai_text ?? ucfirst(str_replace('_', ' ', $refund->trang_thai)) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        {{ $refund->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.hoan-tra.show', $refund->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 hover-bg-primary hover-text-white transition-all"
                                            title="Xem chi tiết">
                                            <i class="bi bi-eye fs-6"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <i class="bi bi-inbox fs-1 text-secondary"></i>
                                            <span class="fst-italic">Chưa có yêu cầu hoàn trả nào.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($refunds->hasPages())
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-flex justify-content-center">
                        {{ $refunds->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: rgba(13, 110, 253, 0.04) !important;
            transition: background-color 0.15s ease;
        }

        .hover-underline:hover {
            text-decoration: underline !important;
        }

        .hover-bg-primary:hover {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
        }

        .focus-ring-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .badge.rounded-pill {
            min-width: 130px;
            text-align: center;
            font-weight: 500;
        }

        .table th {
            letter-spacing: 0.4px;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
@endsection
