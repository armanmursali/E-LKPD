<?php

namespace App\Http\Controllers;

use App\Models\DeskripsiLkpd;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeskripsiLkpdController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $deskripsi = $this->firstOrCreateFor($kelas);

        return Inertia::render('deskripsi-lkpd/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'deskripsiLkpd' => [
                'paragraf' => $deskripsi->paragraf,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $deskripsi = $this->firstOrCreateFor($kelas);

        $validated = $request->validate([
            'paragraf' => ['required', 'string'],
        ]);

        $paragraf = array_values(array_filter(array_map('trim', explode("\n\n", $validated['paragraf']))));

        $deskripsi->update([
            'paragraf' => $paragraf,
        ]);

        return redirect()->back();
    }

    protected function firstOrCreateFor(Kelas $kelas): DeskripsiLkpd
    {
        $defaults = [
            'paragraf' => ['update', 'update'],
        ];

        return DeskripsiLkpd::firstOrCreate(
            ['kelas_id' => $kelas->id],
            $defaults
        );
    }
}
