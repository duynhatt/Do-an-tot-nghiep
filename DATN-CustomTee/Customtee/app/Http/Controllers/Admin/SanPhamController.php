<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BienThe;
use App\Models\Category;
use App\Models\KichThuoc;
use App\Models\MauSac;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SanPhamController extends Controller
{
    public function index()
    {
        $sanPhams = SanPham::with('danhMuc')->orderBy('id', 'desc')->get();

        $danhMucs = Category::where('trang_thai', 1)->get();
        $colors = MauSac::all();
        $sizes = KichThuoc::all();

        return view('admin.product.list', compact('sanPhams', 'danhMucs', 'colors', 'sizes'));
    }

    public function create()
    {
        $danhMucs = Category::where('trang_thai', 1)->get();
        return view('admin.san-pham.create', compact('danhMucs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'danh_muc_id'       => 'required|exists:danh_mucs,id',
            'ten_san_pham'      => 'required|string|max:255',
            'mo_ta_ngan'        => 'nullable|string|max:500',
            'mo_ta_chi_tiet'    => 'nullable|string',
            'hinh_anh_chinh'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cho_phep_thiet_ke' => 'boolean',
            'trang_thai'        => 'required|in:0,1',
            'variants'                  => 'nullable|array',
            'variants.*.mau_sac_id'     => 'required_with:variants|exists:mau_sacs,id',
            'variants.*.kich_thuoc_id'  => 'required_with:variants|exists:kich_thuocs,id',
            'variants.*.gia'            => 'required_with:variants|numeric|min:0|max:999999999999',
            'variants.*.gia_khuyen_mai' => 'nullable|numeric|min:0|max:999999999999',
            'variants.*.so_luong'       => 'required_with:variants|integer|min:0',
            'variants.*.trang_thai'     => 'nullable|in:0,1',
        ], [
            'variants.*.gia.min'            => 'Giá không được âm.',
            'variants.*.gia.max'            => 'Giá vượt quá giới hạn cho phép.',
            'variants.*.gia_khuyen_mai.min' => 'Giá khuyến mãi không được âm.',
            'variants.*.so_luong.min'       => 'Số lượng không được âm.',
            'variants.*.trang_thai.in'      => 'Trạng thái không hợp lệ.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $variants = $request->input('variants', []);
            $seen = [];

            foreach ($variants as $index => $variant) {
                $mau = $variant['mau_sac_id'] ?? null;
                $kich = $variant['kich_thuoc_id'] ?? null;
                $gia = $variant['gia'] ?? null;
                $giaKm = $variant['gia_khuyen_mai'] ?? null;

                if ($giaKm !== null && $gia !== null && $giaKm > $gia) {
                    $validator->errors()->add("variants.$index.gia_khuyen_mai", 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.');
                }

                if ($mau !== null && $kich !== null) {
                    $key = $mau . '-' . $kich;
                    if (isset($seen[$key])) {
                        $validator->errors()->add("variants.$index.mau_sac_id", 'Không được trùng biến thể trong danh sách thêm mới.');
                    }
                    $seen[$key] = true;
                }
            }
        });

        $validated = $validator->validate();

        if ($request->hasFile('hinh_anh_chinh')) {
            $path = $request->file('hinh_anh_chinh')->store('san-pham', 'public');
            $validated['hinh_anh_chinh'] = $path;
        }

        $variants = $validated['variants'] ?? [];
        unset($validated['variants']);

        $sanPham = SanPham::create($validated);

        if (!empty($variants)) {
            $rows = [];
            foreach ($variants as $variant) {
                $rows[] = [
                    'san_pham_id'    => $sanPham->id,
                    'mau_sac_id'     => $variant['mau_sac_id'],
                    'kich_thuoc_id'  => $variant['kich_thuoc_id'],
                    'gia'            => $variant['gia'],
                    'gia_khuyen_mai' => $variant['gia_khuyen_mai'] ?? null,
                    'so_luong'       => $variant['so_luong'],
                    'trang_thai'     => $variant['trang_thai'] ?? 1,
                ];
            }

            BienThe::insert($rows);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Thêm sản phẩm thành công',
        ]);
    }

    public function edit($id)
    {
        $sanPham = SanPham::findOrFail($id);
        $danhMucs = Category::where('trang_thai', 1)->get();

        return response()->json([
            'status' => true,
            'data'   => $sanPham,
            'danh_mucs' => $danhMucs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);

        $validated = $request->validate([
            'danh_muc_id'       => 'required|exists:danh_mucs,id',
            'ten_san_pham'      => 'required|string|max:255',
            'mo_ta_ngan'        => 'nullable|string|max:500',
            'mo_ta_chi_tiet'    => 'nullable|string',
            'hinh_anh_chinh'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cho_phep_thiet_ke' => 'boolean',
            'trang_thai'        => 'required|in:0,1',
        ]);

        if ($request->hasFile('hinh_anh_chinh')) {
            if ($sanPham->hinh_anh_chinh) {
                Storage::disk('public')->delete($sanPham->hinh_anh_chinh);
            }
            $path = $request->file('hinh_anh_chinh')->store('san-pham', 'public');
            $validated['hinh_anh_chinh'] = $path;
        }

        $sanPham->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật sản phẩm thành công',
        ]);
    }

    public function destroy($id)
    {
        $sanPham = SanPham::findOrFail($id);

        if ($sanPham->hinh_anh_chinh) {
            Storage::disk('public')->delete($sanPham->hinh_anh_chinh);
        }

        $sanPham->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }
}
