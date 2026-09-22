<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil } from '@lucide/vue';
import BagikanKelas from '@/components/BagikanKelas.vue';
import KelasContentMenu from '@/components/KelasContentMenu.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Detail Kelas',
                
            },
        ],
    },
});
const props = defineProps<{ kelas: { id: number; nama: string; deskripsi: string | null; public_token: string } }>();

</script>

<template>
    <Head :title="kelas.nama" />
    <div class="min-h-screen flex-1 bg-amber-50/60 px-3 py-4 sm:px-4 sm:py-5 md:px-8 md:py-8">
        <div class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between">
                <Link href="/kelas" class="text-sm font-semibold text-amber-900 transition hover:text-amber-700 hover:underline dark:text-amber-100 dark:hover:text-amber-300">Kembali ke daftar kelas</Link>
                <span class="hidden text-sm font-extrabold tracking-wide text-amber-900 dark:text-amber-100 sm:block">E-LKPD WEB</span>
            </div>

            <div class="mt-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-bold tracking-[0.18em] text-amber-700 uppercase">Ruang kerja LKPD</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50">{{ kelas.nama }}</h1>
                    <p v-if="kelas.deskripsi" class="mt-2 max-w-2xl text-sm leading-relaxed text-amber-900/70 dark:text-amber-100/70">{{ kelas.deskripsi }}</p>
                </div>
                <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:justify-end">
                    <Link
                        :href="`/kelas/${kelas.id}/edit`"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-md bg-amber-800 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-900 sm:flex-none sm:px-4"
                    >
                        <Pencil class="size-4" />
                        Edit kelas
                    </Link>
                    <BagikanKelas :token="kelas.public_token" />
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-amber-200 bg-white/85 p-4 shadow-sm sm:p-6 dark:border-amber-900/60 dark:bg-card">
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-px flex-1 bg-amber-200" />
                    <h2 class="text-sm font-bold tracking-[0.16em] text-amber-800 uppercase">Konten kelas</h2>
                    <span class="h-px flex-1 bg-amber-200" />
                </div>

                <KelasContentMenu :kelas-id="kelas.id" />
            </div>
        </div>
    </div>
</template>
