<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\ProfilPenulis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfilPenulisController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);
        $profil = $this->firstOrCreateFor($kelas);

        return Inertia::render('profil-penulis/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'profilPenulis' => [
                'paragraf' => $profil->paragraf,
                'gambar' => $profil->gambar
                    ? rtrim((string) config('filesystems.disks.public.url'), '/').'/'.ltrim($profil->gambar, '/')
                    : null,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $validated = $request->validate([
            'paragraf' => ['required', 'string', 'max:20000'],
            'gambar' => ['nullable', 'image', 'max:5120'],
            'hapus_gambar' => ['nullable', 'boolean'],
        ]);

        $profil = $this->firstOrCreateFor($kelas);
        $data = ['paragraf' => $validated['paragraf']];

        if ($request->boolean('hapus_gambar') && $profil->gambar) {
            Storage::disk('public')->delete($profil->gambar);
            $data['gambar'] = null;
        }

        if ($request->hasFile('gambar')) {
            if ($profil->gambar) {
                Storage::disk('public')->delete($profil->gambar);
            }

            $data['gambar'] = $request->file('gambar')->store('profil-penulis', 'public');
        }

        $profil->update($data);

        return redirect()->back();
    }

    private function firstOrCreateFor(Kelas $kelas): ProfilPenulis
    {
        return ProfilPenulis::firstOrCreate(
            ['kelas_id' => $kelas->id],
            ['paragraf' => ''],
        );
    }
}