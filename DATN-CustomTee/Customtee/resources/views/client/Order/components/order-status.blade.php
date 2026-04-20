<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-gradient py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Trạng thái đơn hàng</h5>
    </div>
    <div class="card-body p-4">

        @php
            $statusMap = [
                'cho_xac_nhan' => ['Chờ xác nhận', 'warning', 'bi bi-hourglass-split', 'Đang chờ xác nhận từ cửa hàng'],
                'dang_xu_ly' => ['Đang xử lý', 'info', 'bi bi-gear', 'Đang chuẩn bị và đóng gói'],
                'dang_yeu_cau_huy' => [
                    'Đang yêu cầu hủy',
                    'warning',
                    'bi bi-hourglass-split',
                    'Đơn hàng đang chờ admin duyệt yêu cầu hủy.',
                ],
                'cho_duyet_huy' => ['Chờ duyệt hủy', 'warning', 'bi bi-hourglass-split', 'Đang chờ admin duyệt yêu cầu hủy'],
                'dang_giao' => ['Đang giao', 'primary', 'bi bi-truck', 'Đơn hàng đang được vận chuyển'],
                'da_giao' => [
                    'Đã giao',
                    'success',
                    'bi bi-check-circle-fill',
                    'Đơn hàng đã được giao thành công.',
                ],
                'da_nhan_hang' => [
                    'Đã nhận hàng',
                    'success',
                    'bi bi-box-seam',
                    '',
                ],
                'da_hoan_thanh' => [
                    'Đã hoàn thành',
                    'success',
                    'bi bi-check2-all',
                    'Đơn hàng đã được bạn xác nhận hoàn thành.',
                ],
                'da_huy' => ['Đã hủy', 'danger', 'bi bi-x-circle-fill', 'Đơn hàng đã bị hủy'],
            ];

            $effectiveTrangThai = $donHang->trang_thai;
            if (
                (bool) $donHang->yeu_cau_huy
                && in_array($donHang->trang_thai, ['dang_xu_ly', 'cho_duyet_huy'], true)
            ) {
                $effectiveTrangThai = 'dang_yeu_cau_huy';
            }
            if ($effectiveTrangThai === 'da_giao' && !empty($donHang->da_nhan_hang_at)) {
                $effectiveTrangThai = 'da_nhan_hang';
            }

            $current = $statusMap[$effectiveTrangThai] ?? [
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

            @if (in_array($effectiveTrangThai, ['da_giao', 'da_nhan_hang', 'da_hoan_thanh']) && $donHang->da_giao_at)
                <div class="alert alert-light border small mb-4">
                    <strong>Thời gian đã giao:</strong> {{ $donHang->da_giao_at->format('d/m/Y H:i') }}
                </div>
            @endif
            @if (in_array($effectiveTrangThai, ['da_nhan_hang', 'da_hoan_thanh']) && $donHang->da_nhan_hang_at)
                <div class="alert alert-light border small mb-4">
                    <strong>Thời gian đã nhận hàng:</strong> {{ $donHang->da_nhan_hang_at->format('d/m/Y H:i') }}
                </div>
            @endif

            <div class="d-flex justify-content-between position-relative mt-4 timeline-compact">
                <div
                    class="timeline-step {{ in_array($effectiveTrangThai, ['cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'da_giao', 'da_nhan_hang', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-check-circle"></i></div>
                    <small>Xác nhận</small>
                </div>
                <div
                    class="timeline-step {{ in_array($effectiveTrangThai, ['dang_xu_ly', 'cho_duyet_huy', 'dang_giao', 'da_giao', 'da_nhan_hang', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-gear"></i></div>
                    <small>Xử lý</small>
                </div>
                <div
                    class="timeline-step {{ in_array($effectiveTrangThai, ['dang_giao', 'da_giao', 'da_nhan_hang', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-truck"></i></div>
                    <small>Giao hàng</small>
                </div>
                <div class="timeline-step {{ in_array($effectiveTrangThai, ['da_nhan_hang', 'da_hoan_thanh']) ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-box-seam"></i></div>
                    <small>Đã nhận hàng</small>
                </div>
                <div class="timeline-step {{ $effectiveTrangThai === 'da_hoan_thanh' ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-check2-all"></i></div>
                    <small>Hoàn tất</small>
                </div>
            </div>

            @if (
                $donHang->ly_do_tu_choi_huy &&
                in_array($donHang->trang_thai, ['dang_xu_ly', 'dang_giao'], true)
            )
                <div class="alert alert-danger border small mb-4">
                    <strong>Lý do từ chối hủy:</strong> {{ $donHang->ly_do_tu_choi_huy }}
                </div>
            @endif

            @if ($donHang->trang_thai === 'da_huy' && $donHang->ly_do_huy_boi_admin)
                <div class="alert alert-warning border small mb-4">
                    <strong>Lý do hủy từ cửa hàng:</strong> {{ $donHang->ly_do_huy_boi_admin }}
                </div>
            @endif

            @if (
                $donHang->phuong_thuc_thanh_toan === 'cod' &&
                    in_array($donHang->trang_thai, ['dang_xu_ly', 'cho_duyet_huy'], true) &&
                    !(bool) $donHang->yeu_cau_huy &&
                    !empty($donHang->ly_do_yeu_cau_huy))
                <div class="alert alert-info border small mb-4">
                    <strong>Lý do hủy bạn đã gửi:</strong> {{ $donHang->ly_do_yeu_cau_huy }}
                </div>
            @endif
        @endif

        {{-- ==================== PHẦN YÊU CẦU HOÀN TIỀN ==================== --}}
        @php
            $yeuCauHoanTien = $donHang->refunds()->latest()->first();

            $refundStatusMap = [
                'cho_xu_ly' => [
                    'Chờ xác nhận',
                    'warning',
                    'bi bi-hourglass',
                    'Hoàn tiền/trả hàng đang chờ cửa hàng xác nhận.',
                ],
                'da_chap_nhan' => [
                    'Đã chấp nhận',
                    'success',
                    'bi bi-arrow-repeat',
                    'Đang kiểm tra và chuẩn bị hoàn tiền.',
                ],
                'da_tu_choi' => ['Đã từ chối', 'danger', 'bi bi-x-circle', 'Hoàn tiền/trả hàng đã bị từ chối.'],
                'da_hoan_tien' => [
                    'Đã hoàn tiền',
                    'primary',
                    'bi bi-check-circle',
                    'Hoàn tiền thành công.',
                ],
            ];

            $currentRefund = $yeuCauHoanTien
                ? $refundStatusMap[$yeuCauHoanTien->trang_thai] ?? [
                        'Không xác định',
                        'secondary',
                        'bi bi-question-circle',
                        '',
                    ]
                : null;

            $refundAttempts = $yeuCauHoanTien ? (int) ($yeuCauHoanTien->so_lan_yeu_cau ?? 1) : 0;
            $canCreateNewRefund =
                !$yeuCauHoanTien
                || ($yeuCauHoanTien->trang_thai === 'da_tu_choi' && $refundAttempts < 2);

            $isOnlineCancelRefund =
                $donHang->phuong_thuc_thanh_toan === 'vnpay'
                && $donHang->trang_thai === 'da_huy'
                && $donHang->trang_thai_thanh_toan === 'da_thanh_toan';
            $isDeliveredReturn =
                in_array($donHang->phuong_thuc_thanh_toan, ['cod', 'vnpay'], true)
                && $donHang->trang_thai === 'da_giao'
                && !empty($donHang->da_giao_at)
                && !empty($donHang->da_nhan_hang_at);

            // Kiểm tra thời hạn hoàn tiền (3 ngày) chỉ áp dụng cho case đã giao.
            $mocDaGiao = $donHang->da_giao_at;
            $conTrongThoiHanDaGiao =
                $isDeliveredReturn
                && $mocDaGiao
                && \Carbon\Carbon::parse($mocDaGiao)->addDays(3)->isFuture();

            $showRefundButton = ($isOnlineCancelRefund || $isDeliveredReturn) && $canCreateNewRefund;
            $coTheGuiYeuCauNgay = $isOnlineCancelRefund || $conTrongThoiHanDaGiao;
            $refundButtonLabel = $donHang->trang_thai === 'da_giao' ? 'Hoàn tiền/Trả hàng' : 'Hoàn tiền';
        @endphp

        @if (!empty($donHang->da_giao_at))
            <div class="alert alert-light border small mt-4 mb-0">
                <strong>Thời gian đã giao:</strong> {{ $donHang->da_giao_at->format('d/m/Y H:i') }}
            </div>
        @endif

        @if ($yeuCauHoanTien && $currentRefund)
            {{-- Luôn hiển thị thông tin hoàn tiền/trả hàng nếu đã có yêu cầu --}}
            <div class="mt-4 card border-{{ $currentRefund[1] }} shadow-sm">
                <div class="card-header bg-{{ $currentRefund[1] }} text-white fw-semibold">
                    <i class="{{ $currentRefund[2] }} me-2"></i> Hoàn tiền/trả hàng
                </div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Trạng thái:</span>
                        <span class="badge bg-{{ $currentRefund[1] }}">{{ $currentRefund[0] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số tiền:</span>
                        <strong>{{ number_format($yeuCauHoanTien->so_tien_yeu_cau ?? 0, 0, ',', '.') }}
                            ₫</strong>
                    </div>
                    @if ($yeuCauHoanTien->created_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Thời gian gửi:</span>
                            <strong>{{ $yeuCauHoanTien->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    @endif
                    @if ($yeuCauHoanTien->ly_do)
                        <div class="mt-2">
                            <span class="text-muted">Lý do:</span><br>
                            <small>{{ $yeuCauHoanTien->ly_do }}</small>
                        </div>
                    @endif
                    @if ($yeuCauHoanTien->trang_thai === 'da_hoan_tien' && $yeuCauHoanTien->hinh_anh_xac_nhan)
                        <div class="mt-3">
                            <button type="button" class="btn btn-link p-0 text-decoration-none"
                                data-bs-toggle="modal" data-bs-target="#refundProofModal{{ $yeuCauHoanTien->id }}">
                                {{ $currentRefund[3] }}
                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                            </button>
                        </div>

                        <div class="modal fade" id="refundProofModal{{ $yeuCauHoanTien->id }}" tabindex="-1"
                            aria-labelledby="refundProofModalLabel{{ $yeuCauHoanTien->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="refundProofModalLabel{{ $yeuCauHoanTien->id }}">
                                            Ảnh minh chứng hoàn tiền
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Đóng"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <a href="{{ asset('storage/' . $yeuCauHoanTien->hinh_anh_xac_nhan) }}"
                                            target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/' . $yeuCauHoanTien->hinh_anh_xac_nhan) }}"
                                                alt="Ảnh minh chứng hoàn tiền" class="img-fluid rounded shadow-sm">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mt-3 mb-0">{{ $currentRefund[3] }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if ($showRefundButton)
            @if ($coTheGuiYeuCauNgay)
                @if (!$yeuCauHoanTien || $canCreateNewRefund)
                    <div class="mt-4">
                        <button type="button"
                            class="btn btn-warning w-100 d-flex align-items-center justify-content-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalYeuCauHoanTra"
                            aria-label="{{ $refundButtonLabel }}">
                            <i class="bi bi-arrow-return-left"></i>
                            {{ $refundButtonLabel }}
                        </button>

                        <p class="text-small text-muted text-center mt-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            {{ $donHang->trang_thai === 'da_huy'
                                ? 'Đơn đã hủy: hoàn theo tổng tiền bạn đã thanh toán.'
                                : 'Đơn đã nhận hàng: không hoàn phí vận chuyển, tiền hoàn dựa trên giá trị hàng hóa thực trả.' }}
                        </p>
                    </div>
                @endif
            @else
                <div class="alert alert-info mt-4 text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Hết thời gian hoàn tiền/trả hàng (quá 3 ngày kể từ khi đã giao)
                </div>
            @endif
        @endif

        @if ($yeuCauHoanTien && $yeuCauHoanTien->trang_thai === 'da_tu_choi' && $refundAttempts >= 2)
            <div class="alert alert-secondary mt-4 mb-0 text-center">
                Bạn đã dùng hết 2 lần yêu cầu hoàn tiền cho đơn hàng này.
            </div>
        @endif

        {{-- Nút hủy đơn --}}
        @php
            $isPendingCancelRequest = (bool) $donHang->yeu_cau_huy
                && in_array($donHang->trang_thai, ['dang_xu_ly', 'cho_duyet_huy'], true);
            $cancelRequestAttempts = (int) ($donHang->so_lan_yeu_cau_huy ?? 0);
            $canRequestCancelInProcessing = $donHang->trang_thai === 'dang_xu_ly'
                && !$isPendingCancelRequest
                && $cancelRequestAttempts < 2;
        @endphp

        @if ($isPendingCancelRequest)
            <div class="mt-4 alert alert-warning mb-0">
                <i class="bi bi-hourglass-split me-2"></i> Đơn đang chờ admin duyệt hủy.
                @if ($donHang->ly_do_yeu_cau_huy)
                    <div class="small mt-2 mb-0">
                        <strong>Lý do bạn đã gửi:</strong> {{ $donHang->ly_do_yeu_cau_huy }}
                    </div>
                @endif
            </div>
        @elseif ($donHang->trang_thai === 'cho_xac_nhan')
            <div class="mt-4">
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                    data-bs-target="#modalHuyDon">
                    <i class="bi bi-x-circle me-2"></i> Hủy đơn
                </button>
            </div>
        @elseif ($canRequestCancelInProcessing)
            <div class="mt-4">
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                    data-bs-target="#modalHuyDon">
                    <i class="bi bi-x-circle me-2"></i>
                    {{ $donHang->phuong_thuc_thanh_toan === 'vnpay' ? 'Gửi yêu cầu hủy' : 'Hủy đơn' }}
                </button>
            </div>
        @endif

        @if (
            $donHang->trang_thai === 'cho_xac_nhan' ||
                ($donHang->trang_thai === 'dang_xu_ly' &&
                    !$isPendingCancelRequest &&
                    $cancelRequestAttempts < 2))
            @php
                $laYeuCauHuyOnline =
                    $donHang->phuong_thuc_thanh_toan === 'vnpay' && $donHang->trang_thai === 'dang_xu_ly';
                $boQuaLyDoHuy =
                    ($donHang->phuong_thuc_thanh_toan === 'vnpay'
                        && $donHang->trang_thai === 'cho_xac_nhan')
                    || ($donHang->phuong_thuc_thanh_toan === 'cod'
                        && $donHang->trang_thai === 'cho_xac_nhan');
            @endphp
            <div class="modal fade" id="modalHuyDon" tabindex="-1" aria-labelledby="modalHuyDonLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('order.cancel', $donHang->id) }}" method="post">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalHuyDonLabel">Hủy đơn hàng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                @if ($laYeuCauHuyOnline)
                                    <p class="text-muted small mb-3">
                                        Bạn đang gửi <strong>yêu cầu hủy</strong>. Admin sẽ xem xét trước khi đơn được
                                        hủy.
                                    </p>
                                @else
                                    <p class="text-muted small mb-3">
                                        Sau khi xác nhận, đơn hàng sẽ bị hủy theo chính sách của cửa hàng.
                                    </p>
                                @endif
                                @if ($boQuaLyDoHuy)
                                @else
                                    <label for="ly_do_yeu_cau_huy" class="form-label fw-semibold">Lý do hủy <span
                                            class="text-danger">*</span></label>
                                    <textarea name="ly_do_yeu_cau_huy" id="ly_do_yeu_cau_huy" class="form-control"
                                        rows="4" required maxlength="2000" placeholder="Nhập lý do hủy đơn...">{{ old('ly_do_yeu_cau_huy') }}</textarea>
                                    @error('ly_do_yeu_cau_huy')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-danger">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if ($donHang->trang_thai === 'dang_xu_ly' && !$isPendingCancelRequest && $cancelRequestAttempts >= 2)
            <div class="alert alert-secondary mt-4 mb-0">
                Bạn đã dùng hết 2 lần yêu cầu hủy cho đơn hàng này.
            </div>
        @endif

        @if ($errors->has('ly_do_yeu_cau_huy'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var el = document.getElementById('modalHuyDon');
                    if (el && window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(el).show();
                    }
                });
            </script>
        @endif

    </div>
</div>
