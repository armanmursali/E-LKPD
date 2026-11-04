<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import EvaluasiPembelajaranList, { type EvaluasiItem } from '@/components/EvaluasiPembelajaranList.vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';

defineProps<{ kelas: { public_token: string; nama?: string }; evaluasi: EvaluasiItem[] }>();
</script>

<template>
    <Head title="Evaluasi Pembelajaran" />
    <div class="evaluation-public-page">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 md:py-10">
            <header class="flex items-center justify-between gap-3">
                <Link :href="`/kelas-publik/${kelas.public_token}`" class="inline-flex items-center gap-2.5 font-black tracking-wider text-white">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-[#5e402a] text-sm text-white">E</span>
                    <span>E-LKPD WEB</span>
                </Link>
                <Link
                    :href="`/kelas-publik/${kelas.public_token}`"
                    class="inline-flex items-center gap-2 rounded-full bg-white/95 px-3.5 py-1.5 text-xs font-bold text-[#5e402a] shadow-xs transition hover:-translate-y-0.5 hover:bg-white"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke Kelas
                </Link>
            </header>

            <main class="mt-8">
                <div class="mb-6 text-center text-white">
                    <LkpdSectionLabel label="Evaluasi Pembelajaran" />
                    <p class="mx-auto -mt-4 max-w-xl text-sm font-medium text-white/90">
                        Pilih angket Pre-test atau Post-test di bawah ini sesuai arahan guru.
                    </p>
                </div>
                <EvaluasiPembelajaranList :evaluasi="evaluasi" />
            </main>
        </div>
    </div>
</template>

<style scoped>
.evaluation-public-page {
    min-height: 100vh;
    background-color: #ddb27b;
    background-image: linear-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.14) 1px, transparent 1px);
    background-size: 30px 30px;
}
</style>
