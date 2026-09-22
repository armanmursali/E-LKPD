<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import EvaluasiPembelajaranList, { type EvaluasiItem } from '@/components/EvaluasiPembelajaranList.vue';
defineProps<{ kelas: { public_token: string }; evaluasi: EvaluasiItem[] }>();
</script>
<template><Head title="Evaluasi Pembelajaran" /><div class="min-h-screen bg-amber-50/60 p-6 md:p-10"><div class="mx-auto max-w-5xl"><header class="flex justify-between"><Link :href="`/kelas-publik/${kelas.public_token}`" class="font-extrabold text-amber-900">E-LKPD WEB</Link><Link :href="`/kelas-publik/${kelas.public_token}`" class="text-sm font-semibold text-amber-900">Kembali</Link></header><main class="mt-8 rounded-lg bg-white/0"><LkpdSectionLabel label="Evaluasi Pembelajaran" /><EvaluasiPembelajaranList :evaluasi="evaluasi" /></main></div></div></template>
