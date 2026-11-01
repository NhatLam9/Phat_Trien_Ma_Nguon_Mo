<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aisudung extends Model
{
    use HasFactory;

    protected $table = 'aisudung';

    public $timestamps = false;

    protected $fillable = ['nguoidung_id', 'ngay', 'solan'];

    protected function casts(): array
    {
        return [
            'ngay' => 'date',
            'solan' => 'integer',
        ];
    }

    public function nguoidung(): BelongsTo
    {
        return $this->belongsTo(Nguoidung::class, 'nguoidung_id');
    }
}
