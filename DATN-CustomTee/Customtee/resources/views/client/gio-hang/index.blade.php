@include('client.layout.header')



<div class="container my-5">
    <nav aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
        </ol>
    </nav>
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
                    <th style="width: 44px" class="text-center">
                        <input type="checkbox" id="select-all" class="form-check-input" title="Chọn tất cả">
                    </th>
                    <th style="width: 200px"></th>
                    <th>Tên</th>
                    <th>màu sắc/kích thước</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-center" style="width: 140px">Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                    <th style="width: 80px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                @php
                $maxStock = $item->bienThe ? $item->bienThe->so_luong : 0;
                $isOutOfStock = $maxStock < 1;
                    $displayQty=$isOutOfStock ? 0 : $item->so_luong;
                    $isChecked = !$isOutOfStock && (!$useSelection || isset($selectedSet[$item->id]));
                    @endphp
                    <tr data-item-id="{{ $item->id }}">
                        <td class="text-center">
                            <input type="checkbox"
                                class="form-check-input cart-item-checkbox"
                                data-item-id="{{ $item->id }}"
                                {{ $isOutOfStock ? 'disabled' : '' }}
                                {{ $isChecked ? 'checked' : '' }}>
                        </td>
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
                            @if($isOutOfStock)
                            <span class="badge bg-danger ms-1">Het hang</span>
                            @endif
                            @else
                            —
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($item->don_gia) }} ₫</td>
                        <td>
                            <form action="{{ route('gio-hang.update', $item) }}" method="POST" class="d-inline-flex align-items-center justify-content-center gap-1 form-update-qty" data-item-id="{{ $item->id }}" data-max="{{ $maxStock }}">
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button type="button" class="btn btn-outline-secondary btn-qty-minus" {{ $isOutOfStock ? 'disabled' : '' }}>−</button>
                                    <input type="number" name="so_luong" class="form-control text-center qty-input" min="{{ $isOutOfStock ? 0 : 1 }}" value="{{ $displayQty }}" max="{{ $maxStock }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                    <button type="button" class="btn btn-outline-secondary btn-qty-plus" {{ $isOutOfStock ? 'disabled' : '' }}>+</button>
                                </div>
                            </form>
                        </td>
                        <td class="text-end fw-bold text-danger thanh-tien-cell">{{ number_format($item->thanh_tien) }} ₫</td>
                        <td>
                            <form action="{{ route('gio-hang.destroy', $item) }}" method="POST" class="d-inline form-remove-item">
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
                    <p class="small text-muted mb-0"> </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2 justify-content-between flex-wrap">
        <a href="{{ url('/Shop') }}" class="btn btn-outline-secondary">Tiếp tục mua sắm</a>
        <button type="button" id="btn-proceed-to-checkout" class="btn btn-primary">
            Tiến hành thanh toán
        </button>
    </div>
    @endif
</div>

@include('client.layout.footer')
@include('client.layout.scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(!$items->isEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateQtyForms = document.querySelectorAll('.form-update-qty');
        const tongTienEl = document.getElementById('tong-tien');
        const selectAllEl = document.getElementById('select-all');

        function formatMoney(n) {
            return new Intl.NumberFormat('vi-VN').format(n) + ' ₫';
        }

        function refreshTongTien() {
            let tong = 0;
            document.querySelectorAll('tr[data-item-id]').forEach(function(row) {
                const checkbox = row.querySelector('.cart-item-checkbox');
                if (!checkbox || checkbox.disabled || !checkbox.checked) return;
                const cell = row.querySelector('.thanh-tien-cell');
                const t = cell ? cell.getAttribute('data-value') : null;
                if (t) tong += parseInt(t, 10);
            });
            if (tongTienEl) tongTienEl.textContent = formatMoney(tong);
        }

        function syncSelectAll() {
            if (!selectAllEl) return;
            const enabledCheckboxes = Array.from(document.querySelectorAll('.cart-item-checkbox'))
                .filter(function(cb) {
                    return !cb.disabled;
                });
            if (enabledCheckboxes.length === 0) {
                selectAllEl.checked = false;
                selectAllEl.indeterminate = false;
                return;
            }
            const checkedCount = enabledCheckboxes.filter(function(cb) {
                return cb.checked;
            }).length;
            selectAllEl.checked = checkedCount === enabledCheckboxes.length;
            selectAllEl.indeterminate = checkedCount > 0 && checkedCount < enabledCheckboxes.length;
        }

        function collectSelectedIds() {
            return Array.from(document.querySelectorAll('.cart-item-checkbox'))
                .filter(function(cb) {
                    return !cb.disabled && cb.checked;
                })
                .map(function(cb) {
                    return parseInt(cb.dataset.itemId, 10);
                })
                .filter(function(id) {
                    return Number.isInteger(id);
                });
        }

        function syncSelection() {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) return;
            fetch('{{ route('gio-hang.selection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        ids: collectSelectedIds()
                    })
                }).catch(function() {
            });
        }

        document.querySelectorAll('.thanh-tien-cell').forEach(function(cell) {
            const text = cell.textContent.replace(/\D/g, '');
            if (text) cell.setAttribute('data-value', text);
        });
        refreshTongTien();
        syncSelectAll();
        syncSelection();

        function showError(message) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    text: message
                });
                return;
            }
            alert(message);
        }

        updateQtyForms.forEach(function(form) {
            const itemId = form.dataset.itemId;
            let max = parseInt(form.dataset.max, 10) || 0;
            const input = form.querySelector('.qty-input');
            const row = form.closest('tr');
            const thanhTienCell = row ? row.querySelector('.thanh-tien-cell') : null;
            const isDisabled = input.hasAttribute('disabled');

            function submitQty() {
                form.dispatchEvent(new Event('submit', {
                    cancelable: true
                }));
            }

            if (isDisabled || max < 1) {
                return;
            }

            form.querySelector('.btn-qty-minus').addEventListener('click', function() {
                let v = parseInt(input.value, 10) || 1;
                if (v > 1) {
                    input.value = v - 1;
                    submitQty();
                }
            });
            form.querySelector('.btn-qty-plus').addEventListener('click', function() {
                let v = parseInt(input.value, 10) || 1;
                if (v < max) {
                    input.value = v + 1;
                    submitQty();
                }
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
                    showError('Số lượng phải từ 1 đến ' + max);
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
                        body: JSON.stringify({
                            so_luong: qty
                        })
                    })
                    .then(function(r) {
                        if (!r.ok) {
                            return r.json().then(function(data) {
                                throw data;
                            });
                        }
                        return r.json();
                    })
                    .then(function(data) {
                        if (data.success && data.thanh_tien !== undefined && thanhTienCell) {
                            thanhTienCell.setAttribute('data-value', data.thanh_tien);
                            thanhTienCell.textContent = formatMoney(data.thanh_tien);
                            if (typeof data.max === 'number') {
                                max = data.max;
                                form.dataset.max = data.max;
                                input.max = data.max;
                                if (data.max < 1) {
                                    input.value = 0;
                                    input.setAttribute('disabled', 'disabled');
                                    form.querySelector('.btn-qty-minus').setAttribute('disabled', 'disabled');
                                    form.querySelector('.btn-qty-plus').setAttribute('disabled', 'disabled');
                                    const checkbox = row ? row.querySelector('.cart-item-checkbox') : null;
                                    if (checkbox) {
                                        checkbox.checked = false;
                                        checkbox.setAttribute('disabled', 'disabled');
                                    }
                                }
                            }
                            if (typeof data.so_luong === 'number') {
                                input.value = data.so_luong;
                            }
                            refreshTongTien();
                            syncSelectAll();
                            syncSelection();
                        }
                    })
                    .catch(function(err) {
                        if (err && err.errors && err.errors.so_luong) {
                            showError(err.errors.so_luong[0]);
                            if (typeof err.max === 'number') {
                                max = err.max;
                                form.dataset.max = err.max;
                                input.max = err.max;
                            }
                            return;
                        }
                        showError('Không thể cập nhật số lượng. Vui lòng thử lại.');
                        form.submit();
                    });
            });
        });

        document.querySelectorAll('.form-remove-item').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    text: "Bạn muốn xóa sản phẩm này khỏi giỏ hàng?",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        if (selectAllEl) {
            selectAllEl.addEventListener('change', function() {
                const checked = selectAllEl.checked;
                document.querySelectorAll('.cart-item-checkbox').forEach(function(cb) {
                    if (!cb.disabled) cb.checked = checked;
                });
                refreshTongTien();
                syncSelectAll();
                syncSelection();
            });
        }

        document.querySelectorAll('.cart-item-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                refreshTongTien();
                syncSelectAll();
                syncSelection();
            });
        });

        document.getElementById('btn-proceed-to-checkout')?.addEventListener('click', function() {
            const selectedIds = collectSelectedIds();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa chọn sản phẩm',
                    text: 'Vui lòng chọn ít nhất một sản phẩm để thanh toán',
                    confirmButtonText: 'Đóng'
                });
                return;
            }

            const queryString = new URLSearchParams({
                items: selectedIds.join(',')
            }).toString();

            window.location.href = '{{ route("dat-hang") }}?' + queryString;
        });
    });
</script>
@endif