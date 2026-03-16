@include('client.layout.header')

<div class="container">
    <nav aria-label="breadcrumb" class="my-4">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item">
                <a href="{{ url('/Shop') }}" class="text-decoration-none">
                    {{ $sanPham->category->ten_danh_muc ?? 'Danh mục' }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $sanPham->ten_san_pham }}</li>
        </ol>
    </nav>
</div>

<div class="container my-5">
    <div class="row g-5">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="{{ asset('storage/' . $sanPham->hinh_anh_chinh) }}"
                    class="img-fluid w-100 product-main-img"
                    alt="{{ $sanPham->ten_san_pham }}">
            </div>
            <!-- Album -->
        </div>

        <div class="col-lg-7">
            <h1 class="fw-bold mb-2">{{ $sanPham->ten_san_pham }}</h1>

            <div class="mb-3 d-flex align-items-center">
                <div class="text-warning me-2 fs-5">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($avgRating))
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                </div>
                <span class="text-muted">
                    {{ $avgRating }}/5 ({{ $totalRating }} đánh giá)
                </span>
            </div>

            <div class="mb-4">
                <h3 class="d-inline fw-bold text-danger me-3" id="gia-hien-tai">
                    {{ $priceRange }}
                </h3>

                <span class="text-muted text-decoration-line-through fs-5 d-none" id="gia-goc"></span>
                <span class="badge bg-danger ms-2 d-none" id="phan-tram-giam"></span>
            </div>

            <p class="text-secondary mb-4 lead">
                {{ $sanPham->mo_ta_ngan }}
            </p>

            <div class="mb-4">
                <label class="fw-semibold d-block mb-2">Màu sắc:</label>
                <div class="d-flex flex-wrap gap-2" id="color-options">
                    @foreach ($sanPham->variants->unique('mau_sac_id') as $variant)
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm color-btn rounded-pill px-3"
                        data-color-id="{{ $variant->mau_sac_id }}">
                        {{ $variant->color->ten_mau }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="fw-semibold d-block mb-2">Kích thước:</label>
                <div class="d-flex flex-wrap gap-2" id="size-options">
                    @foreach ($sanPham->variants->unique('kich_thuoc_id') as $variant)
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm size-btn px-3"
                        data-size-id="{{ $variant->kich_thuoc_id }}">
                        {{ $variant->size->ten_kich_thuoc }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="fw-semibold me-3">Số lượng:</label>
                <div class="input-group w-50 w-md-25">
                    <button class="btn btn-outline-secondary" type="button" id="btn-decrease">-</button>
                    <input type="number" class="form-control text-center" id="quantity" min="1" value="1" max="999">
                    <button class="btn btn-outline-secondary" type="button" id="btn-increase">+</button>
                </div>
                <small class="text-muted d-block mt-2" id="ton-kho-info">
                    @if($totalStock > 0)
                    Còn {{ $totalStock }} sản phẩm (tổng tất cả biến thể)
                    @else
                    Hết hàng
                    @endif
                </small>
            </div>

            <div class="d-flex flex-wrap gap-3 mb-4">
                <button class="btn btn-success btn-lg px-5" id="btn-add-to-cart">
                    <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                </button>

                <button class="btn btn-outline-danger btn-lg px-5" id="btn-buy-now">
                    <i class="fas fa-bolt me-2"></i> Mua ngay
                </button>
            </div>

            <div class="border-top pt-4">
                <h5 class="mb-3">Thông tin sản phẩm</h5>
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <strong>Danh mục:</strong><br>
                        {{ $sanPham->category->ten_danh_muc ?? '—' }}
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Tình trạng:</strong><br>
                        <span class="text-success">{{ $totalStock > 0 ? 'Còn hàng' : 'Hết hàng' }}</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <strong>Bảo hành:</strong><br>
                        12 tháng
                    </div>
                </div>
            </div>

            @if($sanPham->mo_ta_chi_tiet)
            <div class="mt-5">
                <h5 class="mb-3">Mô tả chi tiết</h5>
                <div class="product-description">
                    {!! nl2br(e($sanPham->mo_ta_chi_tiet)) !!}
                </div>
            </div>
            @endif

            <div class="mt-5">
                <h4 class="fw-bold mb-4">Đánh giá sản phẩm</h4>

                @if($danhGias->count() > 0)

                    @foreach($danhGias as $dg)

                    <div class="border rounded p-3 mb-3 shadow-sm">

                        <div class="d-flex justify-content-between mb-2">
                            <strong>{{ $dg->user->name ?? 'Khách hàng' }}</strong>

                            <small class="text-muted">
                                {{ $dg->created_at->format('d/m/Y') }}
                            </small>
                        </div>

                        {{-- Sao --}}
                        <div class="text-warning mb-2">
                            @for($i=1;$i<=5;$i++)
                                @if($i <= $dg->so_sao)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>

                        <p class="mb-0 text-secondary">
                            {{ $dg->noi_dung }}
                        </p>

                    </div>

                    @endforeach

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $danhGias->links('pagination::bootstrap-5') }}
                    </div>

                @else

                    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>

                @endif
            </div>

        </div>
    </div>
</div>

<style>
    .product-main-img {
        transition: transform 0.3s ease;
    }

    .product-main-img:hover {
        transform: scale(1.03);
    }

    .btn.active {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }

    .color-btn.active,
    .size-btn.active {
        background-color: #198754;
        color: white;
        border-color: #198754;
    }

    .product-description {
        line-height: 1.8;
    }
</style>

@include('client.layout.footer')
@include('client.layout.scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorButtons = document.querySelectorAll('.color-btn');
        const sizeButtons = document.querySelectorAll('.size-btn');
        const giaHienTai = document.getElementById('gia-hien-tai');
        const giaGoc = document.getElementById('gia-goc');
        const phanTramGiam = document.getElementById('phan-tram-giam');
        const tonKhoInfo = document.getElementById('ton-kho-info');
        const addToCartBtn = document.getElementById('btn-add-to-cart');
        const buyNowBtn = document.getElementById('btn-buy-now');
        const quantityInput = document.getElementById('quantity');

        let selectedColor = null;
        let selectedSize = null;
        let currentVariantId = null;
        let currentStock = null;

        colorButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                colorButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedColor = this.dataset.colorId;
                updateVariantInfo();
            });
        });

        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                sizeButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedSize = this.dataset.sizeId;
                updateVariantInfo();
            });
        });

        document.getElementById('btn-increase').addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10) || 1;
            if (currentStock !== null) {
                qty = Math.min(qty + 1, currentStock);
            } else {
                qty = qty + 1;
            }
            quantityInput.value = qty;
        });

        document.getElementById('btn-decrease').addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10) || 1;
            if (qty > 1) {
                quantityInput.value = qty - 1;
            }
        });

        // Người dùng gõ tay số lượng
        quantityInput.addEventListener('change', () => {
            let qty = parseInt(quantityInput.value, 10) || 1;
            qty = Math.max(1, qty);
            if (currentStock !== null) {
                qty = Math.min(qty, currentStock);
            }
            quantityInput.value = qty;
        });

        function updateVariantInfo() {
            if (!selectedColor || !selectedSize) {
                currentVariantId = null;
                currentStock = null;
                giaHienTai.textContent = '{{ $priceRange }}';
                giaGoc.classList.add('d-none');
                phanTramGiam.classList.add('d-none');
                tonKhoInfo.innerHTML = '{{ $totalStock > 0 ? "Còn $totalStock sản phẩm (tổng tất cả biến thể)" : "Hết hàng" }}';
                quantityInput.disabled = false;
                addToCartBtn.disabled = false;
                buyNowBtn.disabled = false;
                return;
            }

            fetch(`/api/product-variant?product_id={{ $sanPham->id }}&color=${selectedColor}&size=${selectedSize}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.variant) {
                        currentVariantId = data.variant.id || null;
                        currentStock = parseInt(data.variant.so_luong, 10) || 0;
                        const giaBan = data.variant.gia_khuyen_mai || data.variant.gia;
                        giaHienTai.textContent = new Intl.NumberFormat('vi-VN').format(giaBan) + ' ₫';

                        if (data.variant.gia_khuyen_mai && data.variant.gia_khuyen_mai < data.variant.gia) {
                            giaGoc.textContent = new Intl.NumberFormat('vi-VN').format(data.variant.gia) + ' ₫';
                            const percent = Math.round(100 - (data.variant.gia_khuyen_mai / data.variant.gia * 100));
                            phanTramGiam.textContent = `-${percent}%`;
                            giaGoc.classList.remove('d-none');
                            phanTramGiam.classList.remove('d-none');
                        } else {
                            giaGoc.classList.add('d-none');
                            phanTramGiam.classList.add('d-none');
                        }

                        tonKhoInfo.textContent = `Còn ${currentStock} sản phẩm`;

                        // Cập nhật giới hạn số lượng theo tồn kho
                        quantityInput.max = currentStock > 0 ? currentStock : 999;
                        if (currentStock <= 0) {
                            quantityInput.value = 0;
                            quantityInput.disabled = true;
                            addToCartBtn.disabled = true;
                            buyNowBtn.disabled = true;
                        } else {
                            if (parseInt(quantityInput.value, 10) < 1) {
                                quantityInput.value = 1;
                            }
                            if (parseInt(quantityInput.value, 10) > currentStock) {
                                quantityInput.value = currentStock;
                            }
                            quantityInput.disabled = false;
                            addToCartBtn.disabled = false;
                            buyNowBtn.disabled = false;
                        }
                    } else {
                        currentVariantId = null;
                        currentStock = null;
                        giaHienTai.textContent = 'Hết hàng';
                        tonKhoInfo.textContent = 'Hết hàng';
                        giaGoc.classList.add('d-none');
                        phanTramGiam.classList.add('d-none');
                        quantityInput.value = 0;
                        quantityInput.disabled = true;
                        addToCartBtn.disabled = true;
                        buyNowBtn.disabled = true;
                    }
                })
                .catch(() => {
                    giaHienTai.textContent = 'Lỗi tải giá';
                });
        }

        addToCartBtn.addEventListener('click', function() {
            if (!selectedColor || !selectedSize) {
                alert('Vui lòng chọn màu sắc và kích thước!');
                return;
            }
            if (!currentVariantId) {
                alert('Vui lòng chọn lại màu và kích thước.');
                return;
            }
            let qty = parseInt(quantityInput.value, 10) || 1;
            qty = Math.max(1, qty);
            if (currentStock !== null) {
                if (qty > currentStock) {
                    alert('Số lượng tối đa có thể mua là ' + currentStock);
                    qty = currentStock;
                }
            }
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                alert('Phiên đăng nhập hết hạn. Vui lòng tải lại trang.');
                return;
            }
            addToCartBtn.disabled = true;
            fetch('{{ route("gio-hang.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    san_pham_id: {{ $sanPham->id }},
                    bien_the_id: currentVariantId,
                    so_luong: qty
                })
            })
            .then(async r => {
                if (r.status === 401) {
                    window.location.href = '{{ url("/login") }}';
                    return;
                }
                const data = await r.json();
                if (!data) return;
                if (r.ok && data.success) {
                    alert(data.message || 'Đã thêm vào giỏ hàng!');
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat() : [];
                    const msg = errors.length ? errors.join('\n') : (data.message || 'Có lỗi xảy ra.');
                    alert(msg);
                }
            })
            .catch(() => alert('Có lỗi xảy ra. Vui lòng thử lại.'))
            .finally(() => { addToCartBtn.disabled = false; });
        });

        buyNowBtn.addEventListener('click', function() {
            if (!selectedColor || !selectedSize) {
                alert('Vui lòng chọn màu sắc và kích thước!');
                return;
            }
            if (!currentVariantId) {
                alert('Vui lòng chọn lại màu và kích thước.');
                return;
            }
            let qty = parseInt(quantityInput.value, 10) || 1;
            qty = Math.max(1, qty);
            if (currentStock !== null) {
                if (qty > currentStock) {
                    alert('Số lượng tối đa có thể mua là ' + currentStock);
                    qty = currentStock;
                }
            }
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                alert('Phiên đăng nhập hết hạn. Vui lòng tải lại trang.');
                return;
            }
            buyNowBtn.disabled = true;
            fetch('{{ route("buy-now") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    san_pham_id: {{ $sanPham->id }},
                    bien_the_id: currentVariantId,
                    so_luong: qty
                })
            })
            .then(async r => {
                if (r.status === 401) {
                    window.location.href = '{{ url("/login") }}';
                    return;
                }
                const data = await r.json();
                if (!data) return;
                if (r.ok && data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    const errors = data.errors ? Object.values(data.errors).flat() : [];
                    const msg = errors.length ? errors.join('\n') : (data.message || 'Có lỗi xảy ra.');
                    alert(msg);
                }
            })
            .catch(() => alert('Có lỗi xảy ra. Vui lòng thử lại.'))
            .finally(() => { buyNowBtn.disabled = false; });
        });
    });
</script>