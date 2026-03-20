@extends('admin.layout.AdminLayout')

@section('AdminContent')

<div class="container-fluid" style="margin-top: 10px;">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 font-weight-bold">Quản lý biến thể sản phẩm</h2>
    <div>
        <a href="{{ route('admin.san-pham.index') }}" class="btn btn-outline-secondary shadow-sm px-3 mr-2">
            <i class="fa fa-arrow-left"></i> Danh sách sản phẩm
        </a>
        {{-- @if(!empty($selectedProductId))
            <a href="{{ route('variants.index') }}" class="btn btn-outline-secondary shadow-sm px-3 mr-2">
                <i class="fa fa-list"></i> Tất cả biến thể
            </a>
        @endif
        <a href="{{ route('variants.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fa fa-plus"></i> Thêm biến thể
        </a> --}}
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif

@php
    // Reset chỉ xóa lọc biến thể, giữ lại lọc theo sản phẩm (nếu có)
    $resetUrl = route('variants.index', array_filter(['san_pham_id' => $selectedProductId], function ($v) {
        return $v !== null && $v !== '';
    }));

    $khoFilter = request('kho_filter', 'all');
@endphp

<div class="card shadow-lg border-0">
    <div class="card-body p-0">

        @forelse($sanPhams as $sp)
        <div class="border-bottom">

            {{-- HEADER SẢN PHẨM --}}
            <div class="d-flex justify-content-between align-items-center p-4 bg-white">

                <div class="d-flex align-items-center">
                    <img src="{{ $sp->hinh_anh_chinh ? asset('storage/' . $sp->hinh_anh_chinh) : asset('img/shop_01.jpg') }}"
                         width="120" height="120"
                         style="object-fit:cover; border-radius:10px;"
                         class="shadow-sm mr-3">

                    <div>
                        <div class="font-weight-bold" style="font-size: 18px;">
                            {{ $sp->ten_san_pham }}
                        </div>

                        <div class="text-muted small">
                            Danh mục: {{ $sp->danhMuc->ten_danh_muc ?? '-' }}
                        </div>

                        <div class="mt-1">
                            <span class="badge badge-info px-3 py-1">
                                {{ $sp->variants->count() }} biến thể
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    {{-- Chỉ render nút lọc 1 lần (đặt cạnh nút Sửa/Thêm) --}}
                    @if($loop->first)
                        <form method="get" action="{{ route('variants.index') }}" style="display:inline;">
                            <input type="hidden" name="san_pham_id" value="{{ $selectedProductId }}">
                            <div class="dropup" style="display:inline-block; margin-right:8px;">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    title="Lọc nâng cao"
                                >
                                    <i class="fas fa-filter"></i> Lọc
                                </button>

                                <div class="dropdown-menu p-2" style="min-width: 460px; padding: 12px 16px; left: 0; right: auto;">
                                    <div onclick="event.stopPropagation()">
                                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">màu/size</label>
                                            <select name="mau_sac_id" class="form-control form-control-sm" style="flex:1; min-width:0;">
                                                <option value="">Tất cả</option>
                                                @foreach($colors as $c)
                                                    <option value="{{ $c->id }}" {{ request('mau_sac_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->ten_mau }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="kich_thuoc_id" class="form-control form-control-sm" style="flex:1; min-width:0;">
                                                <option value="">Tất cả</option>
                                                @foreach($sizes as $s)
                                                    <option value="{{ $s->id }}" {{ request('kich_thuoc_id') == $s->id ? 'selected' : '' }}>
                                                        {{ $s->ten_kich_thuoc }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-1" style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">Trạng thái</label>
                                            <select name="trang_thai" class="form-control form-control-sm" style="flex:1;">
                                                <option value="">Tất cả</option>
                                                <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hiện</option>
                                                <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Ẩn</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-1" style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">Giảm giá</label>
                                            <select name="km_filter" class="form-control form-control-sm" style="flex:1;">
                                                <option value="all" {{ request('km_filter', 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
                                                <option value="dang_giam" {{ request('km_filter') === 'dang_giam' ? 'selected' : '' }}>Đang giảm giá</option>
                                            </select>
                                        </div>

                                        <hr style="opacity:.2; margin: 8px 0;">

                                        <div class="form-group mb-1" style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">Kho</label>
                                            <select name="kho_filter" class="form-control form-control-sm" style="flex:1;">
                                                <option value="all" {{ $khoFilter === 'all' ? 'selected' : '' }}>Tất cả</option>
                                                <option value="het_hang" {{ $khoFilter === 'het_hang' ? 'selected' : '' }}>=0 hết hàng</option>
                                                <option value="gan_het" {{ $khoFilter === 'gan_het' ? 'selected' : '' }}>&lt;10 gần hết</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-1" style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">Min/Max</label>
                                            <div style="display:flex; gap:10px; flex:1;">
                                                <input type="number"
                                                       name="kho_min"
                                                       class="form-control form-control-sm"
                                                       min="0"
                                                       step="1"
                                                       placeholder="Từ"
                                                       value="{{ request('kho_min') }}"
                                                       style="flex:1;">
                                                <input type="number"
                                                       name="kho_max"
                                                       class="form-control form-control-sm"
                                                       min="0"
                                                       step="1"
                                                       placeholder="Đến"
                                                       value="{{ request('kho_max') }}"
                                                       style="flex:1;">
                                            </div>
                                        </div>

                                        <hr style="opacity:.2; margin: 8px 0;">

                                        <div class="form-group mb-1" style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
                                            <label class="small text-muted" style="margin-bottom:0; white-space:nowrap; width:90px;">Giá Min/Max</label>
                                            <div style="display:flex; gap:10px; flex:1;">
                                                <input type="number" name="gia_min" class="form-control form-control-sm" min="0" step="1" placeholder="Từ" value="{{ request('gia_min') }}" style="flex:1;">
                                                <input type="number" name="gia_max" class="form-control form-control-sm" min="0" step="1" placeholder="Đến" value="{{ request('gia_max') }}" style="flex:1;">
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 mt-1">
                                            <button type="submit" class="btn btn-sm btn-primary">Lọc</button>
                                            <a href="{{ $resetUrl }}" class="btn btn-sm btn-outline-secondary" title="Xóa lọc nâng cao">Xóa</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif

                    @if($sp->variants->count() > 0)
                        <a href="{{ route('variants.edit', $sp->variants->first()->id) }}"
                           class="btn btn-warning btn-sm shadow px-3 mr-2">
                            <i class="fa fa-edit"></i> Sửa biến thể
                        </a>
                    @endif
                    <a href="{{ route('variants.create', ['san_pham_id' => $sp->id]) }}"
                       class="btn btn-success btn-sm shadow px-3">
                        <i class="fa fa-plus-circle"></i> Thêm biến thể
                    </a>
                </div>

            </div>

            {{-- BẢNG BIẾN THỂ --}}
            @if($sp->variants->count() > 0)
            <div class="table-responsive px-3 pb-3">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Màu</th>
                            <th>Size</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Trạng thái</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sp->variants as $v)
                        <tr>
                            <td>{{ $v->color->ten_mau ?? '-' }}</td>
                            <td>{{ $v->size->ten_kich_thuoc ?? '-' }}</td>
                            <td class="font-weight-bold text-danger">
                                {{ number_format($v->gia ?? 0) }}đ
                            </td>
                            <td>{{ $v->so_luong }}</td>
                            <td>
                                @if($v->trang_thai)
                                    <span class="badge badge-success px-3">Hiện</span>
                                @else
                                    <span class="badge badge-secondary px-3 width">Ẩn</span>
                                @endif
                            </td>
                                     <td>
                                          {{-- <a href="{{ route('variants.edit', $v->id) }}"
                                              class="btn btn-warning btn-sm">
                                              <i class="fa fa-edit"></i>
                                          </a> --}}

                                <form action="{{ route('variants.delete', $v->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Xóa biến thể này?')">
                                            <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 text-center text-muted small">
                @if(!empty($hasVariantFilters))
                    Không có biến thể phù hợp bộ lọc —
                    <a href="{{ $resetUrl }}" class="font-weight-bold">Xóa bộ lọc</a>
                @else
                    Chưa có biến thể —
                    <a href="{{ route('variants.create', ['san_pham_id' => $sp->id]) }}" class="font-weight-bold">
                        Thêm biến thể đầu tiên
                    </a>
                @endif
            </div>
            @endif

        </div>
        @empty
        <div class="alert alert-info m-4">
            @if(!empty($hasVariantFilters))
                Không có biến thể phù hợp bộ lọc.
            @else
                Chưa có biến thể nào.
            @endif
        </div>
        @endforelse

    </div>
</div>
</div>

@endsection
