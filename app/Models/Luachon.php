<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Luachon extends Model
{
    use HasFactory;

    protected $table = 'luachon';

    public $timestamps = false;

    protected $fillable = ['cauhoi_id', 'noidung', 'ladung', 'thutu'];

    protected function casts(): array
    {
        return [
            'ladung' => 'boolean',
            'thutu' => 'integer',
        ];
    }

    public function cauhoi(): BelongsTo
    {
        return $this->belongsTo(Cauhoi::class, 'cauhoi_id');
    }
}
