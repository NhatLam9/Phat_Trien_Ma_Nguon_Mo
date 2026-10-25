<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Nguoidung extends Authenticatable
{
    use Notifiable;

    protected $table = 'nguoidung';

    // Khớp với cấu trúc CSDL remake.sql (dùng ngaytao và ngaycapnhat thay vì timestamps mặc định)
    const CREATED_AT = 'ngaytao';
    const UPDATED_AT = 'ngaycapnhat';

    protected $fillable = [
        'hoten',
        'email',
        'email_xacthuc',
        'matkhau',
        'google_id',
        'vaitro',
        'anh',
        'muctieu',
        'trangthai',
    ];

    protected $hidden = [
        'matkhau',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_xacthuc' => 'datetime',
            'matkhau' => 'hashed',
        ];
    }

    // Trỏ cột mật khẩu về đúng tên 'matkhau' trong bảng nguoidung
    public function getAuthPasswordName()
    {
        return 'matkhau';
    }

    public function getAuthPassword()
    {
        return $this->matkhau;
    }
}
