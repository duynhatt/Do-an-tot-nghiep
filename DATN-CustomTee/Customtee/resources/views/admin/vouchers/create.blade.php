@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="form-w3layouts">
    <section class="panel">
        <header class="panel-heading">THÊM VOUCHER MỚI</header>
        <div class="panel-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Mã Voucher</label>
                    <input type="text" name="ma" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Loại</label>
                        <select name="loai" class="form-control">
                            <option value="tien_mat">Tiền mặt (đ)</option>
                            <option value="phan_tram">Phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Giá trị giảm</label>
                        <input type="number" name="gia_tri" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="datetime-local" name="bat_dau" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ngày kết thúc</label>
                        <input type="datetime-local" name="ket_thuc" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="so_luong" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-info">Lưu Voucher</button>
            </form>
        </div>
    </section>
</div>
@endsection