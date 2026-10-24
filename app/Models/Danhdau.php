<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Danhdau extends Model
{
    protected $table = 'danhdau';

    // Bảng danhdau dùng cột ngaytao thay cho created_at mặc định
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = null; // không có cột updated_at

    protected $fillable = [
        'nguoidung_id',
        'cauhoi_id',
        'ngaytao',
    ];

    // Quan hệ với bảng nguoidung
    public function nguoidung()
    {
        return $this->belongsTo(Nguoidung::class, 'nguoidung_id');
    }
}