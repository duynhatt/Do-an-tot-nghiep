@include('client.layout.header')

<style>
    .checkout-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 40px 0;
    }
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
    }
    .step-number {
        width: 32px;
        height: 32px;
        background: #0d6efd;
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: bold;
        margin-right: 12px;
    }
    .payment-option {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .payment-option.active,
    .payment-option:hover {
        border-color: #0d6efd;
        background: #e7f1ff;
    }
    .order-item img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
    }
    .btn-place-order {
        font-size: 1.1rem;
        padding: 14px;
        border-radius: 10px;
    }
    @media (max-width: 991px) {
        .order-summary {
            position: static !important;
        }
    }
</style>

<div class="checkout-container">
    @if ($errors->any())
    <div class="container">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-dark">THANH TOÁN</h2>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 d-flex align-items-center">
                            <span class="step-number">1</span> Thông tin nhận hàng
                        </h4>

                        <form method="POST" action="{{ !empty($buyNowMode) ? route('checkout.buy-now.process') : route('checkout.process') }}" id="checkoutForm">
                            @csrf
                            @if(!empty($buyNowMode))
                                <input type="hidden" name="mode" value="buy_now">
                            @endif
                            <input type="hidden" name="voucher_code_applied" id="hidden-voucher-code">
                            
                            @if(empty($buyNowMode))
                                <input type="hidden" name="selected_items" value="{{ request()->query('items', '') }}">
                            @endif
                            <div class="row g-3">
                                <div class="col-md-6">
<label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                        name="full_name" value="{{ old('full_name') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone') }}" required>
                                </div>

                               <div class="col-md-4">
                                    <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select class="form-select @error('province') is-invalid @enderror" name="province" id="province" required>
                                        <option value="">Chọn tỉnh/thành</option>
                                    </select>
                                    @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select class="form-select @error('district') is-invalid @enderror" name="district" id="district" required disabled>
                                        <option value="">Chọn quận/huyện</option>
                                    </select>
                                    @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                    <select class="form-select @error('ward') is-invalid @enderror" name="ward" id="ward" required disabled>
                                        <option value="">Chọn phường/xã</option>
                                    </select>
                                    @error('ward')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Địa chỉ chi tiết <span class="text-danger">*</span></label>
<textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường...">{{ old('address') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <textarea class="form-control" name="note" rows="3">{{ old('note') }}</textarea>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-body p-4">
                                    <h4 class="mb-4 d-flex align-items-center">
                                        <span class="step-number">2</span> Phương thức thanh toán
                                    </h4>

                                    <div class="payment-option active" data-method="cod">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked required>
                                            <label class="form-check-label fw-bold" for="cod">Thanh toán khi nhận hàng (COD)</label>
                                        </div>
                                    </div>

                                    <div class="payment-option" data-method="online">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="vnpay" required>
                                            <label class="form-check-label fw-bold" for="online">Thanh toán trực tuyến</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card order-summary position-sticky" style="top: 20px;">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Đơn hàng của bạn</h4>

                        <div class="mb-4">
                            @forelse($cartItems as $item)
                            <div class="d-flex align-items-center mb-3 order-item">
                                <img src="{{ asset('storage/' . $item->sanPham->hinh_anh_chinh) }}" class="me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $item->sanPham->ten_san_pham }}</h6>
                                    <small class="text-muted">
                                        Size: {{ $item->bienThe->size->ten_kich_thuoc ?? 'N/A' }} |
Color: {{ $item->bienThe->color->ten_mau ?? 'N/A' }} | 
                                        SL: {{ $item->checkout_qty ?? $item->so_luong }}
                                    </small>
                                    <div class="fw-bold text-primary">{{ number_format($item->checkout_thanh_tien ?? $item->thanh_tien) }} ₫</div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted">Giỏ hàng trống</p>
                            @endforelse
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span>{{ number_format($subtotal) }} ₫</span>
                        </div>

                    <div class="d-flex justify-content-between mb-3">
                 <span>Phí vận chuyển</span>
                  <span id="shipping-display" class="{{ $shippingFee > 0 ? '' : 'text-success' }}">
                   {{ $shippingFee > 0 ? number_format($shippingFee) . ' ₫' : 'Miễn phí' }}
                   </span>
                    </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Mã giảm giá</label>
                            @if(isset($availableVouchers) && $availableVouchers->isNotEmpty())
                                <p class="small text-muted mb-2">Voucher được phép dùng:</p>
                                <div class="voucher-list mb-3">
                                    @foreach($availableVouchers as $v)
                                        <div class="border rounded p-2 mb-2 voucher-item" data-code="{{ $v->ma }}" role="button" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-primary">{{ $v->ma }}</span>
                                                <span class="badge bg-success">
                                                    @if($v->loai == 'phan_tram')
                                                        Giảm {{ (int)$v->gia_tri }}% @if($v->giam_toi_da) (tối đa {{ number_format($v->giam_toi_da) }}đ) @endif
                                                    @else
                                                        Giảm {{ number_format($v->gia_tri) }}đ
                                                    @endif
                                                </span>
                                            </div>
                                            @if($v->don_hang_toi_thieu)
                                                <small class="text-muted">Đơn từ {{ number_format($v->don_hang_toi_thieu) }}đ</small>
                                            @endif
</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="input-group">
                                <input type="text" class="form-control" id="voucher-code" placeholder="Nhập mã...">
                                <button class="btn btn-outline-primary" type="button" id="apply-voucher">Áp dụng</button>
                            </div>
                            <div id="voucher-message" class="mt-2 small fw-medium"></div>
                        </div>

                        <div id="discount-row" class="justify-content-between mb-2 text-success" style="display: none;">
                            <span>Giảm giá (Voucher)</span>
                            <span id="discount-display">-0 ₫</span>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Tổng cộng</h5>
                            <h4 class="mb-0 text-primary fw-bold" id="total-display">{{ number_format($total) }} ₫</h4>
                        </div>

                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-place-order w-100" id="btn-place-order">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var btnPlaceOrder = document.getElementById('btn-place-order');
    var voucherBlockPlaceOrder = false;

    function setPlaceOrderBlock(block) {
        voucherBlockPlaceOrder = block;
        btnPlaceOrder.disabled = block;
        btnPlaceOrder.title = block ? 'Voucher chưa đủ điều kiện. Vui lòng xóa mã hoặc tăng giá trị đơn hàng.' : '';
    }

    // Khi xóa mã voucher -> cho phép đặt hàng lại
    document.getElementById('voucher-code').addEventListener('input', function() {
        if (!this.value.trim()) {
            setPlaceOrderBlock(false);
            document.getElementById('hidden-voucher-code').value = '';
        }
    });

    // Click voucher trong danh sách -> điền mã và áp dụng
    document.querySelectorAll('.voucher-item').forEach(function(el) {
        el.addEventListener('click', function() {
            const code = this.dataset.code;
            document.getElementById('voucher-code').value = code;
        });
    });

    // Logic cho Voucher
    document.getElementById('apply-voucher').addEventListener('click', function() {
        const code = document.getElementById('voucher-code').value.trim();
        const subtotal = {{ $subtotal }};
        const msg = document.getElementById('voucher-message');
        const btn = this;

        if (!code) {
            msg.innerHTML = '<span class="text-danger">Vui lòng nhập mã!</span>';
            setPlaceOrderBlock(false);
            return;
        }

        btn.disabled = true;
btn.innerText = '...';

        fetch("{{ route('voucher.apply') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ voucher_code: code, total_amount: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Áp dụng';
            document.getElementById('hidden-voucher-code').value = '';
            if (data.success) {
                setPlaceOrderBlock(false);
                msg.innerHTML = '<span class="text-success">' + data.message + '</span>';
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount-display').innerText = '-' + data.discount.toLocaleString() + ' ₫';
                var shippingFee = {{ $shippingFee }};
                var finalTotal = data.new_total + shippingFee;
                document.getElementById('total-display').innerText = finalTotal.toLocaleString() + ' ₫';
                document.getElementById('hidden-voucher-code').value = data.voucher_ma;
            } else {
                var isMinOrderError = (data.message || '').indexOf('tối thiểu') !== -1 || (data.message || '').indexOf('điều kiện') !== -1;
                if (isMinOrderError) {
                    setPlaceOrderBlock(true);
                } else {
                    setPlaceOrderBlock(false);
                }
                msg.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                document.getElementById('discount-row').style.display = 'none';
            }
        });
    });

    // Logic cho Payment Method
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // Logic cho Địa chỉ (Province API)
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const apiBase = 'https://provinces.open-api.vn/api/';

    async function fetchData(url) {
        const res = await fetch(url);
        return await res.json();
    }

    async function loadProvinces() {
        const provinces = await fetchData(`${apiBase}?depth=1`);
        provinces.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.name; opt.textContent = p.name; opt.dataset.code = p.code;
            provinceSelect.appendChild(opt);
        });
    }
provinceSelect.addEventListener('change', async function() {
        const code = this.options[this.selectedIndex].dataset.code;
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        if(!code) return;
        const data = await fetchData(`${apiBase}p/${code}?depth=2`);
        data.districts.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.name; opt.textContent = d.name; opt.dataset.code = d.code;
            districtSelect.appendChild(opt);
        });
        districtSelect.disabled = false;
    });

    districtSelect.addEventListener('change', async function() {
        const code = this.options[this.selectedIndex].dataset.code;
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        if(!code) return;
        const data = await fetchData(`${apiBase}d/${code}?depth=2`);
        data.wards.forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.name; opt.textContent = w.name;
            wardSelect.appendChild(opt);
        });
        wardSelect.disabled = false;
    });

    loadProvinces();
</script>

@include('client.layout.footer')
@include('client.layout.scripts')