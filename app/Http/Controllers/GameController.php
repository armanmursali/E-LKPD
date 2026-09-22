<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GameController extends Controller
{
    private const IKON = ['gamepad-2', 'puzzle', 'trophy', 'brain', 'rocket'];

    public function index(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($request, $kelas);

        return Inertia::render('game/Index', [
            'kelas' => $kelas->only(['id', 'nama', 'deskripsi']),
            'games' => $kelas->games()->latest()->get(),
            'ikonPilihan' => self::IKON,
        ]);
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($request, $kelas);
        $kelas->games()->create($this->validated($request));

        return to_route('kelas.game', $kelas)->with('success', 'Game berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas, Game $game)
    {
        $this->authorizeGame($request, $kelas, $game);
        $game->update($this->validated($request));

        return to_route('kelas.game', $kelas)->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Request $request, Kelas $kelas, Game $game)
    {
        $this->authorizeGame($request, $kelas, $game);
        $game->delete();

        return to_route('kelas.game', $kelas)->with('success', 'Game berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'wordwall_url' => ['required', 'url', 'max:2048'],
            'ikon' => ['required', 'string', 'in:'.implode(',', self::IKON)],
        ]);
    }

    private function authorizeKelas(Request $request, Kelas $kelas): void
    {
        abort_unless($kelas->user_id === $request->user()->id, 403);
    }

    private function authorizeGame(Request $request, Kelas $kelas, Game $game): void
    {
        $this->authorizeKelas($request, $kelas);
        abort_unless($game->kelas_id === $kelas->id, 404);
    }
}