<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiendo extends Model
{
    protected $table = 'tiendo';

    const CREATED_AT = null;
    const UPDATED_AT = 'ngaycapnhat';

    protected $fillable = [
        'nguoidung_id',
        'loai_id',
        'tongso',
        'sodung',
        'diemtrungbinh',
    ];

    protected $casts = [
        'tongso' => 'integer',
        'sodung' => 'integer',
        'diemtrungbinh' => 'decimal:2',
        'ngaycapnhat' => 'datetime',
    ];
}
