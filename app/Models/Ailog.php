<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ailog extends Model
{
    use HasFactory;

    protected $table = 'ailog';

    public const CREATED_AT = 'ngaytao';

    public const UPDATED_AT = null;

    protected $fillable = [
        'nguoidung_id',
        'loai',
        'dauvao',
        'daura',
        'model',
        'token_vao',
        'token_ra',
    ];

    protected function casts(): array
    {
        return [
            'token_vao' => 'integer',
            'token_ra' => 'integer',
            'ngaytao' => 'datetime',
        ];
    }

    public function nguoidung(): BelongsTo
    {
        return $this->belongsTo(Nguoidung::class, 'nguoidung_id');
    }
}
