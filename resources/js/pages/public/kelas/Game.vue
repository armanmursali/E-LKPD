<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Brain, ExternalLink, Gamepad2, Puzzle, Rocket, Trophy } from '@lucide/vue';

type Game = { id: number; nama: string; deskripsi: string | null; wordwall_url: string; ikon: string };
const iconMap = { 'gamepad-2': Gamepad2, puzzle: Puzzle, trophy: Trophy, brain: Brain, rocket: Rocket };
defineProps<{ kelas: { public_token: string; nama: string }; games: Game[] }>();
</script>

<template>
    <Head title="Games" />
    <div class="games-page">
        <header class="games-header">
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="brand">
                <span class="brand-badge">E</span>
                <span>E-LKPD WEB</span>
            </Link>
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="back-pill">
                <ArrowLeft class="size-3.5" />
                Kembali ke Kelas
            </Link>
        </header>

        <main>
            <section class="games-hero">
                <span class="hero-icon-badge">
                    <Gamepad2 class="size-10" />
                </span>
                <p class="hero-kicker">{{ kelas.nama }}</p>
                <h1>Zona Games Interaktif</h1>
                <p class="hero-copy">Uji pemahamanmu sambil bermain melalui kuis interaktif di bawah ini!</p>
            </section>

            <section id="daftar-games" class="games-list">
                <div v-if="games.length" class="game-grid">
                    <article v-for="game in games" :key="game.id" class="game-card">
                        <div class="game-card-top">
                            <span class="game-icon">
                                <component :is="iconMap[game.ikon as keyof typeof iconMap] ?? Gamepad2" class="size-7" />
                            </span>
                            <span class="game-badge">Wordwall</span>
                        </div>
                        <div class="game-card-body">
                            <h3>{{ game.nama }}</h3>
                            <p>{{ game.deskripsi || 'Game interaktif Wordwall untuk menguji pemahaman materi kelas ini.' }}</p>
                        </div>
                        <a :href="game.wordwall_url" target="_blank" rel="noreferrer" class="start-button">
                            <span>Mulai Bermain</span>
                            <ExternalLink class="size-4" />
                        </a>
                    </article>
                </div>
                <div v-else class="empty-game">
                    <Gamepad2 class="mx-auto size-12 opacity-60" />
                    <p class="mt-3 font-bold">Belum ada game untuk kelas ini.</p>
                </div>
            </section>
        </main>
    </div>
</template>

<style scoped>
.games-page { min-height: 100vh; color: #351f13; background-color: #fdfaf5; background-image: radial-gradient(ellipse at center, transparent 0 13px, rgba(155, 113, 71, .07) 14px 16px, transparent 17px), radial-gradient(ellipse at center, transparent 0 11px, rgba(155, 113, 71, .05) 12px 14px, transparent 15px); background-size: 58px 48px, 58px 48px; }
.games-header { display: flex; justify-content: space-between; align-items: center; max-width: 1120px; margin: 0 auto; padding: 20px 24px; }
.brand { display: inline-flex; align-items: center; gap: 10px; color: #5e402a; font-size: 1rem; font-weight: 900; letter-spacing: .06em; text-decoration: none; }
.brand-badge { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; border-radius: 9px; background: #79563d; color: #fff; font-weight: 900; }
.back-pill { display: inline-flex; align-items: center; gap: 7px; border: 1px solid #e2cbb0; border-radius: 999px; background: #fff; color: #5e402a; padding: 7px 14px; font-size: .8rem; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(91, 57, 32, .06); transition: background .15s, transform .15s; }
.back-pill:hover { background: #f8efe4; transform: translateY(-1px); }
.games-hero { display: flex; flex-direction: column; align-items: center; padding: 24px 20px 42px; text-align: center; }
.hero-icon-badge { display: flex; width: 76px; height: 76px; align-items: center; justify-content: center; margin-bottom: 16px; border-radius: 24px; background: #f6e2c9; border: 2px solid #ddb27b; color: #79563d; box-shadow: 0 8px 20px rgba(121, 86, 61, .14); }
.hero-kicker { margin: 0 0 6px; color: #a87549; font-size: .78rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
.games-hero h1 { margin: 0; color: #5e402a; font-size: clamp(2rem, 4.5vw, 3rem); font-weight: 900; }
.hero-copy { max-width: 580px; margin: 10px 0 0; font-size: 1.05rem; color: #6e523d; line-height: 1.5; }
.games-list { width: min(100% - 32px, 1040px); margin: 0 auto; padding: 12px 0 80px; }
.game-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; text-align: left; }
.game-card { display: flex; flex-direction: column; justify-content: space-between; gap: 14px; border: 1px solid #e6d2bc; border-radius: 18px; background: #fff; padding: 20px; box-shadow: 0 6px 18px rgba(91, 57, 32, .08); transition: transform .2s, box-shadow .2s, border-color .2s; }
.game-card:hover { transform: translateY(-4px); border-color: #c79464; box-shadow: 0 12px 26px rgba(91, 57, 32, .14); }
.game-card-top { display: flex; align-items: center; justify-content: space-between; }
.game-icon { display: flex; width: 52px; height: 52px; align-items: center; justify-content: center; border: 2px solid #ddb27b; border-radius: 14px; background: #fdf3e6; color: #79563d; }
.game-badge { border-radius: 999px; background: #fdf3e6; padding: 4px 10px; color: #8c5c38; font-size: .72rem; font-weight: 800; }
.game-card-body h3 { margin: 0 0 6px; color: #432c1d; font-size: 1.1rem; font-weight: 800; line-height: 1.3; }
.game-card-body p { margin: 0; color: #735d4d; font-size: .86rem; line-height: 1.5; }
.start-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 12px; background: #79563d; color: white; padding: 11px 16px; font-size: .85rem; font-weight: 800; text-decoration: none; transition: transform .2s, background .2s; }
.start-button:hover { background: #5e402a; transform: translateY(-1px); }
.empty-game { border: 2px dashed #ddb27b; border-radius: 18px; background: rgba(255, 255, 255, 0.8); padding: 48px 24px; text-align: center; color: #79563d; }
</style>
