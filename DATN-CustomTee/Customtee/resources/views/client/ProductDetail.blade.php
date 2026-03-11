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

            <div class="mb-3">
                <span class="text-warning">★★★★★</span>
                <span class="text-muted ms-2">(128 đánh giá)</span>
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

        let selectedColor = null;
        let selectedSize = null;
        let currentVariantId = null;

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
            let qty = parseInt(document.getElementById('quantity').value);
            document.getElementById('quantity').value = qty + 1;
        });

        document.getElementById('btn-decrease').addEventListener('click', () => {
            let qty = parseInt(document.getElementById('quantity').value);
            if (qty > 1) document.getElementById('quantity').value = qty - 1;
        });

        function updateVariantInfo() {
            if (!selectedColor || !selectedSize) {
                giaHienTai.textContent = '{{ $priceRange }}';
                giaGoc.classList.add('d-none');
                phanTramGiam.classList.add('d-none');
                tonKhoInfo.innerHTML = '{{ $totalStock > 0 ? "Còn $totalStock sản phẩm (tổng tất cả biến thể)" : "Hết hàng" }}';
                return;
            }

            fetch(`/api/product-variant?product_id={{ $sanPham->id }}&color=${selectedColor}&size=${selectedSize}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.variant) {
                        currentVariantId = data.variant.id || null;
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

                        tonKhoInfo.textContent = `Còn ${data.variant.so_luong} sản phẩm`;
                    } else {
                        currentVariantId = null;
                        giaHienTai.textContent = 'Hết hàng';
                        tonKhoInfo.textContent = 'Hết hàng';
                        giaGoc.classList.add('d-none');
                        phanTramGiam.classList.add('d-none');
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
            const qty = parseInt(document.getElementById('quantity').value, 10) || 1;
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
            .then(r => {
                if (r.status === 401) {
                    window.location.href = '{{ url("/login") }}';
                    return;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    alert(data.message || 'Đã thêm vào giỏ hàng!');
                } else {
                    const msg = (data.errors && Object.values(data.errors).flat().length) ? Object.values(data.errors).flat().join('\n') : (data.message || 'Có lỗi xảy ra.');
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
            const qty = parseInt(document.getElementById('quantity').value, 10) || 1;
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                alert('Phiên đăng nhập hết hạn. Vui lòng tải lại trang.');
                return;
            }
            buyNowBtn.disabled = true;
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
                    so_luong: qty,
                    buy_now: true
                })
            })
            .then(r => {
                if (r.status === 401) {
                    window.location.href = '{{ url("/login") }}';
                    return;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                if (data.success && data.cart_item_id) {
                    const itemsParam = data.cart_item_id + (qty > 0 ? ':' + qty : '');
                    window.location.href = '{{ route("dat-hang") }}?items=' + encodeURIComponent(itemsParam);
                } else if (data.success) {
                    alert(data.message || 'Đã thêm vào giỏ hàng!');
                } else {
                    const msg = (data.errors && Object.values(data.errors).flat().length) ? Object.values(data.errors).flat().join('\n') : (data.message || 'Có lỗi xảy ra.');
                    alert(msg);
                }
            })
            .catch(() => alert('Có lỗi xảy ra. Vui lòng thử lại.'))
            .finally(() => { buyNowBtn.disabled = false; });
        });
    });
</script>