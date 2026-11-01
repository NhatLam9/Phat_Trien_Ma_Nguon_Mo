<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lanthi extends Model
{
    protected $table = 'lanthi';

    protected $primaryKey = 'id';

    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = null;

    protected $fillable = [
        'nguoidung_id',
        'dethi_id',
        'dokho_hientai',
        'batdau',
        'ketthuc',
        'trangthai',
        'literacy',
        'comprehension',
        'conversation',
        'production',
        'tongdiem',
    ];

    protected $casts = [
        'dokho_hientai' => 'decimal:2',
        'batdau' => 'datetime',
        'ketthuc' => 'datetime',
        'ngaytao' => 'datetime',
        'tongdiem' => 'integer',
    ];

    // Mot lan thi co nhieu cau hoi
    public function cauHoi(): HasMany
    {
        return $this->hasMany(
            Lanthicauhoi::class,
            'lanthi_id',
            'id'
        );
    }

    // Mot lan thi co the co nhieu vi pham
    public function viPham(): HasMany
    {
        return $this->hasMany(
            Vipham::class,
            'lanthi_id',
            'id'
        );
    }

    // Mot lan thi co toi da mot chung chi
    public function chungChi(): HasOne
    {
        return $this->hasOne(
            Chungchi::class,
            'lanthi_id',
            'id'
        );
    }
}
