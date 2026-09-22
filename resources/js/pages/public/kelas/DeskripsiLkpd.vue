<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';
defineProps<{ kelas: { nama: string; public_token: string }; paragraf: string[] }>();
</script>
<template><Head title="Deskripsi LKPD" /><div class="min-h-screen bg-amber-50/60 p-6 md:p-10"><div class="mx-auto max-w-4xl"><header class="flex justify-between"><Link :href="`/kelas-publik/${kelas.public_token}`" class="font-extrabold text-amber-900">E-LKPD WEB</Link><Link :href="`/kelas-publik/${kelas.public_token}`" class="text-sm font-semibold text-amber-900">Kembali</Link></header><main class="mt-8 rounded-lg bg-white p-6 shadow-xl sm:p-10"><LkpdSectionLabel label="Deskripsi LKPD" /><ParagraphContent :paragraphs="paragraf" /></main></div></div></template>
