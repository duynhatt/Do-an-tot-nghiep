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
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
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

                        <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                        name="full_name" value="{{ old('full_name') }}" required>
                                    @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone') }}" required pattern="0[0-9]{9,10}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                                    <textarea
                                        name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Số nhà, đường, thôn/xóm..."
                                        rows="3"
                                        required>{{ old('address') }}</textarea>

                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi chú (tùy chọn)</label>
                                    <textarea class="form-control" name="note" rows="3"
                                        placeholder="Nhập ghi chú cho tài xế...">{{ old('note') }}</textarea>
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
                                            <label class="form-check-label fw-bold" for="cod">
                                                Thanh toán khi nhận hàng (COD)
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Bạn trả tiền mặt khi nhận hàng</small>
                                    </div>

                                    <div class="payment-option" data-method="online">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online" required>
                                            <label class="form-check-label fw-bold" for="online">
                                                Thanh toán online
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">VNPay, Momo, ZaloPay, thẻ ngân hàng...</small>
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
                                <img src="{{ asset('storage/' . $item->sanPham->hinh_anh_chinh) }}"
                                    alt="{{ $item->sanPham->ten_san_pham }}" class="me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $item->sanPham->ten_san_pham }}</h6>
                                    <small class="text-muted d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-light text-dark border">
                                            Size: {{ $item->bienThe->size->ten_kich_thuoc ?? 'N/A' }}
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            Color: {{ $item->bienThe->color->ten_mau ?? 'N/A' }}
                                        </span>
                                        <span class="fw-bold text-danger">
                                            × {{ $item->so_luong }}
                                        </span>
                                    </small>
                                    <div class="fw-bold text-primary mt-1">
                                        {{ number_format($item->thanh_tien) }} ₫
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted">Giỏ hàng trống</p>
                            @endforelse
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span id="subtotal-display">{{ number_format($subtotal) }} ₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển</span>
                            <span id="shipping-display" class="{{ $shippingFee > 0 ? '' : 'text-success' }}">
                                {{ $shippingFee > 0 ? number_format($shippingFee) . ' ₫' : 'Miễn phí' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Mã giảm giá</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="voucher-code"
                                    placeholder="Nhập mã giảm giá" autocomplete="off">
                                <button class="btn btn-outline-primary" type="button" id="apply-voucher">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="voucher-message" class="mt-2 small fw-medium" style="min-height: 1.2rem;"></div>
                        </div>

                        <div id="discount-row" class="d-flex justify-content-between mb-2 text-success" style="display: {{ $discount > 0 ? 'flex' : 'none' }};">
                            <span>Giảm giá (voucher)</span>
                            <span id="discount-display">-{{ number_format($discount) }} ₫</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Tổng cộng</h5>
                            <h4 class="mb-0 text-primary fw-bold" id="total-display">{{ number_format($total) }} ₫</h4>
                        </div>

                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-place-order w-100">
                            ĐẶT HÀNG
                        </button>

                        <p class="text-center text-muted small mt-3 mb-0">
                            Bằng cách đặt hàng, bạn đồng ý với Điều khoản dịch vụ của Customtee
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    const apiBase = 'https://provinces.open-api.vn/api/';

    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Lỗi tải dữ liệu địa chỉ');
            return await response.json();
        } catch (error) {
            console.error(error);
            alert('Không thể tải danh sách địa chỉ. Vui lòng thử lại sau.');
            return [];
        }
    }

    async function loadProvinces() {
        const provinces = await fetchData(`${apiBase}?depth=1`);
        provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
        provinces.forEach(p => {
            const option = document.createElement('option');
            option.value = p.name;
            option.textContent = p.name;
            option.dataset.code = p.code;
            provinceSelect.appendChild(option);
        });

        const oldProvince = "{{ old('province') }}";
        if (oldProvince) {
            provinceSelect.value = oldProvince;
            if (provinceSelect.value) loadDistricts();
        }
    }

    async function loadDistricts() {
        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceCode = selectedOption ? selectedOption.dataset.code : null;

        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        districtSelect.disabled = true;
        wardSelect.disabled = true;

        if (!provinceCode) return;

        const districts = await fetchData(`${apiBase}p/${provinceCode}?depth=2`);
        districts.districts.forEach(d => {
            const option = document.createElement('option');
            option.value = d.name;
            option.textContent = d.name;
            option.dataset.code = d.code;
            districtSelect.appendChild(option);
        });

        districtSelect.disabled = false;

        const oldDistrict = "{{ old('district') }}";
        if (oldDistrict) {
            districtSelect.value = oldDistrict;
            if (districtSelect.value) loadWards();
        }
    }

    async function loadWards() {
        const selectedOption = districtSelect.options[districtSelect.selectedIndex];
        const districtCode = selectedOption ? selectedOption.dataset.code : null;

        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        wardSelect.disabled = true;

        if (!districtCode) return;

        const wards = await fetchData(`${apiBase}d/${districtCode}?depth=2`);
        wards.wards.forEach(w => {
            const option = document.createElement('option');
            option.value = w.name;
            option.textContent = w.name;
            wardSelect.appendChild(option);
        });

        wardSelect.disabled = false;

        const oldWard = "{{ old('ward') }}";
        if (oldWard) wardSelect.value = oldWard;
    }

    provinceSelect.addEventListener('change', loadDistricts);
    districtSelect.addEventListener('change', loadWards);

    loadProvinces();
</script>

@include('client.layout.footer')
@include('client.layout.scripts')