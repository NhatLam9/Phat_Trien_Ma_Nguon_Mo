<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dethicauhoi extends Model
{
    use HasFactory;

    protected $table = 'dethicauhoi';

    public $timestamps = false;

    protected $fillable = ['dethi_id', 'cauhoi_id', 'thutu', 'diem'];

    protected function casts(): array
    {
        return [
            'thutu' => 'integer',
            'diem' => 'decimal:2',
        ];
    }

    public function dethi(): BelongsTo
    {
        return $this->belongsTo(Dethi::class, 'dethi_id');
    }

    public function cauhoi(): BelongsTo
    {
        return $this->belongsTo(Cauhoi::class, 'cauhoi_id');
    }
}
