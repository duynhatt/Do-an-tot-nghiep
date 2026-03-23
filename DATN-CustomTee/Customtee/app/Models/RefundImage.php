<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RefundImage extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     *
     * @var string
     */
    protected $table = 'refund_images';

    /**
     * Các trường có thể mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'refund_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    /**
     * Các trường sẽ được cast sang kiểu dữ liệu phù hợp
     *
     * @var array
     */
    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Quan hệ belongsTo với Refund (yêu cầu hoàn tiền)
     */
    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    /**
     * Accessor: lấy URL công khai đầy đủ của ảnh
     * Sử dụng: $image->url
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Accessor: lấy kích thước file dạng dễ đọc (KB, MB)
     * Sử dụng: $image->human_size
     *
     * @return string
     */
    public function getHumanSizeAttribute(): string
    {
        if (!$this->size) {
            return 'Không xác định';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Accessor: kiểm tra file có phải ảnh không
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'image/');
    }
}