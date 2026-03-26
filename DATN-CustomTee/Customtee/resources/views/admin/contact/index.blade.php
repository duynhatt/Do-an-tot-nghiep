@extends('admin.layout.AdminLayout')

@section('AdminContent')
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            Danh sách liên hệ từ khách hàng
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Người gửi</th>
                        <th>Tiêu đề</th>
                        <th>Nội dung</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th style="width:100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($contacts) && $contacts->count() > 0)
                        @foreach($contacts as $key => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->user_name ?? 'Khách vãng lai' }}</strong><br>
                                <small>{{ $item->user_email ?? '' }}</small>
                            </td>
                            <td>{{ $item->tieu_de }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->noi_dung, 50) }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                            <td>
                                {{-- SỬA ĐIỀU KIỆN TẠI ĐÂY: So sánh với chữ 'da_xu_ly' --}}
                                @if($item->trang_thai == 'da_xu_ly')
                                    <span class="label label-success">Đã xác nhận</span>
                                @else
                                    <span class="label label-danger">Chờ xử lý</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <form action="{{ route('admin.lien-he.updateStatus', $item->id) }}" method="POST">
                                        @csrf
                                        {{-- SỬA NÚT BẤM: Đổi màu theo trạng thái chữ --}}
                                        <button type="submit" class="btn btn-sm {{ $item->trang_thai == 'da_xu_ly' ? 'btn-warning' : 'btn-primary' }}">
                                            <i class="fa {{ $item->trang_thai == 'da_xu_ly' ? 'fa-refresh' : 'fa-check' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.lien-he.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Xóa liên hệ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Chưa có liên hệ nào.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if(isset($contacts))
        <footer class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    {{ $contacts->links() }}
                </div>
            </div>
        </footer>
        @endif
    </div>
</div>
@endsection