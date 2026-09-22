<?php

namespace App\Http\Controllers;

use App\Models\DaftarPustaka;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DaftarPustakaController extends Controller
{
    public function index(Kelas $kelas)
    {
        $this->authorize('view', $kelas);

        $daftarPustaka = $this->firstOrCreateFor($kelas);

        return Inertia::render('daftar-pustaka/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'daftarPustaka' => [
                'items' => $daftarPustaka->items,
            ],
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorize('update', $kelas);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.referensi' => ['required', 'string', 'max:2000'],
        ]);

        $this->firstOrCreateFor($kelas)->update([
            'items' => collect($validated['items'])
                ->values()
                ->map(fn (array $item, int $index) => [
                    'nomor' => $index + 1,
                    'referensi' => trim($item['referensi']),
                ])
                ->all(),
        ]);

        return redirect()->back();
    }

    private function firstOrCreateFor(Kelas $kelas): DaftarPustaka
    {
        return DaftarPustaka::firstOrCreate(
            ['kelas_id' => $kelas->id],
            ['items' => []],
        );
    }
}