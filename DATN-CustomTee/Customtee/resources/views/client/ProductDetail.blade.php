@include('client.layout.header')

<nav aria-label="breadcrumb" class="my-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $sanPham->category->ten_danh_muc ?? 'Danh mục' }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $sanPham->ten_san_pham }}</li>
    </ol>
</nav>

<div class="container my-5">
    <div class="row g-5">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="{{ asset('storage/' . $sanPham->hinh_anh_chinh) }}"
                    class="img-fluid w-100 product-main-img"
                    alt="{{ $sanPham->ten_san_pham }}">
            </div>

            <!-- Img Albulm -->
        </div>

        <div class="col-lg-7">
            <h1 class="fw-bold mb-2">{{ $sanPham->ten_san_pham }}</h1>

            <div class="mb-3">
                <span class="text-warning">★★★★★</span>
                <span class="text-muted ms-2">(128 đánh giá)</span>
            </div>

            <div class="mb-4">
                <h3 class="d-inline fw-bold text-danger me-3" id="gia-hien-tai">
                    {{ number_format($giaMacDinh->gia_khuyen_mai ?? $giaMacDinh->gia) }} ₫
                </h3>

                @if($giaMacDinh->gia_khuyen_mai && $giaMacDinh->gia_khuyen_mai < $giaMacDinh->gia)
                    <span class="text-muted text-decoration-line-through fs-5" id="gia-goc">
                        {{ number_format($giaMacDinh->gia) }} ₫
                    </span>
                    <span class="badge bg-danger ms-2" id="phan-tram-giam">
                        -{{ round(100 - ($giaMacDinh->gia_khuyen_mai / $giaMacDinh->gia * 100)) }}%
                    </span>
                    @endif
            </div>

            <p class="text-secondary mb-4 lead">
                {{ $sanPham->mo_ta_ngan }}
            </p>

            <div class="mb-4">
                <label class="fw-semibold d-block mb-2">Màu sắc:</label>
                <div class="d-flex flex-wrap gap-2" id="color-options">
                    @foreach ($sanPham->variants->where('trang_thai', true)->unique('mau_sac_id') as $variant)
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm color-btn rounded-pill px-3"
                        data-color-id="{{ $variant->mau_sac_id }}"
                        data-toggle="button">
                        {{ $variant->color->ten_mau }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="fw-semibold d-block mb-2">Kích thước:</label>
                <div class="d-flex flex-wrap gap-2" id="size-options">
                    @foreach ($sanPham->variants->where('trang_thai', true)->unique('kich_thuoc_id') as $variant)
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
                    Còn {{ $giaMacDinh->so_luong ?? 0 }} sản phẩm
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
                        <span class="text-success">Còn hàng</span>
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
        const quantityInput = document.getElementById('quantity');
        const addToCartBtn = document.getElementById('btn-add-to-cart');

        let selectedColor = null;
        let selectedSize = null;

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
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });

        document.getElementById('btn-decrease').addEventListener('click', () => {
            if (quantityInput.value > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });

        function updateVariantInfo() {
            if (!selectedColor || !selectedSize) return;

            fetch(`/api/product-variant?product_id={{ $sanPham->id }}&color=${selectedColor}&size=${selectedSize}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.variant) {
                        document.getElementById('gia-hien-tai').textContent = new Intl.NumberFormat('vi-VN').format(data.variant.gia_khuyen_mai || data.variant.gia) + ' ₫';
                        document.getElementById('ton-kho-info').textContent = `Còn ${data.variant.so_luong} sản phẩm`;

                    } else {
                    }
                });
        }

        addToCartBtn.addEventListener('click', function() {
            if (!selectedColor || !selectedSize) {
                alert('Vui lòng chọn màu sắc và kích thước!');
                return;
            }
            alert('Đã thêm vào giỏ hàng!');
        });
    });
</script>