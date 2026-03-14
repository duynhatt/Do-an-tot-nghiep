@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="form-w3layouts">
    <section class="panel">
        <header class="panel-heading">CHỈNH SỬA VOUCHER</header>
        <div class="panel-body">
            <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Mã Voucher</label>
                    <input type="text" name="ma" class="form-control" value="{{ $voucher->ma }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Loại</label>
                        <select name="loai" class="form-control">
                            <option value="tien_mat" {{ $voucher->loai == 'tien_mat' ? 'selected' : '' }}>Tiền mặt (đ)</option>
                            <option value="phan_tram" {{ $voucher->loai == 'phan_tram' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Giá trị giảm</label>
                        <input type="number" name="gia_tri" class="form-control" value="{{ (int)$voucher->gia_tri }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="datetime-local" name="bat_dau" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($voucher->bat_dau)) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ngày kết thúc</label>
                        <input type="datetime-local" name="ket_thuc" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($voucher->ket_thuc)) }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="so_luong" class="form-control" value="{{ $voucher->so_luong }}" required>
                </div>
                <button type="submit" class="btn btn-info">Cập nhật thay đổi</button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-default">Quay lại</a>
            </form>
        </div>
    </section>
</div>
@endsection