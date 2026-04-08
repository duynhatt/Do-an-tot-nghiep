<div class="modal fade" id="modalYeuCauHoanTra" tabindex="-1" aria-labelledby="modalYeuCauHoanTraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="modalYeuCauHoanTraLabel">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Hoàn tiền
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('order.return.request', $donHang->id) }}" method="POST"
                enctype="multipart/form-data" id="formYeuCauHoanTra">
                @csrf

                <div class="modal-body">
                    @php
                        $isOnlineCancelRefund = $donHang->phuong_thuc_thanh_toan === 'vnpay'
                            && $donHang->trang_thai === 'da_huy'
                            && $donHang->trang_thai_thanh_toan === 'da_thanh_toan';
                        $isDeliveredReturn = $donHang->trang_thai === 'da_giao';
                        $requiresFullReturn = $isOnlineCancelRefund || $isDeliveredReturn;
                    @endphp

                    @if (!$requiresFullReturn)
                        <div class="mb-5">
                            <h6 class="fw-semibold mb-3">Chọn sản phẩm và số lượng muốn hoàn trả</h6>
                            <div class="list-group">
                                @foreach ($donHang->chiTietDonHangs as $chiTiet)
                                    <div
                                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $chiTiet->sanPham->hinh_anh_chinh ? asset('storage/' . $chiTiet->sanPham->hinh_anh_chinh) : 'https://via.placeholder.com/50' }}"
                                                alt="" class="rounded me-3" width="50" height="50"
                                                style="object-fit: cover;">
                                            <div>
                                                <div class="fw-medium">{{ $chiTiet->sanPham->ten_san_pham }}</div>
                                                @if ($chiTiet->bienThe)
                                                    <small class="text-muted">
                                                        {{ $chiTiet->bienThe->color->ten_mau ?? '—' }} /
                                                        {{ $chiTiet->bienThe->size->ten_kich_thuoc ?? '—' }}
                                                    </small>
                                                @endif
                                                <div class="small text-muted mt-1">
                                                    SL đã mua: <strong>{{ $chiTiet->so_luong }}</strong> ×
                                                    {{ number_format($chiTiet->don_gia, 0, ',', '.') }} ₫
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chi_tiet_ids[]"
                                                    value="{{ $chiTiet->id }}" id="chiTiet{{ $chiTiet->id }}"
                                                    data-max="{{ $chiTiet->so_luong }}">
                                                <label class="form-check-label" for="chiTiet{{ $chiTiet->id }}">Hoàn
                                                    trả</label>
                                            </div>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <span class="input-group-text">SL</span>
                                                <input type="number" name="so_luong[{{ $chiTiet->id }}]"
                                                    class="form-control so-luong-input" min="1"
                                                    max="{{ $chiTiet->so_luong }}" value="1" disabled>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted mt-2 d-block">
                                Chọn sản phẩm và điều chỉnh số lượng muốn hoàn trả (tối thiểu 1, tối đa bằng số đã mua).
                            </small>
                        </div>
                    @elseif ($isDeliveredReturn)
                        <div class="mb-5">
                            <h6 class="fw-semibold mb-3">Sản phẩm sẽ được hoàn trả toàn bộ</h6>
                            <div class="list-group">
                                @foreach ($donHang->chiTietDonHangs as $chiTiet)
                                    <div
                                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $chiTiet->sanPham->hinh_anh_chinh ? asset('storage/' . $chiTiet->sanPham->hinh_anh_chinh) : 'https://via.placeholder.com/50' }}"
                                                alt="" class="rounded me-3" width="50" height="50"
                                                style="object-fit: cover;">
                                            <div>
                                                <div class="fw-medium">{{ $chiTiet->sanPham->ten_san_pham }}</div>
                                                @if ($chiTiet->bienThe)
                                                    <small class="text-muted">
                                                        {{ $chiTiet->bienThe->color->ten_mau ?? '—' }} /
                                                        {{ $chiTiet->bienThe->size->ten_kich_thuoc ?? '—' }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="small text-muted">
                                            Hoàn trả toàn bộ: <strong>{{ $chiTiet->so_luong }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($isOnlineCancelRefund)
                        <div class="mb-4">
                            <input type="hidden" name="ly_do" value="{{ old('ly_do', $donHang->ly_do_yeu_cau_huy ?? '') }}">
                        </div>
                    @else
                        <div class="mb-4">
                            <label for="ly_do" class="form-label fw-semibold">Lý do hoàn trả / khiếu nại</label>
                            <textarea name="ly_do" id="ly_do" class="form-control" rows="4"
                                placeholder="Vui lòng mô tả chi tiết vấn đề (hàng lỗi, không đúng mô tả, hư hỏng khi vận chuyển...)">{{ old('ly_do') }}</textarea>
                            @error('ly_do')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if (!$isOnlineCancelRefund)
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Hình ảnh minh chứng (tối đa 5 ảnh)</label>
                            <input type="file" name="hinh_anh[]" id="hinhAnhInput" class="form-control" accept="image/*"
                                multiple>
                            <small class="form-text text-muted d-block mt-1">Hỗ trợ: jpg, jpeg, png. Tối đa 5 ảnh.</small>
                            <div id="previewContainer" class="mt-3 row g-2"></div>
                            @error('hinh_anh.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-5">
                        <label class="form-label fw-semibold mb-3">Chọn cách cung cấp thông tin tài khoản ngân
                            hàng</label>

                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="refund_method" id="method_upload"
                                value="upload" checked>
                            <label class="btn btn-outline-primary" for="method_upload">Tải lên ảnh thông tin
                                TK</label>

                            <input type="radio" class="btn-check" name="refund_method" id="method_manual"
                                value="manual">
                            <label class="btn btn-outline-primary" for="method_manual">Nhập thông tin thủ
                                công</label>
                        </div>

                        <div id="upload_section">
                            <label class="form-label fw-semibold">Ảnh thông tin tài khoản nhận tiền</label>
                            <input type="file" name="hinh_tai_khoan[]" id="hinhTaiKhoanInput" class="form-control"
                                accept="image/*" multiple>
                            <small class="form-text text-muted d-block mt-1">
                                Tải lên ảnh Qr Code có chứa thông tin ngân hàng nhận tiền của bạn
                            </small>
                            <div id="previewTaiKhoanContainer" class="mt-3 row g-2"></div>
                            @error('hinh_tai_khoan.*')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="manual_section" class="d-none">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tên ngân hàng</label>
                                    <input type="text" name="ngan_hang" class="form-control"
                                        placeholder="Ví dụ: Vietcombank, BIDV, Techcombank..." required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Số tài khoản</label>
                                    <input type="text" name="so_tai_khoan" class="form-control"
                                        placeholder="Nhập số tài khoản ngân hàng" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Chi nhánh</label>
                                    <input type="text" name="chi_nhanh" class="form-control"
                                        placeholder="Ví dụ: Chi nhánh Hà Nội, Chi nhánh TP.HCM">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tên chủ tài khoản</label>
                                    <input type="text" name="ten_chu_tk" class="form-control"
                                        placeholder="Họ và tên chủ tài khoản" required>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning" id="btnSubmitHoanTra">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Gửi hoàn tiền/trả hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
