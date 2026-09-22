<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, BarChart3, Check, ClipboardCheck, Menu, X } from '@lucide/vue';
import { ref } from 'vue';
import { dashboard, login, register } from '@/routes';

const page = usePage<{ auth: { user: { id: number; name: string; email: string } | null } }>();
const mobileMenuOpen = ref(false);
</script>

<template>
    <Head title="E-LKPD Web">
        <meta name="description" content="Platform pembelajaran digital untuk membuat LKPD interaktif dan memantau perkembangan siswa." />
    </Head>
    <div class="min-h-screen overflow-hidden bg-[#fffaf2] text-stone-900 dark:bg-[#17120f] dark:text-stone-100">
        <header class="sticky top-0 z-40 border-b border-amber-900/10 bg-[#fffaf2]/90 backdrop-blur dark:border-amber-100/10 dark:bg-[#17120f]/90">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 sm:px-8">
                <Link href="/" class="flex items-center gap-2" aria-label="E-LKPD Web beranda">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-amber-800 text-lg font-black text-white">E</span>
                    <span class="text-sm font-black tracking-[0.16em] text-amber-950 dark:text-amber-100">E-LKPD WEB</span>
                </Link>
                <nav class="hidden items-center gap-7 text-sm font-semibold text-stone-600 md:flex dark:text-stone-300">
                    <a href="#tentang" class="transition hover:text-amber-700 dark:hover:text-amber-300">Tentang</a>
                    <a href="#manfaat" class="transition hover:text-amber-700 dark:hover:text-amber-300">Manfaat</a>
                    <a href="#cara-kerja" class="transition hover:text-amber-700 dark:hover:text-amber-300">Cara kerja</a>
                </nav>
                <div class="hidden items-center gap-2 sm:flex">
                    <template v-if="page.props.auth.user">
                        <Link :href="dashboard()" class="rounded-md px-3 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-100 dark:text-amber-300 dark:hover:bg-amber-950/50">Dashboard</Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" class="rounded-md px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-amber-100 dark:text-stone-200 dark:hover:bg-amber-950/50">Masuk</Link>
                        <a href="/auth/google" class="inline-flex items-center gap-2 rounded-md bg-amber-800 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-md"><span class="rounded bg-white px-1 text-xs font-black text-amber-800">G</span> Masuk dengan Google</a>
                    </template>
                </div>
                <button type="button" class="flex size-9 items-center justify-center rounded-md text-amber-900 hover:bg-amber-100 sm:hidden dark:text-amber-100 dark:hover:bg-amber-950/50" aria-label="Buka menu" @click="mobileMenuOpen = true"><Menu class="size-5" /></button>
            </div>
            <div v-if="mobileMenuOpen" class="border-t border-amber-900/10 px-5 py-4 sm:hidden dark:border-amber-100/10">
                <div class="flex flex-col gap-2">
                    <a href="#tentang" class="rounded-md px-3 py-2 text-sm font-semibold hover:bg-amber-100 dark:hover:bg-amber-950/50" @click="mobileMenuOpen = false">Tentang</a>
                    <a href="#manfaat" class="rounded-md px-3 py-2 text-sm font-semibold hover:bg-amber-100 dark:hover:bg-amber-950/50" @click="mobileMenuOpen = false">Manfaat</a>
                    <a href="#cara-kerja" class="rounded-md px-3 py-2 text-sm font-semibold hover:bg-amber-100 dark:hover:bg-amber-950/50" @click="mobileMenuOpen = false">Cara kerja</a>
                    <template v-if="!page.props.auth.user"><Link :href="login()" class="rounded-md px-3 py-2 text-sm font-semibold" @click="mobileMenuOpen = false">Masuk dengan email</Link><a href="/auth/google" class="mt-1 inline-flex items-center justify-center gap-2 rounded-md bg-amber-800 px-3 py-2.5 text-sm font-bold text-white"><span class="rounded bg-white px-1 text-xs font-black text-amber-800">G</span> Masuk dengan Google</a></template>
                    <Link v-else :href="dashboard()" class="rounded-md bg-amber-800 px-3 py-2.5 text-center text-sm font-bold text-white">Buka dashboard</Link>
                </div>
                <button type="button" class="absolute top-4 right-5 hidden" aria-label="Tutup menu" @click="mobileMenuOpen = false"><X class="size-5" /></button>
            </div>
        </header>

        <main>
            <section id="tentang" class="relative mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-28">
                <div class="relative z-10">
                    <p class="inline-flex items-center gap-2 rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-amber-900 dark:border-amber-700 dark:bg-amber-950/50 dark:text-amber-200"><span class="size-1.5 rounded-full bg-amber-600" /> Ruang belajar yang lebih hidup</p>
                    <h1 class="mt-6 max-w-3xl text-4xl font-black leading-[1.05] tracking-tight text-amber-950 sm:text-6xl dark:text-amber-50">Buat LKPD interaktif. <span class="text-amber-700 dark:text-amber-400">Pantau kemajuan siswa.</span></h1>
                    <p class="mt-6 max-w-xl text-base leading-8 text-stone-600 sm:text-lg dark:text-stone-300">E-LKPD Web membantu guru menyusun pengalaman belajar digital yang rapi, menarik, dan mudah dievaluasi dalam satu ruang kerja.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row"><Link :href="page.props.auth.user ? dashboard() : register()" class="inline-flex items-center justify-center gap-2 rounded-md bg-amber-800 px-5 py-3 font-bold text-white transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-lg">Mulai membuat LKPD <ArrowRight class="size-4" /></Link><a href="/auth/google" v-if="!page.props.auth.user" class="inline-flex items-center justify-center gap-2 rounded-md border border-amber-300 bg-white px-5 py-3 font-bold text-amber-900 transition hover:border-amber-500 hover:bg-amber-50 dark:border-amber-700 dark:bg-transparent dark:text-amber-200 dark:hover:bg-amber-950/50"><span class="rounded bg-amber-800 px-1 text-xs font-black text-white">G</span> Masuk dengan Google</a></div>
                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-stone-500 dark:text-stone-400"><span class="inline-flex items-center gap-2"><Check class="size-4 text-amber-700" /> Tanpa instalasi</span><span class="inline-flex items-center gap-2"><Check class="size-4 text-amber-700" /> Siap dibagikan</span></div>
                </div>
                <div class="relative mx-auto w-full max-w-lg"><div class="absolute -inset-5 rounded-[2rem] bg-amber-200/50 blur-2xl dark:bg-amber-900/20" /><div class="relative overflow-hidden rounded-2xl border border-amber-900/10 bg-[#3b2d24] p-5 shadow-2xl dark:border-amber-100/10"><div class="flex items-center justify-between border-b border-white/15 pb-4 text-white"><span class="text-xs font-bold tracking-[0.16em] text-amber-200">LKPD AKTIF</span><span class="rounded-full bg-amber-500/20 px-2 py-1 text-xs text-amber-200">Kelas 7</span></div><div class="mt-5 rounded-xl bg-[#fffaf2] p-5 text-stone-900"><p class="text-xs font-bold uppercase tracking-widest text-amber-700">Kegiatan pembelajaran</p><h2 class="mt-2 text-2xl font-black">Eksplorasi ekosistem</h2><div class="mt-5 space-y-3"><div class="h-3 w-4/5 rounded-full bg-amber-200" /><div class="h-3 w-full rounded-full bg-stone-200" /><div class="h-20 rounded-lg border-2 border-dashed border-amber-300 bg-amber-50" /></div><div class="mt-5 flex items-center justify-between"><span class="text-xs font-semibold text-stone-500">Jawaban tersimpan</span><span class="rounded-md bg-amber-800 px-3 py-1.5 text-xs font-bold text-white">Kirim</span></div></div></div></div>
            </section>

            <section id="manfaat" class="border-y border-amber-900/10 bg-white/70 px-5 py-16 sm:px-8 dark:border-amber-100/10 dark:bg-[#1d1713]">
                <div class="mx-auto max-w-7xl"><div class="max-w-2xl"><p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Satu alur, lebih terarah</p><h2 class="mt-3 text-3xl font-black tracking-tight text-amber-950 sm:text-4xl dark:text-amber-50">Dari materi sampai evaluasi, semuanya terasa dekat.</h2></div><div class="mt-10 grid gap-4 md:grid-cols-3"><article class="rounded-xl border border-amber-200 bg-[#fffaf2] p-6 transition hover:-translate-y-1 hover:border-amber-400 hover:shadow-lg dark:border-amber-900/60 dark:bg-[#251b16]"><ClipboardCheck class="size-7 text-amber-700 dark:text-amber-400" /><h3 class="mt-5 text-lg font-bold text-amber-950 dark:text-amber-50">Bangun aktivitas</h3><p class="mt-2 text-sm leading-7 text-stone-600 dark:text-stone-300">Susun teks, gambar, video, pertanyaan, dan permainan dalam LKPD yang mudah dipahami.</p></article><article class="rounded-xl border border-amber-200 bg-[#fffaf2] p-6 transition hover:-translate-y-1 hover:border-amber-400 hover:shadow-lg dark:border-amber-900/60 dark:bg-[#251b16]"><BarChart3 class="size-7 text-amber-700 dark:text-amber-400" /><h3 class="mt-5 text-lg font-bold text-amber-950 dark:text-amber-50">Baca perkembangan</h3><p class="mt-2 text-sm leading-7 text-stone-600 dark:text-stone-300">Lihat nilai per tugas dan tren setiap siswa untuk menentukan pendampingan berikutnya.</p></article><article class="rounded-xl border border-amber-200 bg-[#fffaf2] p-6 transition hover:-translate-y-1 hover:border-amber-400 hover:shadow-lg dark:border-amber-900/60 dark:bg-[#251b16]"><ArrowRight class="size-7 text-amber-700 dark:text-amber-400" /><h3 class="mt-5 text-lg font-bold text-amber-950 dark:text-amber-50">Bagikan dengan mudah</h3><p class="mt-2 text-sm leading-7 text-stone-600 dark:text-stone-300">Bagikan kelas melalui tautan atau QR code agar siswa bisa langsung mulai belajar.</p></article></div></div>
            </section>

            <section id="cara-kerja" class="mx-auto max-w-7xl px-5 py-20 sm:px-8"><div class="grid gap-10 lg:grid-cols-[0.7fr_1.3fr] lg:items-center"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Cara kerja</p><h2 class="mt-3 text-3xl font-black text-amber-950 sm:text-4xl dark:text-amber-50">Mulai dari satu kelas kecil.</h2><p class="mt-4 leading-8 text-stone-600 dark:text-stone-300">Buat kelas, susun LKPD, bagikan akses, lalu gunakan data jawaban untuk melihat siapa yang perlu dibantu.</p></div><div class="grid gap-3 sm:grid-cols-3"><div class="border-l-2 border-amber-600 px-5 py-3"><span class="text-3xl font-black text-amber-700">01</span><p class="mt-3 font-bold text-amber-950 dark:text-amber-50">Buat kelas</p></div><div class="border-l-2 border-amber-600 px-5 py-3"><span class="text-3xl font-black text-amber-700">02</span><p class="mt-3 font-bold text-amber-950 dark:text-amber-50">Rancang LKPD</p></div><div class="border-l-2 border-amber-600 px-5 py-3"><span class="text-3xl font-black text-amber-700">03</span><p class="mt-3 font-bold text-amber-950 dark:text-amber-50">Pantau hasil</p></div></div></div></section>
            <section class="bg-amber-800 px-5 py-16 text-center text-white sm:px-8"><h2 class="text-3xl font-black sm:text-4xl">Siap membuat pembelajaran lebih aktif?</h2><p class="mx-auto mt-3 max-w-xl text-amber-100">Bangun ruang belajar digital yang bisa langsung digunakan hari ini.</p><Link :href="page.props.auth.user ? dashboard() : register()" class="mt-7 inline-flex items-center gap-2 rounded-md bg-white px-5 py-3 font-bold text-amber-900 transition hover:-translate-y-0.5 hover:bg-amber-50">Mulai sekarang <ArrowRight class="size-4" /></Link></section>
        </main>
        <footer class="border-t border-amber-900/10 px-5 py-6 text-center text-sm text-stone-500 sm:px-8 dark:border-amber-100/10 dark:text-stone-400">E-LKPD Web · Ruang kerja digital untuk pembelajaran yang lebih bermakna.</footer>
    </div>
</template>
