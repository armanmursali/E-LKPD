<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import KegiatanPembelajaranContent, { type KegiatanItem } from '@/components/lkpd/KegiatanPembelajaranContent.vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';

defineProps<{ kelas: { id: number; nama: string; public_token: string }; tujuanPembelajaran: string[]; kegiatanPembelajaran: { items: KegiatanItem[] } }>();
</script>

<template>
    <Head title="Kegiatan Pembelajaran" />
    <div class="min-h-screen bg-amber-50/60 px-4 py-6 md:px-8 md:py-10"><div class="mx-auto max-w-4xl"><header class="flex justify-between"><Link :href="`/kelas-publik/${kelas.public_token}`" class="font-extrabold tracking-wide text-amber-900">E-LKPD WEB</Link><Link :href="`/kelas-publik/${kelas.public_token}`" class="text-sm font-semibold text-amber-900 hover:underline">Kembali</Link></header><main class="mt-8 space-y-6"><section class="rounded-lg bg-white p-6 shadow-xl sm:p-10"><LkpdSectionLabel label="Tujuan Pembelajaran" /><ol class="list-decimal space-y-2 pl-6 text-amber-900"><li v-for="(tujuan, index) in tujuanPembelajaran" :key="index">{{ tujuan }}</li></ol></section><section class="rounded-lg bg-white p-6 shadow-xl sm:p-10"><LkpdSectionLabel label="Kegiatan Pembelajaran" /><KegiatanPembelajaranContent :kelas-id="kelas.id" :items="kegiatanPembelajaran.items" :public-mode="true" :public-token="kelas.public_token" /></section></main></div></div>
</template>
