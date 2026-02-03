@extends('admin.layout.AdminLayout')

@section('AdminContent')

<h3 style="margin-bottom:20px;">Cập nhật biến thể</h3>

<form action="{{ route('variants.update', $variant->id) }}" method="POST" style="max-width:600px;">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SẢN PHẨM --}}
    <div class="form-group">
        <label>Sản phẩm</label>
        <select name="san_pham_id" id="productSelect" class="form-control">
            @foreach($products as $p)
                <option value="{{ $p->id }}"
                        data-img="{{ $p->hinh_anh_chinh ? asset('storage/' . $p->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                        data-cat="{{ $p->category->ten_danh_muc ?? '' }}"
                        {{ $variant->san_pham_id == $p->id ? 'selected' : '' }}>
                    {{ $p->ten_san_pham }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PREVIEW SẢN PHẨM --}}
    <div class="product-info-box">
        <img id="previewImg"
             src="{{ $variant->product->hinh_anh_chinh ? asset('storage/' . $variant->product->hinh_anh_chinh) : asset('img/shop_01.jpg') }}">
        <div><b>Danh mục:</b> <span id="productCat">{{ $variant->product->category->ten_danh_muc ?? '-' }}</span></div>
    </div>

    {{-- MÀU --}}
    <div class="form-group">
        <label>Màu</label>
        <select name="mau_sac_id" class="form-control">
            @foreach($colors as $c)
                <option value="{{ $c->id }}" {{ $variant->mau_sac_id == $c->id ? 'selected' : '' }}>
                    {{ $c->ten_mau }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- SIZE --}}
    <div class="form-group">
        <label>Size</label>
        <select name="kich_thuoc_id" class="form-control">
            @foreach($sizes as $s)
                <option value="{{ $s->id }}" {{ $variant->kich_thuoc_id == $s->id ? 'selected' : '' }}>
                    {{ $s->ten_kich_thuoc }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- GIÁ --}}
    <div class="form-group">
        <label>Giá</label>
        <input type="number" name="gia" class="form-control @error('gia') is-invalid @enderror" value="{{ old('gia', $variant->gia) }}" min="0">
        @error('gia')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- GIÁ KM --}}
    <div class="form-group">
        <label>Giá khuyến mãi</label>
        <input type="number" name="gia_khuyen_mai" class="form-control @error('gia_khuyen_mai') is-invalid @enderror" value="{{ old('gia_khuyen_mai', $variant->gia_khuyen_mai) }}" min="0">
        @error('gia_khuyen_mai')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- SỐ LƯỢNG --}}
    <div class="form-group">
        <label>Số lượng</label>
        <input type="number" name="so_luong" class="form-control @error('so_luong') is-invalid @enderror" value="{{ old('so_luong', $variant->so_luong) }}" min="0">
        @error('so_luong')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- TRẠNG THÁI --}}
    <div class="form-group">
        <label>Trạng thái</label>
        <select name="trang_thai" class="form-control">
            <option value="1" {{ $variant->trang_thai ? 'selected' : '' }}>Hiện</option>
            <option value="0" {{ !$variant->trang_thai ? 'selected' : '' }}>Ẩn</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Cập nhật</button>
</form>


<style>
.form-group{
    margin-bottom:15px;
}
.product-info-box{
    margin:15px 0;
    padding:10px;
    border:1px solid #ddd;
    background:#f9f9f9;
}
#previewImg{
    width:120px;
    margin-bottom:10px;
    border-radius:6px;
}
</style>


<script>
const select = document.getElementById('productSelect');
const img = document.getElementById('previewImg');
const cat = document.getElementById('productCat');

function updateInfo() {
    const opt = select.options[select.selectedIndex];
    img.src = opt.dataset.img;
    cat.textContent = opt.dataset.cat;
}

select.addEventListener('change', updateInfo);
updateInfo();
</script>

@endsection
