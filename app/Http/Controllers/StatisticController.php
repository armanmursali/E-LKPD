<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $classes = Kelas::where('user_id', $request->user()->id)
            ->latest()
            ->get(['id', 'nama']);
        $selectedClass = $classes->firstWhere('id', (int) $request->integer('class_id')) ?? $classes->first();

        return Inertia::render('statistic/Index', [
            'classes' => $classes,
            'selectedClassId' => $selectedClass?->id,
            'statistics' => $selectedClass ? $this->statisticsFor($selectedClass) : $this->emptyStatistics(),
        ]);
    }

    private function statisticsFor(Kelas $kelas): array
    {
        $submissions = $kelas->lkpdSubmissions()
            ->whereNotNull('nilai')
            ->orderBy('kegiatan_nomor')
            ->orderBy('created_at')
            ->get(['nama_peserta', 'kegiatan_nomor', 'nilai']);
        $items = collect($kelas->kegiatanPembelajaran?->items ?? [])
            ->keyBy(fn (array $item): int => (int) ($item['id'] ?? 0));
        $students = [];

        foreach ($submissions as $submission) {
            $name = trim((string) $submission->nama_peserta);
            $studentKey = $this->findStudentKey($students, $name);
            if ($studentKey === null) {
                $studentKey = (string) count($students);
                $students[$studentKey] = [
                    'name' => $name,
                    'normalized' => $this->normalizeName($name),
                    'scores' => [],
                ];
            }

            $students[$studentKey]['scores'][(string) $submission->kegiatan_nomor] = (int) $submission->nilai;
        }

        $tasks = collect($submissions->pluck('kegiatan_nomor')->unique()->values())
            ->map(fn (int $number): array => [
                'number' => $number,
                'label' => $items->get($number)['judul'] ?? 'Tugas '.$number,
            ])->values();
        $taskAverages = $tasks->map(function (array $task) use ($submissions): array {
            $values = $submissions->where('kegiatan_nomor', $task['number'])->pluck('nilai');

            return [...$task, 'average' => round((float) $values->avg(), 1), 'count' => $values->count()];
        })->values();

        return [
            'students' => collect($students)->map(function (array $student): array {
                unset($student['normalized']);
                return $student;
            })->values(),
            'tasks' => $taskAverages,
            'summary' => [
                'students' => count($students),
                'submissions' => $submissions->count(),
                'average' => $submissions->isEmpty() ? null : round((float) $submissions->avg('nilai'), 1),
            ],
        ];
    }

    private function findStudentKey(array $students, string $name): ?string
    {
        $normalized = $this->normalizeName($name);
        foreach ($students as $key => $student) {
            $distance = levenshtein($normalized, $student['normalized']);
            $limit = min(3, max(1, (int) floor(min(strlen($normalized), strlen($student['normalized'])) * 0.2)));
            if ($distance <= $limit || similar_text($normalized, $student['normalized']) / max(strlen($normalized), strlen($student['normalized']), 1) >= 0.88) {
                return (string) $key;
            }
        }

        return null;
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($name))) ?? '';
    }

    private function emptyStatistics(): array
    {
        return ['students' => [], 'tasks' => [], 'summary' => ['students' => 0, 'submissions' => 0, 'average' => null]];
    }
}