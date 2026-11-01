<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mot lan admin import cau hoi tu file JSON.
 */
class ImportBatch extends Model
{
    protected $table = 'import_batches';

    public const CREATED_AT = 'ngaytao';

    public const UPDATED_AT = null;

    protected $fillable = [
        'ten_file',
        'nguoitao',
        'tong_cau',
        'tao_moi',
        'cap_nhat',
        'so_canhbao',
        'trangthai',
        'ghichu',
    ];

    protected function casts(): array
    {
        return [
            'tong_cau' => 'integer',
            'tao_moi' => 'integer',
            'cap_nhat' => 'integer',
            'so_canhbao' => 'integer',
            'ngaytao' => 'datetime',
        ];
    }

    public function nguoiTao(): BelongsTo
    {
        return $this->belongsTo(Nguoidung::class, 'nguoitao');
    }

    public function laThanhCong(): bool
    {
        return $this->trangthai === 'success';
    }
}
