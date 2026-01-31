@extends('admin.layout.AdminLayout')

@section('AdminContent')

<h3 style="margin-bottom:20px;">Thêm biến thể sản phẩm</h3>

<form action="{{ route('variants.store') }}" method="POST" style="max-width:600px;">
    @csrf

    {{-- SẢN PHẨM --}}
    <div class="form-group">
        <label>Sản phẩm</label>
        <select name="san_pham_id" id="productSelect" class="form-control" required>
            <option value="">-- Chọn sản phẩm --</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->ten_san_pham }}</option>
            @endforeach
        </select>
    </div>

    {{-- THÔNG TIN SẢN PHẨM --}}
    <div id="productInfo" class="product-info-box">
        <img id="productImage">
        <div><b>Tên:</b> <span id="productName"></span></div>
        <div><b>Danh mục:</b> <span id="productCategory"></span></div>
        <div><b>Mô tả:</b> <span id="productDesc"></span></div>
    </div>

    {{-- MÀU --}}
    <div class="form-group">
        <label>Màu</label>
        <select name="mau_sac_id" class="form-control" required>
            @foreach($colors as $c)
                <option value="{{ $c->id }}">{{ $c->ten_mau }}</option>
            @endforeach
        </select>
    </div>

    {{-- SIZE --}}
    <div class="form-group">
        <label>Size</label>
        <select name="kich_thuoc_id" class="form-control" required>
            @foreach($sizes as $s)
                <option value="{{ $s->id }}">{{ $s->ten_kich_thuoc }}</option>
            @endforeach
        </select>
    </div>

    {{-- GIÁ --}}
    <div class="form-group">
        <label>Giá</label>
        <input type="number" name="gia" class="form-control" required>
    </div>

    {{-- GIÁ KM --}}
    <div class="form-group">
        <label>Giá khuyến mãi</label>
        <input type="number" name="gia_khuyen_mai" class="form-control">
    </div>

    {{-- SỐ LƯỢNG --}}
    <div class="form-group">
        <label>Số lượng</label>
        <input type="number" name="so_luong" class="form-control" required>
    </div>

    {{-- TRẠNG THÁI --}}
    <div class="form-group">
        <label>Trạng thái</label>
        <select name="trang_thai" class="form-control">
            <option value="1">Hiện</option>
            <option value="0">Ẩn</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Thêm biến thể</button>
</form>


{{-- CSS GỌN --}}
<style>
.form-group{
    margin-bottom:15px;
}
.product-info-box{
    display:none;
    margin:15px 0;
    padding:10px;
    border:1px solid #ddd;
    background:#f9f9f9;
}
#productImage{
    width:120px;
    margin-bottom:10px;
    border-radius:6px;
}
</style>


{{-- AJAX --}}
<script>
document.getElementById('productSelect').addEventListener('change', function () {
    let productId = this.value;

    if (!productId) {
        document.getElementById('productInfo').style.display = 'none';
        return;
    }

    fetch(`/admin/products/info/${productId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('productInfo').style.display = 'block';
            document.getElementById('productName').innerText = data.name;
            document.getElementById('productCategory').innerText = data.category;
            document.getElementById('productDesc').innerText = data.desc;
            document.getElementById('productImage').src = '/storage/' + data.image;
        });
});
</script>

@endsection
