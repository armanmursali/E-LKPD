<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiPembelajaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvaluasiPembelajaranController extends Controller
{
    private const JENIS = [
        'Pre-test',
        'Post-test',
    ];

    public function index(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($request, $kelas);

        return Inertia::render('evaluasi-pembelajaran/Index', [
            'kelas' => $kelas,
            'evaluasi' => $kelas->evaluasiPembelajaran()
                ->latest()
                ->get(),
            'jenisPilihan' => self::JENIS,
        ]);
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($request, $kelas);
        $data = $this->validated($request);
        $kelas->evaluasiPembelajaran()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return to_route('kelas.evaluasi-pembelajaran', $kelas)->with('success', 'Angket berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas, EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->authorizeEvaluasi($request, $kelas, $evaluasiPembelajaran);
        $evaluasiPembelajaran->update($this->validated($request));

        return to_route('kelas.evaluasi-pembelajaran', $kelas)->with('success', 'Angket berhasil diperbarui.');
    }

    public function destroy(Request $request, Kelas $kelas, EvaluasiPembelajaran $evaluasiPembelajaran)
    {
        $this->authorizeEvaluasi($request, $kelas, $evaluasiPembelajaran);
        $evaluasiPembelajaran->delete();

        return to_route('kelas.evaluasi-pembelajaran', $kelas)->with('success', 'Angket berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'in:'.implode(',', self::JENIS)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'google_form_url' => ['required', 'url', 'max:2048'],
        ]);
    }

    private function authorizeKelas(Request $request, Kelas $kelas): void
    {
        abort_unless($kelas->user_id === $request->user()->id, 403);
    }

    private function authorizeEvaluasi(Request $request, Kelas $kelas, EvaluasiPembelajaran $evaluasiPembelajaran): void
    {
        $this->authorizeKelas($request, $kelas);
        abort_unless($evaluasiPembelajaran->kelas_id === $kelas->id, 404);
    }
}
