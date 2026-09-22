<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $classes = Kelas::where('user_id', $request->user()->id)
            ->withCount('lkpdSubmissions')
            ->latest()
            ->get(['id', 'nama', 'deskripsi']);
        $submissions = $classes->flatMap(fn (Kelas $kelas) => $kelas->lkpdSubmissions()
            ->latest()
            ->limit(5)
            ->get(['id', 'kelas_id', 'nama_peserta', 'kegiatan_nomor', 'nilai', 'created_at'])
            ->map(fn ($submission) => [
                'id' => $submission->id,
                'kelasId' => $submission->kelas_id,
                'kelasNama' => $kelas->nama,
                'namaPeserta' => $submission->nama_peserta,
                'kegiatanNomor' => $submission->kegiatan_nomor,
                'nilai' => $submission->nilai,
                'createdAt' => $submission->created_at?->toISOString(),
            ]))
            ->sortByDesc('createdAt')
            ->take(8)
            ->values();
        $allSubmissions = $classes->flatMap(fn (Kelas $kelas) => $kelas->lkpdSubmissions()->get(['nama_peserta', 'nilai']));

        return Inertia::render('Dashboard', [
            'summary' => [
                'classes' => $classes->count(),
                'submissions' => $allSubmissions->count(),
                'graded' => $allSubmissions->whereNotNull('nilai')->count(),
                'average' => $allSubmissions->whereNotNull('nilai')->avg('nilai') === null
                    ? null
                    : round((float) $allSubmissions->whereNotNull('nilai')->avg('nilai'), 1),
            ],
            'classes' => $classes->map(fn (Kelas $kelas) => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'deskripsi' => $kelas->deskripsi,
                'submissionsCount' => $kelas->lkpd_submissions_count,
            ])->values(),
            'recentSubmissions' => $submissions,
        ]);
    }
}