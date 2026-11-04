<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, ExternalLink, Pencil } from '@lucide/vue';
import BagikanKelas from '@/components/BagikanKelas.vue';
import KelasContentMenu from '@/components/KelasContentMenu.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Kelas',
                href: '/kelas',
            },
            {
                title: 'Detail Kelas',
            },
        ],
    },
});
defineProps<{ kelas: { id: number; nama: string; deskripsi: string | null; public_token: string } }>();
</script>

<template>
    <Head :title="kelas.nama" />
    <div class="min-h-screen flex-1 bg-amber-50/60 px-3 py-4 sm:px-5 sm:py-6 md:px-8 md:py-8 dark:bg-background">
        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <Link
                    href="/kelas"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke daftar kelas
                </Link>
                <a
                    :href="`/kelas-publik/${kelas.public_token}`"
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 hover:underline dark:text-amber-300"
                >
                    Lihat tampilan siswa <ExternalLink class="size-3.5" />
                </a>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-white via-white to-amber-50/90 p-5 shadow-sm sm:p-7 dark:border-amber-900/60 dark:from-card dark:via-card dark:to-card">
                <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                    <div class="flex items-start gap-4">
                        <span class="hidden size-12 shrink-0 items-center justify-center rounded-2xl bg-amber-800 text-white shadow-sm sm:flex">
                            <BookOpen class="size-6" />
                        </span>
                        <div>
                            <p class="text-xs font-bold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400">Ruang kerja LKPD</p>
                            <h1 class="mt-1 text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50">{{ kelas.nama }}</h1>
                            <p v-if="kelas.deskripsi" class="mt-2 max-w-2xl text-sm leading-relaxed text-amber-900/70 dark:text-amber-100/70">{{ kelas.deskripsi }}</p>
                        </div>
                    </div>
                    <div class="flex w-full flex-wrap gap-2.5 sm:w-auto sm:justify-end">
                        <Link
                            :href="`/kelas/${kelas.id}/edit`"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700 sm:flex-none"
                        >
                            <Pencil class="size-4" />
                            Edit kelas
                        </Link>
                        <BagikanKelas :token="kelas.public_token" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-white/95 p-5 shadow-sm sm:p-7 dark:border-amber-900/60 dark:bg-card">
                <div class="mb-6 flex items-center gap-3">
                    <span class="h-px flex-1 bg-amber-200/80 dark:bg-border" />
                    <h2 class="text-xs font-extrabold tracking-[0.18em] text-amber-800 uppercase dark:text-amber-300">Menu Modul & Konten Kelas</h2>
                    <span class="h-px flex-1 bg-amber-200/80 dark:bg-border" />
                </div>

                <KelasContentMenu :kelas-id="kelas.id" />
            </div>
        </div>
    </div>
</template>
