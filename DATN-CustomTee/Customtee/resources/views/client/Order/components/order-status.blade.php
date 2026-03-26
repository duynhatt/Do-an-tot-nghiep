<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-gradient py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Trạng thái đơn hàng</h5>
    </div>
    <div class="card-body p-4">
        @php
            $statusMap = [
                'cho_xac_nhan' => ['Chờ xác nhận', 'warning', 'bi bi-hourglass-split', 'Đang chờ xác nhận từ cửa hàng'],
                'dang_xu_ly' => ['Đang xử lý', 'info', 'bi bi-gear', 'Đang chuẩn bị và đóng gói'],
                'dang_giao' => ['Đang giao', 'primary', 'bi bi-truck', 'Đơn hàng đang được vận chuyển'],
                'da_giao' => [
                    'Đã giao',
                    'success',
                    'bi bi-check-circle-fill',
                    'Giao hàng thành công. Vui lòng kiểm tra và xác nhận nếu bạn đã nhận đủ hàng.',
                ],
                'da_hoan_thanh' => [
                    'Đã hoàn thành',
                    'success',
                    'bi bi-check2-all',
                    'Đơn hàng đã được bạn xác nhận hoàn thành.',
                ],
                'da_huy' => ['Đã hủy', 'danger', 'bi bi-x-circle-fill', 'Đơn hàng đã bị hủy'],
            ];

            $current = $statusMap[$donHang->trang_thai] ?? [
                'Khác',
                'secondary',
                'bi bi-question-circle',
                'Trạng thái không xác định',
            ];
        @endphp

        @if ($donHang->yeu_cau_tra == 0)
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-{{ $current[1] }} fs-5 px-4 py-2 d-flex align-items-center">
                    <i class="{{ $current[2] }} me-2 fs-4"></i> {{ $current[0] }}
                </span>
            </div>

            <p class="text-muted mb-4">{{ $current[3] }}</p>
            @if (in_array($donHang->trang_thai, ['da_giao', 'da_hoan_thanh']) && $donHang->da_giao_at)
                <div class="alert alert-light border small mb-4">
                    <strong>Thời gian đã giao:</strong> {{ $donHang->da_giao_at->format('d/m/Y H:i') }}
                </div>
            @endif
            <div class="d-flex justify-content-between position-relative mt-4 timeline-compact">
                <div
                    class="timeline-step {{ in_array($donHang->trang_thai, ['cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'da_giao', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-check-circle"></i></div>
                    <small>Xác nhận</small>
                </div>
                <div
                    class="timeline-step {{ in_array($donHang->trang_thai, ['dang_xu_ly', 'dang_giao', 'da_giao', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-gear"></i></div>
                    <small>Xử lý</small>
                </div>
                <div
                    class="timeline-step {{ in_array($donHang->trang_thai, ['dang_giao', 'da_giao', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-truck"></i></div>
                    <small>Giao hàng</small>
                </div>
                <div class="timeline-step {{ $donHang->trang_thai === 'da_hoan_thanh' ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-check2-all"></i></div>
                    <small>Hoàn tất</small>
                </div>
            </div>
        @endif


        @php
            $showRefundButton =
                ($donHang->phuong_thuc_thanh_toan === 'vnpay' &&
                    !in_array($donHang->trang_thai, ['da_hoan_thanh', 'cho_xac_nhan', 'dang_giao', 'da_huy'])) ||
                ($donHang->phuong_thuc_thanh_toan === 'cod' && $donHang->trang_thai === 'da_giao');

            $yeuCauHoanTien = $donHang->refunds()->latest()->first();

            $refundStatusMap = [
                'cho_xu_ly' => [
                    'Chờ xác nhận',
                    'warning',
                    'glyphicon glyphicon-hourglass',
                    'Yêu cầu hoàn tiền đang chờ cửa hàng xác nhận.',
                ],
                'da_chap_nhan' => [
                    'Đã chấp nhận',
                    'success',
                    'glyphicon glyphicon-refresh',
                    'Đang kiểm tra và chuẩn bị hoàn tiền.',
                ],
                'da_tu_choi' => [
                    'Đã từ chối',
                    'danger',
                    'glyphicon glyphicon-remove-circle',
                    'Yêu cầu hoàn tiền đã bị từ chối.',
                ],
                'da_hoan_tien' => [
                    'Đã hoàn tiền',
                    'primary',
                    'glyphicon glyphicon-ok-circle',
                    'Hoàn tiền thành công cho khách hàng.',
                ],
            ];

            $currentRefund = $yeuCauHoanTien
                ? $refundStatusMap[$yeuCauHoanTien->trang_thai] ?? [
                        'Không xác định',
                        'secondary',
                        'glyphicon glyphicon-question-sign',
                        '',
                    ]
                : null;
            $canCreateNewRefundRequest = !$yeuCauHoanTien || $yeuCauHoanTien->trang_thai === 'da_tu_choi';
        @endphp

        @if ($showRefundButton)

            @php
                $hoanThanhTime = $donHang->da_hoan_thanh_at ?? $donHang->updated_at;
                $conTrongThoiHan = $hoanThanhTime && \Carbon\Carbon::parse($hoanThanhTime)->addDays(3)->isFuture();
            @endphp

            @if ($conTrongThoiHan)

                @if (!$canCreateNewRefundRequest && $yeuCauHoanTien && $currentRefund)
                    <div class="mt-4 card border-{{ $currentRefund[1] }} shadow-sm">
                        <div class="card-header bg-{{ $currentRefund[1] }} text-white fw-semibold">
                            <i class="{{ $currentRefund[2] }} me-2"></i>
                            Yêu cầu hoàn tiền
                        </div>
                        <div class="card-body small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Trạng thái:</span>
                                <span class="badge bg-{{ $currentRefund[1] }}">
                                    {{ $currentRefund[0] }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Số tiền:</span>
                                <strong>{{ number_format($yeuCauHoanTien->so_tien_yeu_cau ?? 0, 0, ',', '.') }}
                                    ₫</strong>
                            </div>
                            @if ($yeuCauHoanTien->ly_do)
                                <div class="mt-2">
                                    <span class="text-muted">Lý do:</span><br>
                                    <small>{{ $yeuCauHoanTien->ly_do }}</small>
                                </div>
                            @endif
                            <p class="text-muted mt-3 mb-0">{{ $currentRefund[3] }}</p>
                        </div>
                    </div>
                @else
                    <div class="mt-4">
                        <button type="button" class="btn btn-warning btn-block" data-bs-toggle="modal"
                            data-bs-target="#modalYeuCauHoanTra">
                            <i class="glyphicon glyphicon-arrow-left"></i>
                            Yêu cầu hoàn tiền
                        </button>
                    </div>
                @endif
            @else
                <div class="alert alert-info mt-4 text-center">
                    <i class="glyphicon glyphicon-info-sign"></i>
                    Hết thời gian yêu cầu hoàn tiền (quá 3 ngày kể từ khi hoàn thành)
                </div>
            @endif

        @endif

        @if ($donHang->trang_thai === 'cho_xac_nhan')
            <div class="mt-4">
                <form action="{{ route('order.cancel', $donHang->id) }}" method="post">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="glyphicon glyphicon-remove"></i> Hủy đơn
                    </button>
                </form>
            </div>
        @endif

    </div>
</div>
