
@include('client.layout.header')
@include('client.layout.banner')



{{-- Danh mục bán chạy: bố cục 1 / 2 / 3 / 4 ô (tối đa 4, xếp theo SL bán trong 30 ngày) --}}
@if($danhMucs->isNotEmpty())
@php
    $browseSlots = $danhMucs->take(4)->values();
    $browseCount = $browseSlots->count();
@endphp
<section class="py-4 bg-white">
    <div class="container py-3">
        <style>
            
            .browse-by-style-wrap {
                background: #f0f0f0;
                border-radius: 1.5rem;
                padding: clamp(1.25rem, 3vw, 2.5rem);
            }

            .browse-by-style-title {
                font-size: clamp(1.5rem, 3.2vw, 2.35rem);
                font-weight: 800;
                letter-spacing: 0.06em;
                text-align: center;
                text-transform: uppercase;
                color: #555555;
                margin-bottom: 0.4rem;
            }

            .browse-by-style-sub {
                text-align: center;
                color: #6c757d;
                font-size: clamp(1rem, 1.8vw, 1.15rem);
                margin-bottom: 1.75rem;
            }

            .browse-style-grid {
                display: grid;
                gap: 1.25rem; /* ~20px  */
            }

            /* 4 danh mục: 1/3 + 2/3 hàng 1, 2/3 + 1/3 hàng 2 */
            .browse-style-grid--4 {
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: minmax(180px, 22vw) minmax(180px, 22vw);
            }

            .browse-style-grid--4 .browse-slot--1 {
                grid-column: 1 / 2;
                grid-row: 1;
            }

            .browse-style-grid--4 .browse-slot--2 {
                grid-column: 2 / 4;
                grid-row: 1;
            }

            .browse-style-grid--4 .browse-slot--3 {
                grid-column: 1 / 3;
                grid-row: 2;
            }

            .browse-style-grid--4 .browse-slot--4 {
                grid-column: 3 / 4;
                grid-row: 2;
            }

            /* 3 danh mục: hàng 1 giống 4 (hẹp + rộng), hàng 2 một thẻ full ngang */
            .browse-style-grid--3 {
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: minmax(180px, 22vw) minmax(180px, 22vw);
            }

            .browse-style-grid--3 .browse-slot--1 {
                grid-column: 1 / 2;
                grid-row: 1;
            }

            .browse-style-grid--3 .browse-slot--2 {
                grid-column: 2 / 4;
                grid-row: 1;
            }

            .browse-style-grid--3 .browse-slot--3 {
                grid-column: 1 / 4;
                grid-row: 2;
            }

            /* 2 danh mục: một hàng hẹp + rộng */
            .browse-style-grid--2 {
                grid-template-columns: 1fr 2fr;
                grid-template-rows: minmax(180px, 22vw);
            }

            .browse-style-grid--2 .browse-slot--1 {
                grid-column: 1;
                grid-row: 1;
            }

            .browse-style-grid--2 .browse-slot--2 {
                grid-column: 2;
                grid-row: 1;
            }

            /* 1 danh mục: một thẻ full */
            .browse-style-grid--1 {
                grid-template-columns: 1fr;
                grid-template-rows: minmax(200px, 26vw);
            }

            .browse-style-grid--1 .browse-slot--1 {
                grid-column: 1;
                grid-row: 1;
            }

            .browse-style-card {
                position: relative;
                display: block;
                height: 100%;
                min-height: 180px;
                background: #fff;
                border-radius: 1.35rem; /* ~20–22px, bo góc đậm  */
                overflow: hidden;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
                text-decoration: none;
                color: inherit;
                transition: transform 0.22s ease, box-shadow 0.22s ease;
            }

            .browse-style-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
                color: inherit;
                text-decoration: none;
            }

            .browse-style-card__label {
                position: absolute;
                top: 1.5rem;
                left: 1.5rem;
                z-index: 2;
                font-size: clamp(1.45rem, 3vw, 2.1rem);
                font-weight: 700;
                color: #555555;
                line-height: 1.2;
                max-width: 46%;
                word-break: break-word;
                text-shadow: 0 0 12px #fff, 0 0 4px #fff, 1px 1px 0 #fff;
                pointer-events: none;
            }

            /* Khung ảnh = toàn bộ thẻ*/
            .browse-style-card__media {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                background: #fff;
                overflow: hidden;
                border-radius: inherit;
            }

            .browse-style-card__media img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: right center;
                display: block;
            }

            @media (max-width: 767.98px) {
                .browse-style-grid--1,
                .browse-style-grid--2,
                .browse-style-grid--3,
                .browse-style-grid--4 {
                    grid-template-columns: 1fr;
                    grid-template-rows: none;
                    grid-auto-rows: minmax(160px, 48vw);
                }

                .browse-style-grid .browse-slot--1,
                .browse-style-grid .browse-slot--2,
                .browse-style-grid .browse-slot--3,
                .browse-style-grid .browse-slot--4 {
                    grid-column: 1 !important;
                    grid-row: auto !important;
                }

                .browse-style-card__label {
                    max-width: 50%;
                    top: 1.45rem;
                    left: 1.45rem;
                }
            }
        </style>

        <div class="browse-by-style-wrap">
            <h2 class="browse-by-style-title">Danh mục bán chạy</h2>
            <p class="browse-by-style-sub mb-0">Gợi ý theo mức độ mua nhiều gần đây — chọn danh mục để xem sản phẩm.</p>
            <div class="browse-style-grid browse-style-grid--{{ $browseCount }} mt-4">
                @foreach($browseSlots as $idx => $dm)
                    @php $slotClass = 'browse-slot--' . ($idx + 1); @endphp
                    <a href="{{ url('/Shop?danh_muc=' . $dm->id) }}" class="browse-style-card {{ $slotClass }}">
                        <span class="browse-style-card__label">{{ $dm->ten_danh_muc }}</span>
                        <div class="browse-style-card__media">
                            <img
                                src="{{ $dm->hinh_anh ? asset('storage/' . $dm->hinh_anh) : asset('img/shop_01.jpg') }}"
                                alt="{{ $dm->ten_danh_muc }}"
                                loading="lazy"
                                decoding="async">
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

    <!-- Sản phẩm mới nhất: 10 SP, 5 / hàng (desktop) -->
    <section class="new-products-section">
        <div class="container-fluid new-products-section__inner px-3 px-md-4 px-xl-5">
            <style>
                .new-products-section {
                    background: linear-gradient(180deg, #f8fafb 0%, #ffffff 45%, #f6f7f9 100%);
                }

                .new-products-section__inner {
                    padding-top: 3.25rem;
                    padding-bottom: 4.25rem;
                    max-width: 1280px;
                    margin-left: auto;
                    margin-right: auto;
                }

                @media (min-width: 992px) {
                    .new-products-section__inner {
                        padding-top: 4rem;
                        padding-bottom: 5rem;
                    }

                    .new-product-card .card-body {
                        padding: 1.15rem 1.25rem 1.4rem;
                    }
                }

                .new-products-title {
                    font-size: clamp(1.75rem, 4vw, 2.75rem);
                    font-weight: 800;
                    letter-spacing: 0.04em;
                    text-transform: uppercase;
                    color: #1a1a1a;
                    line-height: 1.15;
                }

                .new-products-sub {
                    text-align: center;
                    color: #5c636a;
                    font-size: clamp(1.05rem, 2vw, 1.2rem);
                    max-width: 36rem;
                    margin-left: auto;
                    margin-right: auto;
                    line-height: 1.55;
                }

                .new-product-card {
                    background: #fff;
                    border: 1px solid rgba(0, 0, 0, 0.06);
                    border-radius: 1.25rem;
                    overflow: hidden;
                    transition: transform 0.28s ease, box-shadow 0.28s ease;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
                }

                .new-product-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 28px 56px rgba(25, 135, 84, 0.12), 0 12px 32px rgba(0, 0, 0, 0.1);
                }

                .new-product-card .new-product-media {
                    display: block;
                    aspect-ratio: 1;
                    overflow: hidden;
                    background: linear-gradient(145deg, #f0f2f5, #e8eaee);
                }

                .new-product-card .new-product-media img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.35s ease;
                }

                .new-product-card:hover .new-product-media img {
                    transform: scale(1.06);
                }

                .new-product-card .card-body {
                    padding: 1rem 1.1rem 1.25rem;
                }

                .new-product-name {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    font-size: clamp(0.95rem, 1.35vw, 1.05rem);
                    font-weight: 700;
                    line-height: 1.35;
                    color: #212529;
                    min-height: 2.7em;
                }

                .new-product-price {
                    font-size: clamp(1rem, 1.5vw, 1.15rem);
                    font-weight: 800;
                    color: #198754;
                    letter-spacing: 0.02em;
                }

            </style>

            <div class="row text-center pb-4 pb-lg-5">
                <div class="col-lg-8 col-xl-7 mx-auto">
                    <h2 class="new-products-title mb-3">Sản phẩm mới nhất</h2>
                    <p class="new-products-sub mb-0">
                        Mười sản phẩm mới cập nhật — xem nhanh, chọn style phù hợp với bạn.
                    </p>
                </div>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3 g-md-4">
                @forelse($sanPhamsMoiNhat as $sp)
                <div class="col">
                    <div class="card h-100 border-0 new-product-card">
                        <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="new-product-media text-decoration-none">
                            <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                class="img-fluid" alt="{{ $sp->ten_san_pham }}" loading="lazy" decoding="async">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="text-decoration-none text-dark new-product-name mb-2 flex-grow-1">
                                {{ $sp->ten_san_pham }}
                            </a>
                            <p class="mb-0 mt-auto">
                                @if($sp->variants_min_gia)
                                    <span class="new-product-price">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="text-muted fw-semibold">Liên hệ</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Chưa có sản phẩm nào.</p>
                    <a href="{{ url('/Shop') }}" class="btn btn-success">Xem tất cả sản phẩm</a>
                </div>
                @endforelse
            </div>
            @if($sanPhamsMoiNhat->isNotEmpty())
            <div class="text-center mt-4 mt-lg-5 pt-2">
                <a href="{{ url('/Shop') }}" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm">Xem tất cả sản phẩm</a>
            </div>
            @endif
        </div>
    </section>

<!-- Sản phẩm Hot & Giảm giá -->
<section class="py-4 py-lg-5 bg-white">
    <div class="container py-2">
        <div class="row g-4">
            <!-- Cột trái: Sản phẩm hot -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="hot-sale-panel h-100 p-3 p-md-4">
                    <style>
                        .hot-sale-panel {
                            background: linear-gradient(165deg, #ffffff 0%, #f5f7f9 48%, #eef1f4 100%);
                            border: 1px solid rgba(0, 0, 0, 0.06);
                            border-radius: 1.35rem;
                            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.07);
                        }

                        .product-strip {
                            overflow: hidden;
                            width: 100%;
                        }

                        .strip-track {
                            display: flex;
                            transform: translateX(0);
                            will-change: transform;
                            transition: transform 450ms ease;
                        }

                        .strip-item {
                            flex: 0 0 50%;
                            padding: 0 0.5rem;
                            box-sizing: border-box;
                        }

                        @media (min-width: 768px) {
                            .strip-item {
                                flex: 0 0 33.333333%;
                                padding: 0 0.55rem;
                            }
                        }

                        .strip-card {
                            background: #fff;
                            border-radius: 1.1rem;
                            overflow: hidden;
                            box-shadow: 0 8px 26px rgba(0, 0, 0, 0.07);
                            transition: transform 0.28s ease, box-shadow 0.28s ease;
                        }

                        .strip-card--hot:hover {
                            transform: translateY(-8px);
                            box-shadow: 0 22px 50px rgba(25, 135, 84, 0.14), 0 12px 32px rgba(0, 0, 0, 0.1);
                        }

                        .strip-card--sale:hover {
                            transform: translateY(-8px);
                            box-shadow: 0 22px 50px rgba(220, 53, 69, 0.12), 0 12px 32px rgba(0, 0, 0, 0.1);
                        }

                        .strip-card-media {
                            display: block;
                            aspect-ratio: 1;
                            overflow: hidden;
                            background: linear-gradient(145deg, #f0f2f5, #e8eaee);
                        }

                        .strip-card-media img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            transition: transform 0.35s ease;
                        }

                        .strip-card:hover .strip-card-media img {
                            transform: scale(1.06);
                        }

                        .strip-card .card-body {
                            padding: 0.85rem 1rem 1.05rem;
                        }

                        @media (min-width: 768px) {
                            .strip-card .card-body {
                                padding: 1rem 1.1rem 1.2rem;
                            }
                        }

                        .strip-badge {
                            font-size: 0.72rem;
                            font-weight: 700;
                            letter-spacing: 0.04em;
                            padding: 0.4em 0.85em;
                            border: none;
                        }

                        .strip-badge--hot {
                            background: linear-gradient(135deg, #20c997, #198754) !important;
                            color: #fff !important;
                        }

                        .strip-badge--sale {
                            background: linear-gradient(135deg, #ff6b6b, #dc3545) !important;
                            color: #fff !important;
                        }

                        .strip-card-name {
                            display: -webkit-box;
                            -webkit-line-clamp: 2;
                            -webkit-box-orient: vertical;
                            overflow: hidden;
                            font-size: clamp(0.9rem, 1.25vw, 1.02rem);
                            font-weight: 700;
                            line-height: 1.35;
                            color: #212529;
                            min-height: 2.65em;
                        }

                        .strip-price {
                            font-weight: 800;
                            letter-spacing: 0.02em;
                            font-size: clamp(0.95rem, 1.35vw, 1.08rem);
                        }

                        .strip-price--hot {
                            color: #198754;
                        }

                        .strip-price--sale {
                            color: #dc3545;
                        }

                        .strip-price-old {
                            font-size: 0.82rem;
                            color: #8b949e;
                        }

                        .hot-sale-section-title {
                            font-size: clamp(1.5rem, 3.2vw, 2.35rem);
                            font-weight: 800;
                            letter-spacing: 0.04em;
                            text-transform: uppercase;
                            color: #1a1a1a;
                        }

                        .hot-sale-section-sub {
                            text-align: center;
                            color: #5c636a;
                            font-size: clamp(1rem, 1.8vw, 1.12rem);
                            line-height: 1.5;
                            max-width: 28rem;
                            margin-left: auto;
                            margin-right: auto;
                        }
                    </style>
                    <div class="text-center mb-3 mb-md-4">
                        <h1 class="hot-sale-section-title mb-2">Sản phẩm hot</h1>
                        <p class="hot-sale-section-sub mb-0">Top sản phẩm được mua nhiều nhất trong 30 ngày gần đây.</p>
                    </div>

                    @if($sanPhamsHot->isNotEmpty())
                        <div id="hotStrip" class="product-strip" data-original-count="{{ $sanPhamsHot->count() }}">
                            <div class="strip-track">
                                @foreach($sanPhamsHot as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 strip-card strip-card--hot">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="strip-card-media text-decoration-none">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="img-fluid" alt="{{ $sp->ten_san_pham }}" loading="lazy" decoding="async">
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                <span class="badge strip-badge strip-badge--hot mb-2 align-self-start rounded-pill">Hot</span>
                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="strip-card-name text-decoration-none text-dark mb-2 flex-grow-1">
                                                    {{ $sp->ten_san_pham }}
                                                </a>
                                                <p class="mb-0 mt-auto">
                                                    @if($sp->variants_min_gia)
                                                        <span class="strip-price strip-price--hot">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</span>
                                                    @else
                                                        <span class="text-muted fw-semibold small">Liên hệ</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Lặp lại danh sách để reset index mượt hơn --}}
                                @foreach($sanPhamsHot as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 strip-card strip-card--hot">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="strip-card-media text-decoration-none">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="img-fluid" alt="{{ $sp->ten_san_pham }}" loading="lazy" decoding="async">
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                <span class="badge strip-badge strip-badge--hot mb-2 align-self-start rounded-pill">Hot</span>
                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="strip-card-name text-decoration-none text-dark mb-2 flex-grow-1">
                                                    {{ $sp->ten_san_pham }}
                                                </a>
                                                <p class="mb-0 mt-auto">
                                                    @if($sp->variants_min_gia)
                                                        <span class="strip-price strip-price--hot">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</span>
                                                    @else
                                                        <span class="text-muted fw-semibold small">Liên hệ</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-12 text-center py-5">
                            <p class="text-muted mb-3">Chưa có dữ liệu sản phẩm hot.</p>
                            <a href="{{ url('/Shop') }}" class="btn btn-success">Xem tất cả sản phẩm</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cột phải: Sản phẩm đang giảm giá -->
            <div class="col-lg-6">
                <div class="hot-sale-panel h-100 p-3 p-md-4">
                    <div class="text-center mb-3 mb-md-4">
                        <h1 class="hot-sale-section-title mb-2">Đang giảm giá</h1>
                        <p class="hot-sale-section-sub mb-0">Các sản phẩm có giá khuyến mãi</p>
                    </div>

                    @if($sanPhamsGiamGia->isNotEmpty())
                        <div id="giamGiaStrip" class="product-strip" data-original-count="{{ $sanPhamsGiamGia->count() }}">
                            <div class="strip-track">
                                @foreach($sanPhamsGiamGia as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 strip-card strip-card--sale">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="strip-card-media text-decoration-none">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="img-fluid" alt="{{ $sp->ten_san_pham }}" loading="lazy" decoding="async">
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                @php
                                                    $giaGoc = $sp->variants_min_gia ?? null;
                                                    $giaKm = $sp->variants_min_gia_khuyen_mai ?? null;
                                                    $phanTramGiam = null;
                                                    if ($giaGoc && $giaKm && $giaKm < $giaGoc && $giaGoc > 0) {
                                                        $phanTramGiam = (int) round(100 - (($giaKm / $giaGoc) * 100));
                                                    }
                                                @endphp

                                                @if($giaKm && $phanTramGiam !== null)
                                                    <span class="badge strip-badge strip-badge--sale mb-2 align-self-start rounded-pill">-{{ $phanTramGiam }}%</span>
                                                @else
                                                    <span class="badge strip-badge strip-badge--sale mb-2 align-self-start rounded-pill">Sale</span>
                                                @endif

                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="strip-card-name text-decoration-none text-dark mb-2 flex-grow-1">
                                                    {{ $sp->ten_san_pham }}
                                                </a>

                                                <div class="mt-auto">
                                                    <p class="mb-1">
                                                        @if($giaKm)
                                                            <span class="strip-price strip-price--sale">{{ number_format($giaKm, 0, ',', '.') }} ₫</span>
                                                        @else
                                                            <span class="text-muted fw-semibold small">Liên hệ</span>
                                                        @endif
                                                    </p>
                                                    @if($giaGoc)
                                                        <p class="mb-0 strip-price-old"><s>{{ number_format($giaGoc, 0, ',', '.') }} ₫</s></p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Lặp lại danh sách để reset index mượt hơn --}}
                                @foreach($sanPhamsGiamGia as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 strip-card strip-card--sale">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="strip-card-media text-decoration-none">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="img-fluid" alt="{{ $sp->ten_san_pham }}" loading="lazy" decoding="async">
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                @php
                                                    $giaGoc = $sp->variants_min_gia ?? null;
                                                    $giaKm = $sp->variants_min_gia_khuyen_mai ?? null;
                                                    $phanTramGiam = null;
                                                    if ($giaGoc && $giaKm && $giaKm < $giaGoc && $giaGoc > 0) {
                                                        $phanTramGiam = (int) round(100 - (($giaKm / $giaGoc) * 100));
                                                    }
                                                @endphp

                                                @if($giaKm && $phanTramGiam !== null)
                                                    <span class="badge strip-badge strip-badge--sale mb-2 align-self-start rounded-pill">-{{ $phanTramGiam }}%</span>
                                                @else
                                                    <span class="badge strip-badge strip-badge--sale mb-2 align-self-start rounded-pill">Sale</span>
                                                @endif

                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="strip-card-name text-decoration-none text-dark mb-2 flex-grow-1">
                                                    {{ $sp->ten_san_pham }}
                                                </a>

                                                <div class="mt-auto">
                                                    <p class="mb-1">
                                                        @if($giaKm)
                                                            <span class="strip-price strip-price--sale">{{ number_format($giaKm, 0, ',', '.') }} ₫</span>
                                                        @else
                                                            <span class="text-muted fw-semibold small">Liên hệ</span>
                                                        @endif
                                                    </p>
                                                    @if($giaGoc)
                                                        <p class="mb-0 strip-price-old"><s>{{ number_format($giaGoc, 0, ',', '.') }} ₫</s></p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-12 text-center py-5">
                            <p class="text-muted mb-3">Hiện chưa có sản phẩm đang giảm giá.</p>
                            <a href="{{ url('/Shop') }}" class="btn btn-success">Xem tất cả sản phẩm</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">

        <style>
            .featured-review-title {
                font-size: clamp(1.5rem, 3.2vw, 2.35rem);
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: #555555;
            }

            .review-text {
                color: #6c757d;
                font-size: 0.95rem;
            }

            .featured-review-card {
                border: 1.5px solid rgba(0, 0, 0, 0.18);
                border-radius: 10px;
                overflow: hidden;
            }
        </style>

        <div class="text-center mb-3">
            <h1 class="featured-review-title mb-0">Đánh giá nổi bật</h1>
        </div>

        {{-- Nút điều hướng --}}
        <div class="d-flex justify-content-center align-items-center mb-3 gap-2">
            <button class="btn btn-light border swiper-prev">←</button>
            <button class="btn btn-light border swiper-next">→</button>
        </div>

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">

                @forelse($danhGias as $dg)
                <div class="swiper-slide">

                    <div class="card shadow-sm h-100 p-4 featured-review-card">

                        <div class="mb-2 text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $dg->so_sao ? '-fill' : '' }}"></i>
                            @endfor
                        </div>

                        <h6 class="fw-bold mb-1">
                            {{ $dg->user->name ?? 'Khách hàng' }}
                            <span class="text-success">✔</span>
                        </h6>

                        <p class="review-text small mb-0">
                            "{{ $dg->noi_dung }}"
                        </p>

                    </div>

                </div>
                @empty
                    <p class="text-muted">Chưa có đánh giá nào</p>
                @endforelse

            </div>
        </div>

    </div>

    <script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 3,
        spaceBetween: 20,
        loop: true,

        navigation: {
            nextEl: ".swiper-next",
            prevEl: ".swiper-prev",
        },

        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 }
        }
    });
</script>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function initStrip(containerId) {
            const el = document.getElementById(containerId);
            if (!el) return;

            const track = el.querySelector('.strip-track');
            const items = el.querySelectorAll('.strip-item');
            if (!track || !items.length) return;

            const originalCount = parseInt(el.dataset.originalCount || (items.length / 2), 10);
            let index = 0;

            let step = items[0].getBoundingClientRect().width;
            const transitionMs = 450;

            function recalc() {
                step = items[0].getBoundingClientRect().width;
                track.style.transition = 'none';
                track.style.transform = 'translateX(' + (-index * step) + 'px)';
                requestAnimationFrame(() => {
                    track.style.transition = 'transform ' + transitionMs + 'ms ease';
                });
            }

            window.addEventListener('resize', recalc);
            track.style.transition = 'transform ' + transitionMs + 'ms ease';

            setInterval(() => {
                index += 1;
                track.style.transform = 'translateX(' + (-index * step) + 'px)';

                if (index >= originalCount) {
                    setTimeout(() => {
                        track.style.transition = 'none';
                        index = 0;
                        track.style.transform = 'translateX(0px)';
                        requestAnimationFrame(() => {
                            track.style.transition = 'transform ' + transitionMs + 'ms ease';
                        });
                    }, transitionMs + 20);
                }
            }, 2000);
        }

        initStrip('hotStrip');
        initStrip('giamGiaStrip');
    });
</script>

@include('client.layout.scripts')
@include('client.layout.footer')

