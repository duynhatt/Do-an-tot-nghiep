@include('client.layout.header')

<div class="container my-5">
    <div class="row g-5">
        <div class="col-md-5">
            <div class="border rounded-4 p-3 bg-white shadow-sm">
                <img src="{{ asset('storage/' . $sanPham->hinh_anh_chinh) }}"
                    class="img-fluid rounded-4 w-100"
                    alt="{{ $sanPham->ten_san_pham }}">
            </div>
        </div>

        <div class="col-md-7">
            <h2 class="fw-bold mb-3">{{ $sanPham->ten_san_pham }}</h2>

            <h3 class="text-danger fw-bold mb-3" id="gia-san-pham">
                {{ number_format($giaMacDinh->gia_khuyen_mai ?? $giaMacDinh->gia) }} đ
            </h3>

            <p class="text-secondary mb-4">
                {{ $sanPham->mo_ta_ngan }}
            </p>

            <div class="mb-3">
                <label class="fw-semibold mb-2 d-block">Màu sắc</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($sanPham->variants->unique('mau_sac_id') as $variant)
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm color-btn"
                        data-color="{{ $variant->mau_sac_id }}">
                        {{ $variant->color->ten_mau }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <label class="fw-semibold mb-2 d-block">Kích thước</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($sanPham->variants->unique('kich_thuoc_id') as $variant)
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm size-btn"
                        data-size="{{ $variant->kich_thuoc_id }}">
                        {{ $variant->size->ten_kich_thuoc }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center">
                <label class="me-3 fw-semibold">Số lượng</label>
                <input type="number" class="form-control w-25" min="1" value="1">
            </div>

            <div class="d-flex gap-3">
                <button class="btn btn-success px-4">
                    <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                </button>

                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                    Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

@include('client.layout.footer')
@include('client.layout.scripts')