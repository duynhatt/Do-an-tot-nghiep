@include('client.layout.header')

<nav aria-label="breadcrumb" class="my-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
        <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
    </ol>
</nav>

<div class="container my-5">
    <h4 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng của bạn</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-cart-plus fa-4x text-muted mb-3"></i>
                <p class="text-muted mb-4">Giỏ hàng trống.</p>
                <a href="{{ url('/Shop') }}" class="btn btn-success">Tiếp tục mua sắm</a>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center" style="width: 140px">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                        <th style="width: 80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr data-item-id="{{ $item->id }}">
                            <td>
                                <img src="{{ asset('storage/' . $item->sanPham->hinh_anh_chinh) }}"
                                    alt="{{ $item->sanPham->ten_san_pham }}"
                                    class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                            </td>
                            <td>
                                <a href="{{ route('sanpham.chitiet', $item->sanPham->slug) }}" class="text-decoration-none text-dark fw-semibold">
                                    {{ $item->sanPham->ten_san_pham }}
                                </a>
                            </td>
                            <td>
                                @if($item->bienThe)
                                    <span class="badge bg-secondary">{{ $item->bienThe->color->ten_mau ?? '—' }} / {{ $item->bienThe->size->ten_kich_thuoc ?? '—' }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($item->don_gia) }} ₫</td>
                            <td>
                                <form class="d-inline-flex align-items-center justify-content-center gap-1 form-update-qty" data-item-id="{{ $item->id }}" data-max="{{ $item->bienThe ? $item->bienThe->so_luong : 0 }}">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="input-group input-group-sm" style="width: 120px;">
                                        <button type="button" class="btn btn-outline-secondary btn-qty-minus">−</button>
                                        <input type="number" name="so_luong" class="form-control text-center qty-input" min="1" value="{{ $item->so_luong }}" max="{{ $item->bienThe ? $item->bienThe->so_luong : 1 }}">
                                        <button type="button" class="btn btn-outline-secondary btn-qty-plus">+</button>
                                    </div>
                                </form>
                            </td>
                            <td class="text-end fw-bold text-danger thanh-tien-cell">{{ number_format($item->thanh_tien) }} ₫</td>
                            <td>
                                <form action="{{ route('gio-hang.destroy', $item) }}" method="POST" class="d-inline form-remove-item" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <strong class="text-danger" id="tong-tien">{{ number_format($tongTien) }} ₫</strong>
                        </div>
                        <p class="small text-muted mb-0">Chưa bao gồm phí vận chuyển. Không xử lý thanh toán tại bước này.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2 justify-content-between flex-wrap">
            <a href="{{ url('/Shop') }}" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>

@include('client.layout.footer')
@include('client.layout.scripts')

@if(!$items->isEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateQtyForms = document.querySelectorAll('.form-update-qty');
    const tongTienEl = document.getElementById('tong-tien');

    function formatMoney(n) {
        return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
    }

    function refreshTongTien() {
        let tong = 0;
        document.querySelectorAll('.thanh-tien-cell').forEach(function(cell) {
            const t = cell.getAttribute('data-value');
            if (t) tong += parseInt(t, 10);
        });
        if (tongTienEl) tongTienEl.textContent = formatMoney(tong);
    }

    document.querySelectorAll('.thanh-tien-cell').forEach(function(cell) {
        const text = cell.textContent.replace(/\D/g, '');
        if (text) cell.setAttribute('data-value', text);
    });
    refreshTongTien();

    updateQtyForms.forEach(function(form) {
        const itemId = form.dataset.itemId;
        const max = parseInt(form.dataset.max, 10) || 9999;
        const input = form.querySelector('.qty-input');
        const row = form.closest('tr');
        const thanhTienCell = row ? row.querySelector('.thanh-tien-cell') : null;
        const donGia = row ? (function() {
            const prevCell = row.cells[3];
            return prevCell ? parseInt(prevCell.textContent.replace(/\D/g, ''), 10) : 0;
        })() : 0;

        function submitQty() {
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }

        form.querySelector('.btn-qty-minus').addEventListener('click', function() {
            let v = parseInt(input.value, 10) || 1;
            if (v > 1) { input.value = v - 1; submitQty(); }
        });
        form.querySelector('.btn-qty-plus').addEventListener('click', function() {
            let v = parseInt(input.value, 10) || 1;
            if (v < max) { input.value = v + 1; submitQty(); }
        });
        input.addEventListener('change', function() {
            let v = parseInt(input.value, 10) || 1;
            input.value = Math.min(max, Math.max(1, v));
            submitQty();
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const qty = parseInt(input.value, 10);
            if (qty < 1 || qty > max) {
                alert('Số lượng phải từ 1 đến ' + max);
                return;
            }
            const url = '{{ url("/gio-hang") }}/' + itemId;
            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ so_luong: qty })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.thanh_tien !== undefined && thanhTienCell) {
                    thanhTienCell.setAttribute('data-value', data.thanh_tien);
                    thanhTienCell.textContent = formatMoney(data.thanh_tien);
                    refreshTongTien();
                }
            })
            .catch(function() { form.submit(); });
        });
    });

    document.querySelectorAll('.form-remove-item').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ?')) e.preventDefault();
        });
    });
});
</script>
@endif
