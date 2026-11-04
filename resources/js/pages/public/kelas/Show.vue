<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Compass, Gamepad2, Sparkles } from '@lucide/vue';
import { ref } from 'vue';
import KelasContentMenu from '@/components/KelasContentMenu.vue';

const imageFailed = ref(false);
const fallbackImage = 'https://api.dicebear.com/9.x/adventurer/svg?seed=student-learning';

const props = defineProps<{
    kelas: {
        id: number;
        nama: string;
        deskripsi: string | null;
        public_token: string;
        public_hero_image: string | null;
        pemilik: string;
        mata_pelajaran: string;
    };
    section?: string | null;
}>();
</script>

<template>
    <Head :title="kelas.nama" />
    <div class="public-home">
        <header class="public-header">
            <Link :href="`/kelas-publik/${kelas.public_token}`" class="brand">
                <span class="brand-badge">E</span>
                <span>E-LKPD WEB</span>
            </Link>
            <nav class="public-nav" aria-label="Navigasi siswa">
                <Link :href="`/kelas-publik/${kelas.public_token}`" class="nav-pill active">Beranda</Link>
                <a href="#materi" class="nav-pill"><Compass class="size-4" /><span>Materi LKPD</span></a>
                <Link :href="`/kelas-publik/${kelas.public_token}/game`" class="nav-pill"><Gamepad2 class="size-4" /><span>Games</span></Link>
            </nav>
        </header>

        <section class="hero-section">
            <div class="hero-inner">
                <div class="hero-image-wrap">
                    <div class="image-backdrop" />
                    <img
                        v-if="!imageFailed"
                        class="hero-image"
                        :class="props.kelas.public_hero_image ? 'custom-hero-image' : 'cartoon-image'"
                        :src="props.kelas.public_hero_image || fallbackImage"
                        alt="Gambar hero kelas"
                        @error="imageFailed = true"
                    />
                    <div v-else class="hero-fallback" aria-label="Ilustrasi kartun siswa"><span>📚</span><span>✏️</span><span>💡</span></div>
                </div>
                <div class="hero-copy">
                    <p class="hero-kicker">
                        <Sparkles class="size-3.5" />
                        <span>{{ kelas.mata_pelajaran }} · Oleh {{ kelas.pemilik }}</span>
                    </p>
                    <h1>{{ kelas.nama }}</h1>
                    <p class="hero-subtitle">Belajar aktif, berpikir kritis, dan menemukan solusi melalui kegiatan pembelajaran yang menyenangkan.</p>
                    <p v-if="kelas.deskripsi" class="hero-description">{{ kelas.deskripsi }}</p>
                    <div class="hero-actions">
                        <a href="#materi" class="hero-button"><BookOpen class="size-4" /> Mulai Jelajahi Materi</a>
                        <Link :href="`/kelas-publik/${kelas.public_token}/kegiatan-pembelajaran`" class="hero-button-secondary">
                            Buka Lembar Kerja
                        </Link>
                    </div>
                </div>
            </div>
            <div class="wave" />
        </section>

        <main id="materi" class="content-section">
            <div class="section-heading">
                <p>Jelajahi isi kelas</p>
                <h2>Modul & Materi LKPD</h2>
            </div>
            <KelasContentMenu :kelas-id="kelas.id" :public-token="kelas.public_token" />
        </main>
    </div>
</template>

<style scoped>
.public-home { min-height: 100vh; overflow-x: hidden; color: #fff; background: #fdfaf5; }
.public-header { position: relative; z-index: 2; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; max-width: 1220px; margin: 0 auto; padding: 18px 28px; }
.brand { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-size: 1.05rem; font-weight: 900; letter-spacing: .08em; text-decoration: none; }
.brand-badge { display: inline-flex; width: 34px; height: 34px; align-items: center; justify-content: center; border-radius: 10px; background: #5e402a; color: #fff; font-size: 1rem; font-weight: 900; box-shadow: 0 3px 8px rgba(60, 35, 18, 0.22); }
.public-nav { display: flex; align-items: center; gap: 8px; }
.nav-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 7px 14px; color: #fff; background: rgba(94, 64, 42, 0.18); font-size: .84rem; font-weight: 700; text-decoration: none; transition: background .2s, transform .2s; }
.nav-pill:hover, .nav-pill.active { background: #5e402a; color: #fff; transform: translateY(-1px); }
.hero-section { position: relative; min-height: 620px; overflow: hidden; margin-top: -74px; padding-top: 74px; background-color: #ddb27b; background-image: linear-gradient(rgba(255,255,255,.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.14) 1px, transparent 1px); background-size: 32px 32px; }
.hero-inner { display: grid; width: min(100% - 48px, 1080px); min-height: 530px; grid-template-columns: 0.95fr 1.05fr; align-items: center; gap: 48px; margin: 0 auto; padding: 28px 0 95px; }
.hero-image-wrap { position: relative; display: flex; align-items: center; justify-content: center; min-height: 340px; animation: float-image 5s ease-in-out infinite; }
.image-backdrop { position: absolute; width: 78%; height: 78%; border-radius: 48% 52% 45% 55%; background: rgba(242, 213, 170, .68); transform: rotate(-8deg); }
.hero-image { position: relative; width: min(100%, 450px); height: 320px; border: 8px solid rgba(255,255,255,.8); border-radius: 42% 18% 38% 20%; object-fit: cover; box-shadow: 0 20px 32px rgba(100, 61, 32, .24); transform: rotate(2deg); }
.cartoon-image { object-fit: contain; padding: 18px; background: rgba(255,255,255,.85); }
.custom-hero-image { object-fit: cover; }
.hero-fallback { position: relative; display: flex; width: min(100%, 330px); height: 260px; align-items: center; justify-content: center; gap: 14px; border: 8px solid rgba(255,255,255,.7); border-radius: 42% 18% 38% 20%; background: #f6e2c9; box-shadow: 0 18px 26px rgba(100, 61, 32, .22); font-size: 3.5rem; transform: rotate(2deg); }
.hero-copy { position: relative; z-index: 1; color: #fff; }
.hero-kicker { display: inline-flex; align-items: center; gap: 6px; margin: 0 0 14px; border-radius: 999px; background: rgba(255, 255, 255, 0.25); padding: 6px 14px; color: #5e402a; font-size: .75rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
.hero-copy h1 { max-width: 560px; margin: 0; color: #fff; font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 900; line-height: 1.08; text-shadow: 0 2px 12px rgba(94, 64, 42, 0.15); }
.hero-subtitle { max-width: 500px; margin: 16px 0 0; color: #fff; font-size: 1.1rem; font-weight: 600; line-height: 1.5; }
.hero-description { max-width: 470px; margin: 10px 0 0; color: rgba(74, 48, 31, .85); font-size: 0.95rem; line-height: 1.55; }
.hero-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-top: 26px; }
.hero-button { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; background: #5e402a; color: #fff; padding: 13px 22px; font-size: .9rem; font-weight: 800; text-decoration: none; box-shadow: 0 6px 16px rgba(91, 57, 32, .25); transition: transform .2s, background .2s; }
.hero-button:hover { background: #48301f; transform: translateY(-2px); }
.hero-button-secondary { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; border: 2px solid rgba(255, 255, 255, 0.8); background: rgba(255, 255, 255, 0.2); color: #5e402a; padding: 11px 20px; font-size: .9rem; font-weight: 800; text-decoration: none; transition: transform .2s, background .2s; }
.hero-button-secondary:hover { background: #fff; transform: translateY(-2px); }
.wave { position: absolute; right: -4%; bottom: -1px; left: -4%; height: 96px; border-radius: 50% 50% 0 0 / 55% 55% 0 0; background: #fdfaf5; transform: rotate(-1.5deg); }
.content-section { max-width: 1080px; margin: 0 auto; padding: 48px 24px 80px; }
.section-heading { margin-bottom: 32px; color: #5e402a; text-align: center; }
.section-heading p { margin: 0 0 6px; color: #a87549; font-size: .78rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
.section-heading h2 { margin: 0; font-size: 2rem; font-weight: 900; }
@keyframes float-image { 0%, 100% { transform: translateY(0) rotate(0); } 50% { transform: translateY(-8px) rotate(1deg); } }
@media (max-width: 720px) {
    .public-header { padding: 14px 16px; }
    .nav-pill { padding: 6px 11px; font-size: .78rem; }
    .hero-inner { width: min(100% - 32px, 560px); grid-template-columns: 1fr; gap: 16px; padding-top: 20px; text-align: center; }
    .hero-image-wrap { min-height: 230px; }
    .hero-image { width: 82%; height: 220px; }
    .hero-copy { display: flex; flex-direction: column; align-items: center; }
    .hero-copy h1 { font-size: 2.1rem; }
    .hero-subtitle { font-size: 1rem; }
    .hero-actions { justify-content: center; }
    .wave { height: 64px; }
}
</style>
