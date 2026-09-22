<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendahuluan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'judul',
        'mapel',
        'jenjang',
        'kelas_label',
        'fase',
        'kurikulum',
        'penulis',
        'pembimbing',
        'validator_media',
        'validator_materi',
        'kata_pengantar',
    ];

    protected $casts = [
        'kata_pengantar' => 'array',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
