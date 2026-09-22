<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['kelas_id', 'nama', 'deskripsi', 'wordwall_url', 'ikon'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}