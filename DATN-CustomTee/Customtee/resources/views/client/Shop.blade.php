@include('client.layout.header')
@php
    $selectedDanhMucs = collect((array) request()->input('danh_muc', []))->map(fn($id) => (int) $id)->all();
    $selectedSizes = collect((array) request()->input('size', []))->map(fn($id) => (int) $id)->all();
    $selectedColors = collect((array) request()->input('color', []))->map(fn($id) => (int) $id)->all();
    $selectedSort = request('sort');
    $hasAnyFilter = !empty($selectedDanhMucs)
        || !empty($selectedSizes)
        || !empty($selectedColors)
        || request()->filled('min_price')
        || request()->filled('max_price')
        || !empty($selectedSort);
@endphp
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm border-0" id="shopFilterSidebar">
                <div class="card-body">
                    <form action="{{ url('/Shop') }}" method="get" id="shopFilterForm">
                        @if(!empty($tuKhoa))
                            <input type="hidden" name="q" value="{{ $tuKhoa }}">
                        @endif

                        {{-- DANH MỤC --}}
                        <button class="filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#categoryCollapse">
                            Danh mục
                        </button>

                        <div id="categoryCollapse" class="collapse show">
                            <ul class="list-unstyled mt-2 mb-1">
                                @foreach($danhMucs as $danhMuc)
                                    @php
                                        $isActiveCategory = in_array((int) $danhMuc->id, $selectedDanhMucs, true);
                                        $categoryQuery = request()->query();
                                        unset($categoryQuery['page']);
                                        $categoryQuery['danh_muc'] = [$danhMuc->id];
                                        $categoryUrl = url('/Shop') . '?' . http_build_query($categoryQuery);
                                    @endphp
                                    <li class="mb-2">
                                        <a href="{{ $categoryUrl }}"
                                           data-ajax-link="true"
                                           class="category-hover-link {{ $isActiveCategory ? 'active' : '' }}">
                                            {{ $danhMuc->ten_danh_muc }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- KHOẢNG GIÁ --}}
                        <button class="filter-toggle mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#priceCollapse">
                            Khoảng giá
                        </button>

                        <div id="priceCollapse" class="collapse show">
                            <div class="mb-2">
                                <label for="min_price" class="form-label small mb-1">Giá từ</label>
                                <input type="number"
                                       id="min_price"
                                       name="min_price"
                                       min="0"
                                       step="1"
                                       class="form-control form-control-sm"
                                       value="{{ request('min_price') }}"
                                       placeholder="{{ (int) $minPrice }}">
                            </div>

                            <div class="mb-2">
                                <label for="max_price" class="form-label small mb-1">Đến</label>
                                <input type="number"
                                       id="max_price"
                                       name="max_price"
                                       min="0"
                                       step="1"
                                       class="form-control form-control-sm"
                                       value="{{ request('max_price') }}"
                                       placeholder="{{ (int) $maxPrice }}">
                            </div>
                        </div>

                        {{-- SIZE --}}
                        <button class="filter-toggle mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#sizeCollapse">
                            Kích thước
                        </button>

                        <div id="sizeCollapse" class="collapse show">
                            <ul class="list-unstyled mt-2 mb-1">
                                @foreach($sizes as $size)
                                    <li class="mb-2">
                                        <label class="d-flex align-items-center gap-2 mb-0 filter-check-label">
                                            <input type="checkbox"
                                                   class="form-check-input m-0"
                                                   name="size[]"
                                                   value="{{ $size->id }}"
                                                   {{ in_array((int) $size->id, $selectedSizes, true) ? 'checked' : '' }}>
                                            <span>{{ $size->ten_kich_thuoc }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- MÀU --}}
                        <button class="filter-toggle mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#colorCollapse">
                            Màu sắc
                        </button>

                        <div id="colorCollapse" class="collapse show">
                            <ul class="list-unstyled mt-2 mb-1">
                                @foreach($colors as $color)
                                    <li class="mb-2">
                                        <label class="d-flex align-items-center gap-2 mb-0 filter-check-label">
                                            <input type="checkbox"
                                                   class="form-check-input m-0"
                                                   name="color[]"
                                                   value="{{ $color->id }}"
                                                   {{ in_array((int) $color->id, $selectedColors, true) ? 'checked' : '' }}>
                                            <span>{{ $color->ten_mau }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- SẮP XẾP --}}
                        <button class="filter-toggle mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#sortCollapse">
                            Sắp xếp
                        </button>

                        <div id="sortCollapse" class="collapse show">
                            <select name="sort" class="form-select form-select-sm mt-2">
                                <option value="">Mặc định</option>
                                <option value="price_asc" {{ $selectedSort === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ $selectedSort === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                                <option value="new" {{ $selectedSort === 'new' ? 'selected' : '' }}>Mới nhất</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-success btn-sm">Áp dụng bộ lọc</button>
                            @if($hasAnyFilter)
                                <a href="{{ url('/Shop') . (!empty($tuKhoa) ? '?q=' . urlencode($tuKhoa) : '') }}"
                                   class="btn btn-outline-secondary btn-sm"
                                   data-ajax-link="true">Xóa bộ lọc</a>
                            @endif
                        </div>
                    </form>
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

            .filter-check-label {
                cursor: pointer;
                font-size: 14px;
            }

            .filter-check-label span {
                line-height: 1.2;
            }

            .active-filter-chip {
                background: #f3f8f4;
                border: 1px solid #d3e9d6;
                color: #1f5130;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 13px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .active-filter-chip:hover {
                background: #e7f5ea;
                color: #0f3c21;
            }

            .shop-loading {
                opacity: 0.55;
                pointer-events: none;
                transition: opacity 0.2s ease;
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

            .category-hover-link {
                display: block;
                color: #333;
                text-decoration: none;
                border-radius: 8px;
                padding: 8px 10px;
                transition: all 0.2s ease;
                border: 1px solid transparent;
            }

            .category-hover-link:hover {
                background: #f3f8f4;
                border-color: #d3e9d6;
                color: #1f5130;
                transform: translateX(4px);
            }

            .category-hover-link.active {
                background: #eaf6ec;
                border-color: #bde0c4;
                color: #1b5e20;
                font-weight: 600;
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

        <div class="col-lg-9" id="shopContent">
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
                    <form action="{{ url('/Shop') }}" method="get" class="d-flex justify-content-end" id="shopSearchForm">
                        @foreach(request()->except(['q', 'page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
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
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <p class="mb-0 text-muted small" id="shopResultsCount">Hiển thị {{ $sanPhams->count() }} / {{ $sanPhams->total() }} sản phẩm</p>
                <div class="d-flex flex-wrap gap-2" id="shopActiveFilters">
                    @if($hasAnyFilter)
                        @foreach($danhMucs as $danhMuc)
                            @if(in_array((int) $danhMuc->id, $selectedDanhMucs, true))
                                <a class="active-filter-chip"
                                   data-ajax-link="true"
                                   href="{{ request()->fullUrlWithQuery(['danh_muc' => array_values(array_diff($selectedDanhMucs, [(int) $danhMuc->id])), 'page' => null]) }}">
                                    Danh mục: {{ $danhMuc->ten_danh_muc }} <span>&times;</span>
                                </a>
                            @endif
                        @endforeach

                        @foreach($sizes as $size)
                            @if(in_array((int) $size->id, $selectedSizes, true))
                                <a class="active-filter-chip"
                                   data-ajax-link="true"
                                   href="{{ request()->fullUrlWithQuery(['size' => array_values(array_diff($selectedSizes, [(int) $size->id])), 'page' => null]) }}">
                                    Size: {{ $size->ten_kich_thuoc }} <span>&times;</span>
                                </a>
                            @endif
                        @endforeach

                        @foreach($colors as $color)
                            @if(in_array((int) $color->id, $selectedColors, true))
                                <a class="active-filter-chip"
                                   data-ajax-link="true"
                                   href="{{ request()->fullUrlWithQuery(['color' => array_values(array_diff($selectedColors, [(int) $color->id])), 'page' => null]) }}">
                                    Màu: {{ $color->ten_mau }} <span>&times;</span>
                                </a>
                            @endif
                        @endforeach

                        @if(request()->filled('min_price') || request()->filled('max_price'))
                            <a class="active-filter-chip"
                               data-ajax-link="true"
                               href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}">
                                Giá: {{ request('min_price', 0) }} - {{ request('max_price', '...') }} <span>&times;</span>
                            </a>
                        @endif

                        @if(!empty($selectedSort))
                            <a class="active-filter-chip"
                               data-ajax-link="true"
                               href="{{ request()->fullUrlWithQuery(['sort' => null, 'page' => null]) }}">
                                Sắp xếp: {{ $selectedSort }} <span>&times;</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="row" id="shopProductsGrid">
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
<div class="row mt-5" id="shopPaginationWrap">
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
    (function () {
        const sidebar = document.getElementById('shopFilterSidebar');
        const content = document.getElementById('shopContent');
        if (!sidebar || !content) return;

        let loading = false;

        const setLoading = (isLoading) => {
            loading = isLoading;
            sidebar.classList.toggle('shop-loading', isLoading);
            content.classList.toggle('shop-loading', isLoading);
        };

        const updateFromHtml = (html, nextUrl, pushState = true) => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nextSidebar = doc.getElementById('shopFilterSidebar');
            const nextContent = doc.getElementById('shopContent');
            if (!nextSidebar || !nextContent) {
                window.location.href = nextUrl;
                return;
            }

            sidebar.outerHTML = nextSidebar.outerHTML;
            content.outerHTML = nextContent.outerHTML;

            if (pushState) {
                window.history.pushState({}, '', nextUrl);
            }
            bindAjaxEvents();
        };

        const ajaxNavigate = async (nextUrl, pushState = true) => {
            if (loading) return;
            setLoading(true);
            try {
                const response = await fetch(nextUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                updateFromHtml(html, nextUrl, pushState);
            } catch (e) {
                window.location.href = nextUrl;
            } finally {
                setLoading(false);
            }
        };

        const buildUrlFromForm = (form) => {
            const action = form.getAttribute('action') || window.location.pathname;
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value !== null && String(value).trim() !== '') {
                    params.append(key, value);
                }
            }
            params.delete('page');

            const query = params.toString();
            return query ? `${action}?${query}` : action;
        };

        function bindAjaxEvents() {
            const filterForm = document.getElementById('shopFilterForm');
            const searchForm = document.getElementById('shopSearchForm');
            const paginationWrap = document.getElementById('shopPaginationWrap');
            const ajaxLinks = document.querySelectorAll('a[data-ajax-link="true"]');

            if (filterForm) {
                filterForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    ajaxNavigate(buildUrlFromForm(filterForm));
                });

                const autoSubmitInputs = filterForm.querySelectorAll('input[type="checkbox"], select[name="sort"]');
                autoSubmitInputs.forEach((input) => {
                    input.addEventListener('change', () => {
                        ajaxNavigate(buildUrlFromForm(filterForm));
                    });
                });
            }

            if (searchForm) {
                searchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    ajaxNavigate(buildUrlFromForm(searchForm));
                });
            }

            if (paginationWrap) {
                paginationWrap.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        ajaxNavigate(this.href);
                    });
                });
            }

            ajaxLinks.forEach((link) => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    ajaxNavigate(this.href);
                });
            });
        }

        window.addEventListener('popstate', () => {
            ajaxNavigate(window.location.href, false);
        });

        bindAjaxEvents();
    })();
</script>
@include('client.layout.scripts')
@include('client.layout.footer')