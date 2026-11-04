<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ClipboardList, Eye, EyeOff, FileEdit, Microscope, Play, Trash2, Video } from '@lucide/vue';
import type { Component } from 'vue';

export type KegiatanItem = {
    id: number;
    nomor: number;
    icon: string;
    judul: string;
    bahan_ajar_label: string;
    aktif?: boolean;
};

defineProps<{
    kelasId: number;
    items: KegiatanItem[];
    publicMode?: boolean;
    publicToken?: string;
}>();

const iconMap: Record<string, Component> = {
    video: Video,
    microscope: Microscope,
};

function hapus(kelasId: number, item: KegiatanItem) {
    if (!confirm(`Hapus "${item.judul}"?`)) return;
    router.delete(`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}`, { preserveScroll: true });
}

function toggleAktif(kelasId: number, item: KegiatanItem) {
    router.put(`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}/aktif`, { aktif: !(item.aktif ?? true) }, { preserveScroll: true });
}
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2">
        <div
            v-for="(item, idx) in items"
            :key="item.id"
            class="group relative flex flex-col justify-between rounded-2xl border border-amber-200 bg-gradient-to-b from-white to-amber-50/40 p-5 text-center shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-amber-400 hover:shadow-md dark:border-border dark:from-card dark:to-card"
        >
            <div v-if="!publicMode" class="mb-3 flex items-center justify-between">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold transition"
                    :class="
                        item.aktif !== false
                            ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'
                            : 'bg-stone-200 text-stone-700 hover:bg-stone-300'
                    "
                    :title="item.aktif !== false ? 'Klik untuk menonaktifkan LKPD' : 'Klik untuk mengaktifkan LKPD'"
                    @click="toggleAktif(kelasId, item)"
                >
                    <Eye v-if="item.aktif !== false" class="size-3.5" />
                    <EyeOff v-else class="size-3.5" />
                    {{ item.aktif !== false ? 'Aktif untuk Siswa' : 'Nonaktif' }}
                </button>
                <button
                    type="button"
                    aria-label="Hapus kegiatan"
                    title="Hapus kegiatan"
                    class="flex size-8 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50"
                    @click="hapus(kelasId, item)"
                >
                    <Trash2 class="size-4" />
                </button>
            </div>

            <div class="flex flex-col items-center">
                <span class="mx-auto flex size-24 items-center justify-center rounded-2xl border border-amber-200/80 bg-amber-100/70 text-amber-800 shadow-inner transition group-hover:scale-105">
                    <component :is="iconMap[item.icon] ?? Video" class="size-12" />
                </span>
                <span class="mt-3 text-[11px] font-extrabold tracking-widest text-amber-700 uppercase">
                    Kegiatan 0{{ item.nomor ?? idx + 1 }}
                </span>
                <h3 class="mt-1 text-lg font-bold text-amber-950 dark:text-amber-50">{{ item.judul }}</h3>
                <p v-if="item.bahan_ajar_label" class="mt-0.5 text-xs text-amber-900/65 dark:text-muted-foreground">
                    {{ item.bahan_ajar_label }}
                </p>
            </div>

            <div class="mt-5 flex w-full flex-col gap-2 border-t border-amber-100 pt-4 dark:border-border">
                <Link
                    v-if="publicMode && publicToken"
                    :href="`/kelas-publik/${publicToken}/kegiatan-pembelajaran/${item.id}`"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700"
                >
                    <Play class="size-4" />
                    Ayo Belajar
                </Link>
                <template v-if="!publicMode">
                    <Link
                        :href="`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}/builder`"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700"
                    >
                        <FileEdit class="size-4" />
                        Edit Isi LKPD (Builder)
                    </Link>
                    <div class="grid grid-cols-2 gap-2">
                        <Link
                            :href="`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}/jawaban`"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-bold text-amber-900 transition hover:bg-amber-50 dark:bg-secondary dark:text-amber-100"
                        >
                            <ClipboardList class="size-3.5" />
                            Jawaban Siswa
                        </Link>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-bold transition"
                            :class="
                                item.aktif !== false
                                    ? 'border-stone-300 bg-white text-stone-700 hover:bg-stone-50'
                                    : 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'
                            "
                            @click="toggleAktif(kelasId, item)"
                        >
                            {{ item.aktif !== false ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
