<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownToLine, BookOpen, Gamepad2 } from '@lucide/vue';
import { ref } from 'vue';
import KelasContentMenu from '@/components/KelasContentMenu.vue';

const imageFailed = ref(false);
const fallbackImage = 'https://api.dicebear.com/9.x/adventurer/svg?seed=student-learning';

const props = defineProps<{ kelas: { id: number; nama: string; deskripsi: string | null; public_token: string; public_hero_image: string | null; pemilik: string; mata_pelajaran: string }; section?: string | null }>();
</script>

<template>
    <Head :title="kelas.nama" />
    <div class="public-home">
        <header class="public-header">
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="brand">E-LKPD WEB</Link>
            <nav class="public-nav" aria-label="Navigasi siswa">
                <Link :href="`/kelas-publik/${kelas.public_token}`" class="active">Home</Link>
                <a href="#materi"><ArrowDownToLine class="size-4" /> Download LKPD (PDF)</a>
                <Link :href="`/kelas-publik/${kelas.public_token}/game`"><Gamepad2 class="size-4" /> Games</Link>
            </nav>
        </header>

        <section class="hero-section">
            <div class="hero-inner">
                <div class="hero-image-wrap">
                    <div class="image-backdrop" />
                    <img v-if="!imageFailed" class="hero-image" :class="props.kelas.public_hero_image ? 'custom-hero-image' : 'cartoon-image'" :src="props.kelas.public_hero_image || fallbackImage" alt="Gambar hero kelas" @error="imageFailed = true" />
                    <div v-else class="hero-fallback" aria-label="Ilustrasi kartun siswa"><span>📚</span><span>✏️</span><span>💡</span></div>
                </div>
                <div class="hero-copy">
                    <p class="hero-kicker">{{ kelas.mata_pelajaran }} · Oleh {{ kelas.pemilik }}</p>
                    <h1>{{ kelas.nama }}</h1>
                    <p class="hero-subtitle">Belajar aktif, berpikir kritis, dan menemukan solusi melalui kegiatan pembelajaran yang menyenangkan.</p>
                    <p v-if="kelas.deskripsi" class="hero-description">{{ kelas.deskripsi }}</p>
                    <a href="#materi" class="hero-button"><BookOpen class="size-4" /> Baca Panduan LKPD</a>
                </div>
            </div>
            <div class="wave" />
        </section>

        <main id="materi" class="content-section">
            <div class="section-heading"><p>Jelajahi isi kelas</p><h2>Materi LKPD</h2></div>
            <KelasContentMenu :kelas-id="kelas.id" :public-token="kelas.public_token" />
        </main>
    </div>
</template>

<style scoped>
.public-home { min-height: 100vh; overflow-x: hidden; color: #fff; background: #fdfcfb; }
.public-header { position: relative; z-index: 2; display: flex; align-items: center; justify-content: space-between; max-width: 1220px; margin: 0 auto; padding: 16px 28px; }
.brand { color: #fff; font-size: 1.2rem; font-weight: 800; letter-spacing: .03em; text-decoration: none; }
.public-nav { display: flex; align-items: center; gap: 20px; }
.public-nav a { display: inline-flex; align-items: center; gap: 5px; color: #fff; font-size: .9rem; font-weight: 600; text-decoration: none; }
.public-nav a:hover, .public-nav .active { color: #5e402a; }
.hero-section { position: relative; min-height: 650px; overflow: hidden; background-color: #ddb27b; background-image: linear-gradient(rgba(255,255,255,.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.14) 1px, transparent 1px); background-size: 32px 32px; }
.hero-inner { display: grid; width: min(100% - 48px, 1080px); min-height: 570px; grid-template-columns: 1fr 1fr; align-items: center; gap: 48px; margin: 0 auto; padding: 30px 0 90px; }
.hero-image-wrap { position: relative; display: flex; align-items: center; justify-content: center; min-height: 360px; animation: float-image 5s ease-in-out infinite; }
.image-backdrop { position: absolute; width: 75%; height: 75%; border-radius: 48% 52% 45% 55%; background: rgba(242, 213, 170, .62); transform: rotate(-8deg); }
.hero-image { position: relative; width: min(100%, 475px); height: 330px; border: 8px solid rgba(255,255,255,.7); border-radius: 42% 18% 38% 20%; object-fit: cover; box-shadow: 0 18px 26px rgba(100, 61, 32, .22); transform: rotate(2deg); }
.cartoon-image { object-fit: contain; padding: 18px; background: rgba(255,255,255,.78); }
.custom-hero-image { object-fit: cover; }
.hero-fallback { position: relative; display: flex; width: min(100%, 330px); height: 260px; align-items: center; justify-content: center; gap: 14px; border: 8px solid rgba(255,255,255,.7); border-radius: 42% 18% 38% 20%; background: #f6e2c9; box-shadow: 0 18px 26px rgba(100, 61, 32, .22); font-size: 3.5rem; transform: rotate(2deg); }
.hero-copy { position: relative; z-index: 1; color: #fff; }
.hero-kicker { margin: 0 0 10px; color: #765037; font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
.hero-copy h1 { max-width: 560px; margin: 0; color: #fff; font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 800; line-height: 1.08; }
.hero-subtitle { max-width: 500px; margin: 16px 0 0; color: #fff; font-size: 1.15rem; font-weight: 600; line-height: 1.45; }
.hero-description { max-width: 470px; margin: 10px 0 0; color: rgba(74, 48, 31, .78); line-height: 1.5; }
.hero-button { display: inline-flex; align-items: center; gap: 8px; margin-top: 24px; border-radius: 999px; background: #79563d; color: #fff; padding: 12px 20px; font-size: .9rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 0 rgba(91, 57, 32, .16); transition: transform .2s, background .2s; }
.hero-button:hover { background: #5e402a; transform: translateY(-2px); }
.wave { position: absolute; right: -4%; bottom: -1px; left: -4%; height: 105px; border-radius: 50% 50% 0 0 / 55% 55% 0 0; background: #fdfcfb; transform: rotate(-2deg); }
.content-section { max-width: 1080px; margin: 0 auto; padding: 58px 24px 80px; }
.section-heading { margin-bottom: 28px; color: #79563d; text-align: center; }
.section-heading p { margin: 0 0 5px; color: #a87549; font-size: .78rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
.section-heading h2 { margin: 0; font-size: 2rem; }
@keyframes float-image { 0%, 100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-8px) rotate(1deg); } }
@media (max-width: 720px) { .public-header { padding: 14px 18px; } .public-nav { gap: 9px; } .public-nav a { font-size: 0; } .public-nav a svg { width: 18px; height: 18px; } .public-nav a:first-child { display: inline-flex; font-size: .8rem; } .hero-inner { width: min(100% - 32px, 560px); grid-template-columns: 1fr; gap: 4px; padding-top: 25px; text-align: center; } .hero-image-wrap { min-height: 240px; } .hero-image { width: 82%; height: 230px; } .hero-copy { display: flex; flex-direction: column; align-items: center; } .hero-copy h1 { font-size: 2.2rem; } .hero-subtitle { font-size: 1rem; } .wave { height: 72px; } }
</style>
