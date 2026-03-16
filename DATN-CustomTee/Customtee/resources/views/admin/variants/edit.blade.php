@extends('admin.layout.AdminLayout')

@section('AdminContent')
<style>
    .form-group{
    margin-bottom:15px;
    width: 1400px;
}

#variantsContainer .variant-row{
    padding:10px;
    border:1px dashed #ddd;
    margin-bottom:10px;
    background:#fcfcfc;
}
.variant-header{
    font-size:12px;
    color:#666;
    margin-bottom:6px;
}
.variant-header .col-md-1,
.variant-header .col-md-2,
.variant-header .col-md-3{
    padding-top:2px;
    padding-bottom:2px;
}
.variant-actions{
    margin-top:8px;
}

</style>
<div class="d-flex justify-content-between align-items-center" style="margin-bottom:20px;">
    <h3 class="mb-0">Cập nhật biến thể</h3>
    <a href="{{ route('admin.san-pham.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Danh sách sản phẩm
    </a>
</div>

<form action="{{ route('variants.update', $variant->id) }}" method="POST" style="max-width:900px;">
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

    <input type="hidden" name="san_pham_id" value="{{ $product->id }}">

    {{-- SẢN PHẨM --}}
    <div class="form-group">
        <label>Sản phẩm</label>
        <select id="productSelect" class="form-control" disabled>
            @foreach($products as $p)
                <option value="{{ $p->id }}"
                        data-img="{{ $p->hinh_anh_chinh ? asset('storage/' . $p->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                        data-cat="{{ $p->category->ten_danh_muc ?? '' }}"
                        {{ $product->id == $p->id ? 'selected' : '' }}>
                    {{ $p->ten_san_pham }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Sửa biến thể sản phẩm.</small>
    </div>

    {{-- PREVIEW SẢN PHẨM --}}
    <div class="product-info-box">
        <img id="previewImg"
             src="{{ $product->hinh_anh_chinh ? asset('storage/' . $product->hinh_anh_chinh) : asset('img/shop_01.jpg') }}">
        <div><b>Danh mục:</b> <span id="productCat">{{ $product->category->ten_danh_muc ?? '-' }}</span></div>
    </div>

    @php
        $oldVariants = old('variants');
        if (!$oldVariants) {
            $oldVariants = $product->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'mau_sac_id' => $v->mau_sac_id,
                    'kich_thuoc_id' => $v->kich_thuoc_id,
                    'gia' => $v->gia,
                    'gia_khuyen_mai' => $v->gia_khuyen_mai,
                    'so_luong' => $v->so_luong,
                    'trang_thai' => $v->trang_thai ? '1' : '0',
                ];
            })->values()->all();
        }
    @endphp

    <div class="form-group">
        <label>Danh sách biến thể</label>
        <div class="row variant-header">
            <div class="col-md-3">Màu</div>
            <div class="col-md-2">Size</div>
            <div class="col-md-2">Giá</div>
            <div class="col-md-2">Giá KM</div>
            <div class="col-md-2">Số lượng</div>
            <div class="col-md-1">Trạng thái</div>
        </div>
        <div id="variantsContainer">
            @foreach($oldVariants as $index => $row)
                <div class="variant-row" data-index="{{ $index }}">
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="variants[{{ $index }}][mau_sac_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn màu --</option>
                                @foreach($colors as $c)
                                    <option value="{{ $c->id }}" {{ ($row['mau_sac_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->ten_mau }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="variants[{{ $index }}][kich_thuoc_id]" class="form-control form-control-sm" required>
                                <option value="">-- Chọn size --</option>
                                @foreach($sizes as $s)
                                    <option value="{{ $s->id }}" {{ ($row['kich_thuoc_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->ten_kich_thuoc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][gia]" class="form-control form-control-sm" value="{{ $row['gia'] ?? '' }}" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][gia_khuyen_mai]" class="form-control form-control-sm" value="{{ $row['gia_khuyen_mai'] ?? '' }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="variants[{{ $index }}][so_luong]" class="form-control form-control-sm" value="{{ $row['so_luong'] ?? '' }}" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <select name="variants[{{ $index }}][trang_thai]" class="form-control form-control-sm">
                                <option value="1" {{ ($row['trang_thai'] ?? '1') == '1' ? 'selected' : '' }}>Hiện</option>
                                <option value="0" {{ ($row['trang_thai'] ?? '1') == '0' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
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
.variant-header{
    font-size:12px;
    color:#000000;
    margin-bottom:6px;
}
.variant-header .col-md-1,
.variant-header .col-md-2,
.variant-header .col-md-3{
    padding-top:2px;
    padding-bottom:2px;
}
#variantsContainer .variant-row{
    padding:10px;
    border:1px dashed #ddd;
    margin-bottom:10px;
    background:#fcfcfc;
    width: 100%;
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
