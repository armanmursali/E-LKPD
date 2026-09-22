<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Microscope, Trash2, Video } from '@lucide/vue';
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
    <div class="grid gap-4 sm:grid-cols-2">
        <div v-for="item in items" :key="item.id" class="relative flex flex-col items-center gap-4 rounded-lg border border-amber-800 bg-white p-5 text-center">
            <button
                v-if="!publicMode"
                type="button"
                aria-label="Hapus kegiatan"
                class="absolute top-2 right-2 flex size-7 items-center justify-center rounded-full text-red-600 hover:bg-red-50"
                @click="hapus(kelasId, item)"
            >
                <Trash2 class="size-4" />
            </button>
            <span class="text-base font-bold text-amber-900">{{ item.judul }}</span>
            <span v-if="!publicMode" class="rounded-full px-2 py-0.5 text-[11px] font-semibold" :class="item.aktif !== false ? 'bg-emerald-100 text-emerald-700' : 'bg-neutral-100 text-neutral-600'">
                {{ item.aktif !== false ? 'Aktif' : 'Nonaktif' }}
            </span>
            <span class="flex size-28 items-center justify-center rounded-md bg-amber-50">
                <component :is="iconMap[item.icon] ?? Video" class="size-16 text-amber-800" />
            </span>
            <div class="flex w-full flex-col gap-2">
                <button
                    v-if="!publicMode"
                    type="button"
                    class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-amber-50"
                    :class="item.aktif !== false ? 'border-red-300 text-red-700' : 'border-emerald-300 text-emerald-700'"
                    @click="toggleAktif(kelasId, item)"
                >
                    {{ item.aktif !== false ? 'Nonaktifkan LKPD' : 'Aktifkan LKPD' }}
                </button>
                <Link v-if="publicMode && publicToken" :href="`/kelas-publik/${publicToken}/kegiatan-pembelajaran/${item.id}`" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white hover:bg-amber-900">Ayo Belajar</Link>
                <Link
                    v-if="!publicMode"
                    :href="`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}/builder`"
                    class="rounded-md border border-amber-800 px-4 py-2 text-sm font-medium text-amber-900 hover:bg-amber-50"
                >
                    Edit Isi LKPD
                </Link>
                <Link
                    v-if="!publicMode"
                    :href="`/kelas/${kelasId}/kegiatan-pembelajaran/${item.id}/jawaban`"
                    class="rounded-md border border-amber-800 px-4 py-2 text-sm font-medium text-amber-900 hover:bg-amber-50"
                >
                    Lihat Jawaban Siswa
                </Link>
            </div>
        </div>
    </div>
</template>
