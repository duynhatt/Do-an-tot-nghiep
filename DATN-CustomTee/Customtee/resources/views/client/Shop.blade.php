@include('client.layout.header')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    {{-- DANH MỤC --}}
                    <button class="filter-toggle" data-bs-toggle="collapse" data-bs-target="#categoryCollapse">
                        Danh mục
                    </button>

                    <div id="categoryCollapse" class="collapse show">
                        <ul class="list-unstyled mt-2">
                            @foreach($danhMucs as $danhMuc)
                            <li class="mb-2">
                                <a class="filter-link"
                                href="{{ request()->fullUrlWithQuery(['danh_muc' => $danhMuc->id]) }}">
                                    {{ $danhMuc->ten_danh_muc }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- KHOẢNG GIÁ --}}
                    <button class="filter-toggle mt-3" data-bs-toggle="collapse" data-bs-target="#priceCollapse">
                        Khoảng giá
                    </button>

                    <div id="priceCollapse" class="collapse show">
                        <div class="mt-2">
                            <input type="range" id="priceRange"
                                min="{{ $minPrice }}"
                                max="{{ $maxPrice }}"
                                value="{{ request('max_price', $maxPrice) }}"
                                class="form-range">

                            <div class="d-flex justify-content-between small">
                                <span id="minValue">{{ number_format($minPrice) }}đ</span>
                                <span id="maxValue">{{ number_format(request('max_price', $maxPrice)) }}đ</span>
                            </div>

                            <button onclick="filterPrice()" class="btn btn-success btn-sm w-100 mt-2">
                                Áp dụng
                            </button>
                        </div>
                    </div>

                    {{-- SIZE --}}
                    <button class="filter-toggle mt-3" data-bs-toggle="collapse" data-bs-target="#sizeCollapse">
                        Kích thước
                    </button>

                    <div id="sizeCollapse" class="collapse">
                        <ul class="list-unstyled mt-2">
                            @foreach($sizes as $size)
                            <li class="mb-2">
                                <a class="filter-link"
                                href="{{ request()->fullUrlWithQuery(['size' => $size->ten_kich_thuoc]) }}">
                                    {{ $size->ten_kich_thuoc }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- MÀU --}}
                    <button class="filter-toggle mt-3" data-bs-toggle="collapse" data-bs-target="#colorCollapse">
                        Màu sắc
                    </button>

                    <div id="colorCollapse" class="collapse">
                        <ul class="list-unstyled mt-2">
                            @foreach($colors as $color)
                            <li class="mb-2">
                                <a class="filter-link"
                                href="{{ request()->fullUrlWithQuery(['color' => $color->id]) }}">
                                    {{ $color->ten_mau }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- SẮP XẾP --}}
                    <button class="filter-toggle mt-3" data-bs-toggle="collapse" data-bs-target="#sortCollapse">
                        Sắp xếp
                    </button>

                    <div id="sortCollapse" class="collapse">
                        <ul class="list-unstyled mt-2">
                            <li class="mb-2">
                                <a class="filter-link" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">
                                    Giá tăng dần
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="filter-link" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">
                                    Giá giảm dần
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="filter-link" href="{{ request()->fullUrlWithQuery(['sort' => 'new']) }}">
                                    Mới nhất
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <style>
            .filter-toggle {
                width: 100%;
                text-align: left;
                background: none;
                border: none;
                font-weight: 600;
                padding: 8px 0;
                border-bottom: 1px solid #eee;
                position: relative;
            }

            .filter-toggle::after {
                content: "▾";
                position: absolute;
                right: 0;
                transition: transform 0.3s;
            }

            .filter-toggle[aria-expanded="true"]::after {
                transform: rotate(180deg);
            }

            .filter-link {
                display: block;
                padding: 5px 8px;
                border-radius: 6px;
                color: #333;
                text-decoration: none;
            }

            .filter-link:hover {
                background: #f1f1f1;
            }


            .category-link {
                color: #000000 !important;
                display: block !important;
                transition: all 0.3s ease;
                font-size: 24px !important;
            }

            .category-link:hover {
                color: #28a745 !important;
                padding-left: 10px !important;
            }

            /* Đồng bộ chiều cao card sản phẩm */
            .product-wap {
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .product-wap > .card {
                border: 0;
            }

            .product-wap .card-body {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }

            .product-wap .product-title {
                min-height: 56px; /* giữ phần tên 2 dòng cho đều */
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>

        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-inline shop-top-menu pb-3 pt-1">
                        <li class="list-inline-item">
                            <a class="h3 text-dark text-decoration-none mr-3" href="{{ url('/Shop') }}">Tất cả</a>
                        </li>
                    </ul>
                    @if(!empty($tuKhoa))
                        <p class="mb-0 text-muted">Kết quả tìm kiếm cho: <strong>"{{ $tuKhoa }}"</strong></p>
                    @endif
                </div>
                <div class="col-md-6 pb-4">
                    <form action="{{ url('/Shop') }}" method="get" class="d-flex justify-content-end">
                        @if(request('danh_muc'))
                            <input type="hidden" name="danh_muc" value="{{ request('danh_muc') }}">
                        @endif
                        <div class="input-group" style="max-width: 280px;">
                            <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..."
value="{{ old('q', $tuKhoa ?? request('q')) }}">
                            <button type="submit" class="btn btn-success">
                                Tìm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                @forelse($sanPhams as $sp)
                <div class="col-md-4 mb-4">
                    <div class="card product-wap rounded-0">
                        <div class="card rounded-0">
                            <img class="card-img rounded-0 img-fluid" src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}" alt="{{ $sp->ten_san_pham }}">
                            <div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                <ul class="list-unstyled">
                                    <li><a class="btn btn-success text-white"  href="{{ route('sanpham.chitiet', $sp->slug) }}"><i class="far fa-heart"></i></a></li>
                                    <li>
                                        <a class="btn btn-success text-white mt-2"
                                            href="{{ route('sanpham.chitiet', $sp->slug) }}">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    </li>
                                    <li><a class="btn btn-success text-white mt-2"  href="{{ route('sanpham.chitiet', $sp->slug) }}"><i class="fas fa-cart-plus"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <a  href="{{ route('sanpham.chitiet', $sp->slug) }}" class="h3 text-decoration-none product-title">{{ $sp->ten_san_pham }}</a>
                            {{-- <ul class="w-100 list-unstyled d-flex justify-content-between mb-0">
                                    <li class="text-muted small">{{ $sp->category->ten_danh_muc ?? '' }}</li>
                            </ul> --}}
                            <p class="text-center mb-0 mt-2">
                                @if($sp->variants_min_gia)
                                {{ number_format($sp->variants_min_gia, 0, ',', '.') }}đ
                                @else
                                Liên hệ
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
             @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Chưa có sản phẩm nào trong danh mục này.</p>
                    <a href="{{ url('/Shop') }}" class="btn btn-success">Xem tất cả sản phẩm</a>
                </div>
                @endforelse
            </div>
<div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $sanPhams->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </div>
</div>

<section class="bg-light py-5">
    <div class="container my-4">
        <div class="row text-center py-3">
            <div class="col-lg-6 m-auto">
                <h1 class="h1">Our Brands</h1>
            </div>
            <div class="col-lg-9 m-auto tempaltemo-carousel">
                <div class="row d-flex flex-row">
                    <!--Controls-->
                    <div class="col-1 align-self-center">
                        <a class="h1" href="#multi-item-example" role="button" data-bs-slide="prev">
                            <i class="text-light fas fa-chevron-left"></i>
                        </a>
                    </div>
                    <!--End Controls-->

                    <!--Carousel Wrapper-->
                    <div class="col">
                        <div class="carousel slide carousel-multi-item pt-2 pt-md-0" id="multi-item-example" data-bs-ride="carousel">
                            <!--Slides-->
                            <div class="carousel-inner product-links-wap" role="listbox">

                                <!--First slide-->
                                <div class="carousel-item active">
                                    <div class="row">
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_01.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_02.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_03.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_04.png" alt="Brand Logo"></a>
                                        </div>
                                    </div>
                                </div>
                                <!--End First slide-->

                                <!--Second slide-->
                                <div class="carousel-item">
                                    <div class="row">
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_01.png" alt="Brand Logo"></a>
</div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_02.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_03.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_04.png" alt="Brand Logo"></a>
                                        </div>
                                    </div>
                                </div>
                                <!--End Second slide-->

                                <!--Third slide-->
                                <div class="carousel-item">
                                    <div class="row">
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_01.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_02.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_03.png" alt="Brand Logo"></a>
                                        </div>
                                        <div class="col-3 p-md-5">
                                            <a href="#"><img class="img-fluid brand-img" src=" /img/brand_04.png" alt="Brand Logo"></a>
                                        </div>
                                    </div>
                                </div>
                                <!--End Third slide-->

                            </div>
                            <!--End Slides-->
                        </div>
                    </div>
                    <!--End Carousel Wrapper-->

                    <!--Controls-->
                    <div class="col-1 align-self-center">
                        <a class="h1" href="#multi-item-example" role="button" data-bs-slide="next">
                            <i class="text-light fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <!--End Controls-->
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const slider = document.getElementById("priceRange");
    const maxValue = document.getElementById("maxValue");

    slider.oninput = function () {
        maxValue.innerText = new Intl.NumberFormat('vi-VN').format(this.value) + 'đ';
    };

    function filterPrice() {
        let max = slider.value;

        let url = new URL(window.location.href);
        url.searchParams.set('min_price', 0);
        url.searchParams.set('max_price', max);

        window.location.href = url.toString();
    }
</script>
@include('client.layout.scripts')
@include('client.layout.footer')