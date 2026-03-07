
@include('client.layout.header')
@include('client.layout.banner')



    <!-- Sản phẩm mới nhất -->
    <section class="bg-light">
        <div class="container py-5">
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
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="{{ route('sanpham.chitiet', $sp->slug) }}">
                            <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                                class="card-img-top img-fluid" alt="{{ $sp->ten_san_pham }}" style="object-fit: cover; height: 260px;">
                        </a>
                        <div class="card-body">
                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="h5 text-decoration-none text-dark d-block mb-2">
                                {{ $sp->ten_san_pham }}
                            </a>
                            @if($sp->mo_ta_ngan)
                                <p class="card-text text-muted small mb-2">{{ Str::limit($sp->mo_ta_ngan, 80) }}</p>
                            @endif
                            <p class="mb-0">
                                @if($sp->variants_min_gia)
                                    <strong class="text-success">{{ number_format($sp->variants_min_gia, 0, ',', '.') }} ₫</strong>
                                @else
                                    <span class="text-muted">Liên hệ</span>
                                @endif
                            </p>
                            <a href="{{ route('sanpham.chitiet', $sp->slug) }}" class="btn btn-success mt-2">Xem chi tiết</a>
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



@include('client.layout.scripts')
@include('client.layout.footer')

