<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pendahuluan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PendahuluanController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $pendahuluan = $this->firstOrCreateFor($kelas);
        $pembimbing = json_decode((string) $pendahuluan->pembimbing, true);
        $pembimbing = is_array($pembimbing) ? $pembimbing : [$pendahuluan->pembimbing];

        return Inertia::render('pendahuluan/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'pendahuluan' => [
                'sampul' => [
                    ...$pendahuluan->only([
                        'judul', 'mapel', 'jenjang', 'kelas_label', 'fase', 'kurikulum',
                        'penulis', 'validator_media', 'validator_materi',
                    ]),
                    'pembimbing' => array_values(array_filter($pembimbing)),
                ],
                'kata_pengantar' => $pendahuluan->kata_pengantar,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $pendahuluan = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'mapel' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', 'max:255'],
            'kelas_label' => ['required', 'string', 'max:255'],
            'fase' => ['required', 'string', 'max:255'],
            'kurikulum' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'pembimbing' => ['required', 'array', 'min:1'],
            'pembimbing.*' => ['required', 'string', 'max:255'],
            'validator_media' => ['nullable', 'string', 'max:255'],
            'validator_materi' => ['nullable', 'string', 'max:255'],
            'kata_pengantar' => ['required', 'string'],
        ]);

        $paragraf = array_values(array_filter(array_map('trim', explode("\n\n", $validated['kata_pengantar']))));
        $pembimbing = array_values(array_filter(array_map('trim', $validated['pembimbing'])));

        $pendahuluan->update([
            ...collect($validated)->except(['kata_pengantar', 'pembimbing'])->all(),
            'pembimbing' => json_encode($pembimbing, JSON_UNESCAPED_UNICODE),
            'kata_pengantar' => $paragraf,
        ]);

        return redirect()->back();
    }

    protected function firstOrCreateFor(Kelas $kelas): Pendahuluan
    {
        return Pendahuluan::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'judul' => 'update',
                'mapel' => 'update',
                'jenjang' => 'update',
                'kelas_label' => 'update',
                'fase' => 'update',
                'kurikulum' => 'update',
                'penulis' => 'update',
                'pembimbing' => json_encode(['update'], JSON_UNESCAPED_UNICODE),
                'validator_media' => 'update',
                'validator_materi' => 'update',
                'kata_pengantar' => [
                    'update',
                    'update',
                    'update',
                ],
            ]
        );
    }
}
    