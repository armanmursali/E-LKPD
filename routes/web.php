<?php

use App\Http\Controllers\DeskripsiLkpdController;
use App\Http\Controllers\DaftarPustakaController;
use App\Http\Controllers\EvaluasiPembelajaranController;
use App\Http\Controllers\IdentitasMapelController;
use App\Http\Controllers\KegiatanPembelajaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LkpdKontenController;
use App\Http\Controllers\PendahuluanController;
use App\Http\Controllers\PetunjukLkpdController;
use App\Http\Controllers\ProfilPenulisController;
use App\Http\Controllers\PublicKelasController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LkpdSubmissionController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;


Route::inertia('/', 'Welcome')->name('home');
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/kelas-publik/{token}', [PublicKelasController::class, 'show'])->name('kelas.public');
Route::get('/kelas-publik/{token}/{section}', [PublicKelasController::class, 'show'])->name('kelas.public.section');
Route::get('/kelas-publik/{token}/kegiatan-pembelajaran/{nomor}', [PublicKelasController::class, 'activity'])->whereNumber('nomor')->name('kelas.public.activity');
Route::get('/kelas-publik/{token}/game', [PublicKelasController::class, 'game'])->name('kelas.public.game');
Route::post('/kelas-publik/{token}/kegiatan-pembelajaran/{nomor}/jawaban', [LkpdSubmissionController::class, 'store'])->whereNumber('nomor')->name('kelas.public.submissions.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    Route::get('/kelas/{kelas}/pendahuluan', [PendahuluanController::class, 'index'])->name('kelas.pendahuluan');
    Route::put('/kelas/{kelas}/pendahuluan', [PendahuluanController::class, 'update'])->name('kelas.pendahuluan.update');

    Route::get('/kelas/{kelas}/deskripsi-lkpd', [DeskripsiLkpdController::class, 'index'])->name('kelas.deskripsi-lkpd');
    Route::put('/kelas/{kelas}/deskripsi-lkpd', [DeskripsiLkpdController::class, 'update'])->name('kelas.deskripsi-lkpd.update');

    Route::get('/kelas/{kelas}/petunjuk-lkpd', [PetunjukLkpdController::class, 'index'])->name('kelas.petunjuk-lkpd');
    Route::put('/kelas/{kelas}/petunjuk-lkpd', [PetunjukLkpdController::class, 'update'])->name('kelas.petunjuk-lkpd.update');

    Route::get('/kelas/{kelas}/identitas-mapel', [IdentitasMapelController::class, 'index'])->name('kelas.identitas-mapel');
    Route::put('/kelas/{kelas}/identitas-mapel', [IdentitasMapelController::class, 'update'])->name('kelas.identitas-mapel.update');

    Route::get('/kelas/{kelas}/evaluasi-pembelajaran', [EvaluasiPembelajaranController::class, 'index'])->name('kelas.evaluasi-pembelajaran');
    Route::post('/kelas/{kelas}/evaluasi-pembelajaran', [EvaluasiPembelajaranController::class, 'store'])->name('kelas.evaluasi-pembelajaran.store');
    Route::put('/kelas/{kelas}/evaluasi-pembelajaran/{evaluasiPembelajaran}', [EvaluasiPembelajaranController::class, 'update'])->name('kelas.evaluasi-pembelajaran.update');
    Route::delete('/kelas/{kelas}/evaluasi-pembelajaran/{evaluasiPembelajaran}', [EvaluasiPembelajaranController::class, 'destroy'])->name('kelas.evaluasi-pembelajaran.destroy');

    Route::get('/kelas/{kelas}/daftar-pustaka', [DaftarPustakaController::class, 'index'])->name('kelas.daftar-pustaka');
    Route::put('/kelas/{kelas}/daftar-pustaka', [DaftarPustakaController::class, 'update'])->name('kelas.daftar-pustaka.update');

    Route::get('/kelas/{kelas}/profil-penulis', [ProfilPenulisController::class, 'index'])->name('kelas.profil-penulis');
    Route::match(['post', 'put'], '/kelas/{kelas}/profil-penulis', [ProfilPenulisController::class, 'update'])->name('kelas.profil-penulis.update');

    Route::get('/kelas/{kelas}/game', [GameController::class, 'index'])->name('kelas.game');
    Route::post('/kelas/{kelas}/game', [GameController::class, 'store'])->name('kelas.game.store');
    Route::put('/kelas/{kelas}/game/{game}', [GameController::class, 'update'])->name('kelas.game.update');
    Route::delete('/kelas/{kelas}/game/{game}', [GameController::class, 'destroy'])->name('kelas.game.destroy');

    Route::get('/kelas/{kelas}/kegiatan-pembelajaran', [KegiatanPembelajaranController::class, 'index'])->name('kelas.kegiatan-pembelajaran');
    Route::post('/kelas/{kelas}/kegiatan-pembelajaran', [KegiatanPembelajaranController::class, 'store'])->name('kelas.kegiatan-pembelajaran.store');
    Route::put('/kelas/{kelas}/kegiatan-pembelajaran', [KegiatanPembelajaranController::class, 'update'])->name('kelas.kegiatan-pembelajaran.update');
    Route::put('/kelas/{kelas}/kegiatan-pembelajaran/{item}/aktif', [KegiatanPembelajaranController::class, 'toggleActive'])->whereNumber('item')->name('kelas.kegiatan-pembelajaran.toggle-active');
    Route::delete('/kelas/{kelas}/kegiatan-pembelajaran/{item}', [KegiatanPembelajaranController::class, 'destroy'])->whereNumber('item')->name('kelas.kegiatan-pembelajaran.destroy');

    Route::get('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/builder', [LkpdKontenController::class, 'edit'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.builder');
    Route::put('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/builder', [LkpdKontenController::class, 'update'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.builder.update');
    Route::post('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/builder/media', [LkpdKontenController::class, 'uploadMedia'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.builder.media');
    Route::get('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/jawaban', [LkpdSubmissionController::class, 'index'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.submissions');
    Route::get('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/jawaban/export', [LkpdSubmissionController::class, 'export'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.submissions.export');
    Route::get('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/jawaban/{submission}', [LkpdSubmissionController::class, 'show'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.submissions.show');
    Route::put('/kelas/{kelas}/kegiatan-pembelajaran/{nomor}/jawaban/{submission}/nilai', [LkpdSubmissionController::class, 'updateNilai'])->whereNumber('nomor')->name('kelas.kegiatan-pembelajaran.submissions.nilai');
});

require __DIR__.'/settings.php';
