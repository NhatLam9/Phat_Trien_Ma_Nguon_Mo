<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Luyentap extends Model
{
    protected $table = 'luyentap';

    // Khớp với cấu trúc CSDL remake.sql (chỉ dùng ngaylam, không dùng timestamps mặc định)
    public $timestamps = false;
    const CREATED_AT = 'ngaylam';

    protected $fillable = [
        'nguoidung_id',
        'cauhoi_id',
        'noidung',
        'luachon_id',
        'audio',
        'ladung',
        'diem',
        'trangthai_ai',
        'diem_ai',
        'nhanxet_ai',
        'thoigianlam',
        'ngaylam',
    ];

    // Quan hệ với bảng nguoidung
    public function nguoidung()
    {
        return $this->belongsTo(Nguoidung::class, 'nguoidung_id');
    }

    // Quan hệ với bảng cauhoi
    public function cauhoi()
    {
        return $this->belongsTo(Cauhoi::class, 'cauhoi_id');
    }

    // Quan hệ với bảng luachon
    public function luachon()
    {
        return $this->belongsTo(Luachon::class, 'luachon_id');
    }
}