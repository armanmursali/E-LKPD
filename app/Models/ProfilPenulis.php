<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilPenulis extends Model
{
    use HasFactory;

    protected $table = 'profil_penulis';

    protected $fillable = [
        'kelas_id',
        'paragraf',
        'gambar',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}