<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluasiPembelajaran extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kelas_id',
        'judul',
        'jenis',
        'deskripsi',
        'google_form_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}