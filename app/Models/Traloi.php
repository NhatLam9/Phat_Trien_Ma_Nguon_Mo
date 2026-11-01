<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Traloi extends Model
{
    protected $table = 'traloi';

    public $timestamps = false;

    protected $fillable = [
        'lanthicauhoi_id',
        'noidung',
        'luachon_id',
        'audio',
        'ladung',
        'diem',
        'trangthai_ai',
        'diem_ai',
        'nhanxet_ai',
        'thoigian',
    ];

    protected $casts = [
        'ladung' => 'boolean',
        'diem' => 'decimal:2',
        'diem_ai' => 'decimal:2',
        'thoigian' => 'datetime',
    ];

    // Cau tra loi thuoc mot cau hoi trong lan thi
    public function lanThiCauHoi(): BelongsTo
    {
        return $this->belongsTo(
            Lanthicauhoi::class,
            'lanthicauhoi_id',
            'id'
        );
    }

    // Mot cau tra loi co the duoc nhieu nguoi cham
    public function chamDiem(): HasMany
    {
        return $this->hasMany(
            Chamdiem::class,
            'traloi_id',
            'id'
        );
    }
}
