<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-light py-3 px-4">
        <h5 class="mb-0 fw-semibold">Sản phẩm ({{ $donHang->chiTietDonHangs->count() }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th class="text-center">SL</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donHang->chiTietDonHangs as $chiTiet)
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $chiTiet->sanPham->hinh_anh_chinh ? asset('storage/' . $chiTiet->sanPham->hinh_anh_chinh) : 'https://via.placeholder.com/60' }}"
                                        alt="" class="rounded me-3" width="60" height="60"
                                        style="object-fit: cover;">
                                    <div class="fw-medium">{{ $chiTiet->sanPham->ten_san_pham }}</div>
                                </div>
                            </td>
                            <td class="py-3">
                                @if ($chiTiet->bienThe)
                                    <span class="badge bg-light border text-dark px-2 py-1">
                                        {{ $chiTiet->bienThe->color->ten_mau ?? '—' }} /
                                        {{ $chiTiet->bienThe->size->ten_kich_thuoc ?? '—' }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center py-3">{{ $chiTiet->so_luong }}</td>
                            <td class="text-end py-3">{{ number_format($chiTiet->don_gia, 0, ',', '.') }} ₫</td>
                            <td class="text-end py-3 fw-medium text-primary">
                                {{ number_format($chiTiet->thanh_tien, 0, ',', '.') }} ₫
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
