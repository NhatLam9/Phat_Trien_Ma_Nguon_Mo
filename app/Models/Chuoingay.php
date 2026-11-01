<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chuoingay extends Model
{
    protected $table = 'chuoingay';

    // Khoa chinh la ma nguoi dung
    protected $primaryKey = 'nguoidung_id';

    public $incrementing = false;

    protected $keyType = 'int';

    // Bang khong co created_at, updated_at
    public $timestamps = false;

    protected $fillable = [
        'nguoidung_id',
        'hientai',
        'dainhat',
        'ngaygannhat',
    ];

    protected $casts = [
        'nguoidung_id' => 'integer',
        'hientai' => 'integer',
        'dainhat' => 'integer',
        'ngaygannhat' => 'date',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(
            Nguoidung::class,
            'nguoidung_id',
            'id'
        );
    }
}
