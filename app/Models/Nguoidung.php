<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;

class Nguoidung extends Authenticatable implements MustVerifyEmail
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

    // Hỗ trợ kiểm tra xác minh email theo cột email_xacthuc
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_xacthuc);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_xacthuc' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    // Quan hệ với các bảng phụ thuộc do TV1 sở hữu
    public function luyentaps()
    {
        return $this->hasMany(Luyentap::class, 'nguoidung_id');
    }

    public function danhdaus()
    {
        return $this->hasMany(Danhdau::class, 'nguoidung_id');
    }

    // Tiện ích phân quyền & avatar
    public function isAdmin(): bool
    {
        return $this->vaitro === 'admin';
    }

    public function isLocked(): bool
    {
        return $this->trangthai === 'locked';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->anh) {
            return str_starts_with($this->anh, 'http') ? $this->anh : asset('storage/' . $this->anh);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->hoten) . '&background=00b86b&color=ffffff';
    }
}
