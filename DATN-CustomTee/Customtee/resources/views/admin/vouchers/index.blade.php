@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            DANH SÁCH MÃ GIẢM GIÁ (VOUCHERS)
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <a href="{{ route('admin.vouchers.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Thêm Voucher mới
                </a>                
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Loại giảm</th>
                        <th>Giá trị</th>
                        <th>Số lượng</th>
                        <th>Đã dùng</th>
                        <th>Hạn dùng</th>
                        <th style="width:150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $v)
                    <tr>
                        <td><b class="text-primary">{{ $v->ma }}</b></td>
                        <td>{{ $v->loai == 'tien_mat' ? 'Tiền mặt' : 'Phần trăm' }}</td>
                        <td>
                            {{ number_format($v->gia_tri) }}{{ $v->loai == 'phan_tram' ? '%' : 'đ' }}
                        </td>
                        <td>{{ $v->so_luong }}</td>
                        <td>{{ $v->da_su_dung }}</td>
                        <td>
                            <small>
                                Từ: {{ date('d/m/Y', strtotime($v->bat_dau)) }}<br>
                                Đến: {{ date('d/m/Y', strtotime($v->ket_thuc)) }}
                            </small>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-xs btn-warning" title="Sửa">
                                    <i class="fa fa-pencil"></i> Sửa
                                </a>

                                <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" style="margin:0;">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa mã {{ $v->ma }} này không?')" title="Xóa">
                                        <i class="fa fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection