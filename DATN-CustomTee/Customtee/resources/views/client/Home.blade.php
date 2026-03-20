
@include('client.layout.header')
@include('client.layout.banner')



<!-- Danh mục nổi bật -->
<section class="py-4 bg-white">
    <div class="container py-3">
        <div class="row text-center py-2">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Danh mục nổi bật</h1>
                <p class="text-muted mb-0">Chọn danh mục để xem sản phẩm liên quan.</p>
            </div>
        </div>

        <style>
            .category-circle {
                width: 190px;
                height: 190px;
                border-radius: 50%;
                overflow: hidden;
                margin: 0 auto;
                background: #f2f2f2;
            }

            .category-circle img {
                width: 100%;
                height: 100%;
                object-fit: cover; /* crop vào hình tròn */
            }

            .category-circle:hover img {
                transform: scale(1.04);
            }

            .category-circle img {
                transition: transform 0.2s ease;
            }

            .category-item-name {
                font-weight: 600;
                margin-top: 8px;
            }
        </style>

        <div class="row">
            @forelse($danhMucs as $dm)
                <div class="col-6 col-md-4 col-lg-4 mb-4">
                    <a href="{{ url('/Shop?danh_muc=' . $dm->id) }}" class="text-decoration-none">
                        <div class="category-circle shadow-sm">
                            <img
                                src="{{ $dm->hinh_anh ? asset('storage/' . $dm->hinh_anh) : asset('img/shop_01.jpg') }}"
                                alt="{{ $dm->ten_danh_muc }}"
                            >
                        </div>
                    </a>

                    <div class="text-center">
                        <div class="category-item-name">
                            {{ $dm->ten_danh_muc }}
                        </div>
                        <a href="{{ url('/Shop?danh_muc=' . $dm->id) }}" class="btn btn-success btn-sm mt-2">
                            Go Shop
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">Chưa có danh mục.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

    <!-- Sản phẩm mới nhất -->
    <section class="bg-light">
        <div class="container py-4">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Sản phẩm mới nhất</h1>
                    <p class="text-muted mb-0">
                        Những sản phẩm mới được cập nhật, mời bạn khám phá.
                    </p>
                </div>
            </div>
            <div class="row">
                @forelse($sanPhamsMoiNhat as $sp)
                <div class="col-6 col-md-3 col-lg-2 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                            <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                class="card-img-top img-fluid" alt="{{ $sp->ten_san_pham }}" style="object-fit: cover; height: 140px;">
                        </a>
                        <div class="card-body p-2">
                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="h6 text-decoration-none text-dark d-block mb-1"
                                style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                {{ $sp->ten_san_pham }}
                            </a>
                            <p class="mb-0">
                                @if($sp->variants_min_gia)
                                    <strong class="text-success" style="font-size: 0.95rem;">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</strong>
                                @else
                                    <span class="text-muted">Liên hệ</span>
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
            <div class="text-center mt-3">
                <a href="{{ url('/Shop') }}" class="btn btn-outline-success">Xem tất cả sản phẩm</a>
            </div>
            @endif
        </div>
    </section>

<!-- Sản phẩm Hot & Giảm giá -->
<section class="py-4 bg-light">
    <div class="container py-3">
        <div class="row">
            <!-- Cột trái: Sản phẩm hot -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="border rounded-3 bg-white p-3 h-100">
                    <style>
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
                            box-sizing: border-box;
                        }

                        /* Hiển thị 3 sản phẩm/khung khi đủ rộng */
                        @media (min-width: 768px) {
                            .strip-item {
                                flex: 0 0 33.333333%;
                            }
                        }

                        .strip-card-img {
                            height: 140px;
                            object-fit: cover;
                        }
                    </style>
                    <div class="text-center mb-3">
                        <h1 class="h1">Sản phẩm hot</h1>
                        <p class="text-muted mb-0">Top sản phẩm được mua nhiều nhất trong 30 ngày gần đây.</p>
                    </div>

                    @if($sanPhamsHot->isNotEmpty())
                        <div id="hotStrip" class="product-strip" data-original-count="{{ $sanPhamsHot->count() }}">
                            <div class="strip-track">
                                @foreach($sanPhamsHot as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="card-img-top img-fluid strip-card-img" alt="{{ $sp->ten_san_pham }}">
                                            </a>
                                            <div class="card-body p-2">
                                                <span class="badge bg-success mb-2">Hot</span>
                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="h6 text-decoration-none text-dark d-block mb-1"
                                                    style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                                    {{ $sp->ten_san_pham }}
                                                </a>
                                                <p class="mb-0">
                                                    @if($sp->variants_min_gia)
                                                        <strong class="text-success" style="font-size: 0.95rem;">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</strong>
                                                    @else
                                                        <span class="text-muted">Liên hệ</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Lặp lại danh sách để reset index mượt hơn --}}
                                @foreach($sanPhamsHot as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="card-img-top img-fluid strip-card-img" alt="{{ $sp->ten_san_pham }}">
                                            </a>
                                            <div class="card-body p-2">
                                                <span class="badge bg-success mb-2">Hot</span>
                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="h6 text-decoration-none text-dark d-block mb-1"
                                                    style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                                    {{ $sp->ten_san_pham }}
                                                </a>
                                                <p class="mb-0">
                                                    @if($sp->variants_min_gia)
                                                        <strong class="text-success" style="font-size: 0.95rem;">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</strong>
                                                    @else
                                                        <span class="text-muted">Liên hệ</span>
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
                <div class="border rounded-3 bg-white p-3 h-100">
                    <div class="text-center mb-3">
                        <h1 class="h1">Đang giảm giá</h1>
                        <p class="text-muted mb-0">Các sản phẩm có giá khuyến mãi từ các biến thể còn hiệu lực.</p>
                    </div>

                    @if($sanPhamsGiamGia->isNotEmpty())
                        <div id="giamGiaStrip" class="product-strip" data-original-count="{{ $sanPhamsGiamGia->count() }}">
                            <div class="strip-track">
                                @foreach($sanPhamsGiamGia as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="card-img-top img-fluid strip-card-img" alt="{{ $sp->ten_san_pham }}">
                                            </a>
                                            <div class="card-body p-2">
                                                @php
                                                    $giaGoc = $sp->variants_min_gia ?? null;
                                                    $giaKm = $sp->variants_min_gia_khuyen_mai ?? null;
                                                    $phanTramGiam = null;
                                                    if ($giaGoc && $giaKm && $giaKm < $giaGoc && $giaGoc > 0) {
                                                        $phanTramGiam = (int) round(100 - (($giaKm / $giaGoc) * 100));
                                                    }
                                                @endphp

                                                @if($giaKm && $phanTramGiam !== null)
                                                    <span class="badge bg-danger mb-2">-{{ $phanTramGiam }}%</span>
                                                @else
                                                    <span class="badge bg-success mb-2">Sale</span>
                                                @endif

                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="h6 text-decoration-none text-dark d-block mb-1"
                                                    style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                                    {{ $sp->ten_san_pham }}
                                                </a>

                                                <p class="mb-1">
                                                    @if($giaKm)
                                                        <strong class="text-danger" style="font-size: 0.95rem;">{{ number_format($giaKm, 0, ',', '.') }} ₫</strong>
                                                    @else
                                                        <span class="text-muted">Liên hệ</span>
                                                    @endif
                                                </p>

                                                @if($giaGoc)
                                                    <p class="mb-0">
                                                        <s class="text-muted">{{ number_format($giaGoc, 0, ',', '.') }} ₫</s>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Lặp lại danh sách để reset index mượt hơn --}}
                                @foreach($sanPhamsGiamGia as $sp)
                                    <div class="strip-item">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                                                <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                                    class="card-img-top img-fluid strip-card-img" alt="{{ $sp->ten_san_pham }}">
                                            </a>
                                            <div class="card-body p-2">
                                                @php
                                                    $giaGoc = $sp->variants_min_gia ?? null;
                                                    $giaKm = $sp->variants_min_gia_khuyen_mai ?? null;
                                                    $phanTramGiam = null;
                                                    if ($giaGoc && $giaKm && $giaKm < $giaGoc && $giaGoc > 0) {
                                                        $phanTramGiam = (int) round(100 - (($giaKm / $giaGoc) * 100));
                                                    }
                                                @endphp

                                                @if($giaKm && $phanTramGiam !== null)
                                                    <span class="badge bg-danger mb-2">-{{ $phanTramGiam }}%</span>
                                                @else
                                                    <span class="badge bg-success mb-2">Sale</span>
                                                @endif

                                                <a href="{{ route('sanpham.chitiet', $sp->slug) }}"
                                                    class="h6 text-decoration-none text-dark d-block mb-1"
                                                    style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                                    {{ $sp->ten_san_pham }}
                                                </a>

                                                <p class="mb-1">
                                                    @if($giaKm)
                                                        <strong class="text-danger" style="font-size: 0.95rem;">{{ number_format($giaKm, 0, ',', '.') }} ₫</strong>
                                                    @else
                                                        <span class="text-muted">Liên hệ</span>
                                                    @endif
                                                </p>

                                                @if($giaGoc)
                                                    <p class="mb-0">
                                                        <s class="text-muted">{{ number_format($giaGoc, 0, ',', '.') }} ₫</s>
                                                    </p>
                                                @endif
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

