<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Brain, Gamepad2, Puzzle, Rocket, Trophy } from '@lucide/vue';

type Game = { id: number; nama: string; deskripsi: string | null; wordwall_url: string; ikon: string };
const iconMap = { 'gamepad-2': Gamepad2, puzzle: Puzzle, trophy: Trophy, brain: Brain, rocket: Rocket };
defineProps<{ kelas: { public_token: string; nama: string }; games: Game[] }>();
</script>

<template>
    <Head title="Games" />
    <div class="games-page">
        <header class="games-header">
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="brand">E-LKPD WEB</Link>
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="back-link">Kembali</Link>
        </header>

        <main>
            <section class="games-hero">
                <p class="hero-kicker">{{ kelas.nama }}</p>
                <h1>Games</h1>
                <p class="hero-copy">Berikut disediakan games yang dapat Anda akses melalui tombol di bawah ini</p>
                <Gamepad2 class="hero-game-icon" :stroke-width="1.15" />
                <a href="#daftar-games" class="play-button">Play Games</a>
            </section>

            <section id="daftar-games" class="games-list">
                <h2>Pilihan Games</h2>
                <p class="list-copy">Pilih permainan yang ingin kamu kerjakan.</p>
                <div v-if="games.length" class="game-grid">
                    <article v-for="game in games" :key="game.id" class="game-card">
                        <span class="game-icon"><component :is="iconMap[game.ikon as keyof typeof iconMap] ?? Gamepad2" class="size-7" /></span>
                        <div class="game-card-body"><h3>{{ game.nama }}</h3><p>{{ game.deskripsi || 'Game Wordwall untuk kelas ini.' }}</p></div>
                        <a :href="game.wordwall_url" target="_blank" rel="noreferrer" class="start-button">Mulai Game ↗</a>
                    </article>
                </div>
                <div v-else class="empty-game">Belum ada game untuk kelas ini.</div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.games-page { min-height: 100vh; color: #351f13; background-color: #fff; background-image: radial-gradient(ellipse at center, transparent 0 13px, rgba(155, 113, 71, .07) 14px 16px, transparent 17px), radial-gradient(ellipse at center, transparent 0 11px, rgba(155, 113, 71, .05) 12px 14px, transparent 15px); background-size: 58px 48px, 58px 48px; }
.games-header { display: flex; justify-content: space-between; align-items: center; padding: 18px clamp(22px, 6vw, 80px); }
.brand, .back-link { color: #79563d; font-weight: 800; text-decoration: none; }
.brand { font-size: 1.1rem; letter-spacing: .04em; }
.back-link { font-size: .9rem; }
.back-link:hover { text-decoration: underline; }
.games-hero { display: flex; min-height: 570px; flex-direction: column; align-items: center; padding: 20px 20px 70px; text-align: center; }
.hero-kicker { margin: 0 0 4px; color: #a87549; font-size: .78rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
.games-hero h1 { margin: 0; color: #9b7147; font-size: clamp(2.25rem, 5vw, 3.4rem); font-weight: 800; }
.hero-copy { max-width: 720px; margin: 8px 0 0; font-size: clamp(1rem, 2vw, 1.3rem); color: #241b17; }
.hero-game-icon { width: min(38vw, 245px); height: min(38vw, 245px); margin: 38px 0 28px; color: #ac7042; fill: #ac7042; filter: drop-shadow(0 8px 0 rgba(121, 86, 61, .08)); }
.play-button, .start-button { border-radius: 999px; background: #79563d; color: white; font-weight: 700; text-decoration: none; transition: transform .2s, background .2s; }
.play-button { padding: 10px 24px; font-size: .95rem; }
.play-button:hover, .start-button:hover { background: #5f412e; transform: translateY(-2px); }
.games-list { width: min(100% - 32px, 980px); margin: 0 auto; padding: 54px 0 70px; border-top: 1px solid rgba(155, 113, 71, .18); text-align: center; }
.games-list h2 { margin: 0; color: #79563d; font-size: 1.7rem; }
.list-copy { margin: 7px 0 24px; color: #735d4d; }
.game-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 210px)); justify-content: center; gap: 10px; text-align: left; }
.game-card { display: grid; min-height: 145px; aspect-ratio: 1 / .82; grid-template-columns: 38px 1fr; gap: 8px; align-items: start; border: 1px solid #ead8c5; border-radius: 8px; background: rgba(255, 255, 255, .94); padding: 9px; box-shadow: 0 3px 10px rgba(91, 57, 32, .08); }
.game-icon { display: flex; width: 38px; height: 38px; align-items: center; justify-content: center; border: 2px solid #c79464; border-radius: 10px; background: #f6e2c9; color: #87542f; box-shadow: inset 0 -3px 0 rgba(135, 84, 47, .12); }
.game-card-body { min-width: 0; }
.game-card h3 { margin: 2px 0 3px; overflow: hidden; color: #4f3524; font-size: .95rem; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
.game-card p { display: -webkit-box; min-height: 30px; margin: 0; overflow: hidden; color: #806c5b; font-size: .78rem; line-height: 1.25; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
.start-button { grid-column: 1 / -1; align-self: end; padding: 6px 8px; text-align: center; font-size: .72rem; }
.empty-game { padding: 30px; color: #79563d; }
@media (max-width: 600px) { .games-hero { min-height: 500px; padding-top: 10px; } .hero-game-icon { margin-top: 32px; } .games-list { width: min(100% - 24px, 980px); } }
</style>
