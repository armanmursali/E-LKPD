<script setup lang="ts">
import { School } from '@lucide/vue';
import ModelPembelajaranGrid, { type ModelPembelajaranItem } from '@/components/lkpd/ModelPembelajaranGrid.vue';
import RibbonHeader from '@/components/lkpd/RibbonHeader.vue';
import { sanitizeRichText } from '@/types/lkpd-block';

defineProps<{
    identitasMapel: {
        mata_pelajaran: string;
        materi: string;
        satuan_pendidikan: string;
        tahun_pelajaran: string;
        tahapan_fase: string;
        kelas_label: string;
        semester: string;
        alokasi_waktu: string;
        capaian_pembelajaran: string;
        alur_tujuan_pembelajaran: string;
        tujuan_pembelajaran: string[];
        indikator_ketercapaian: string[];
        model_pembelajaran: ModelPembelajaranItem[];
    };
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row">
            <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm">
                <dt class="font-semibold text-amber-900">Mata Pelajaran</dt>
                <dd class="text-amber-900">: {{ identitasMapel.mata_pelajaran }}</dd>
                <dt class="font-semibold text-amber-900">Materi</dt>
                <dd class="text-amber-900">: {{ identitasMapel.materi }}</dd>
                <dt class="font-semibold text-amber-900">Satuan Pendidikan</dt>
                <dd class="text-amber-900">: {{ identitasMapel.satuan_pendidikan }}</dd>
                <dt class="font-semibold text-amber-900">Tahun Pelajaran</dt>
                <dd class="text-amber-900">: {{ identitasMapel.tahun_pelajaran }}</dd>
                <dt class="font-semibold text-amber-900">Tahapan (Fase)</dt>
                <dd class="text-amber-900">: {{ identitasMapel.tahapan_fase }}</dd>
                <dt class="font-semibold text-amber-900">Kelas</dt>
                <dd class="text-amber-900">: {{ identitasMapel.kelas_label }}</dd>
                <dt class="font-semibold text-amber-900">Semester</dt>
                <dd class="text-amber-900">: {{ identitasMapel.semester }}</dd>
                <dt class="font-semibold text-amber-900">Alokasi Waktu</dt>
                <dd class="text-amber-900">: {{ identitasMapel.alokasi_waktu }}</dd>
            </dl>
            <School class="size-20 shrink-0 self-center text-amber-800 sm:size-24" />
        </div>

        <div class="space-y-2">
            <RibbonHeader letter="A" title="Capaian Pembelajaran" />
            <div
                class="pl-1 text-sm text-amber-900 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1 [&_li]:my-0.5"
                v-html="sanitizeRichText(identitasMapel.capaian_pembelajaran) || 'Capaian pembelajaran belum diisi.'"
            />
        </div>

        <div class="space-y-2">
            <RibbonHeader letter="B" title="Alur Tujuan Pembelajaran" />
            <div
                class="pl-1 text-sm text-amber-900 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1 [&_li]:my-0.5"
                v-html="sanitizeRichText(identitasMapel.alur_tujuan_pembelajaran) || 'Alur tujuan pembelajaran belum diisi.'"
            />
        </div>

        <div class="space-y-2">
            <RibbonHeader letter="C" title="Tujuan Pembelajaran" />
            <ul class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                <li v-for="(item, index) in identitasMapel.tujuan_pembelajaran" :key="index">{{ item }}</li>
            </ul>
        </div>

        <div class="space-y-2">
            <RibbonHeader letter="D" title="Indikator Ketercapaian Tujuan Pembelajaran" />
            <ul class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                <li v-for="(item, index) in identitasMapel.indikator_ketercapaian" :key="index">{{ item }}</li>
            </ul>
        </div>

        <div class="space-y-3">
            <RibbonHeader letter="E" title="Model Pembelajaran Problem Based Learning" />
            <ModelPembelajaranGrid :items="identitasMapel.model_pembelajaran" />
        </div>
    </div>
</template>
