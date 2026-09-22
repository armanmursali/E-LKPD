<script setup lang="ts">
import { School } from '@lucide/vue';
import ModelPembelajaranGrid, { type ModelPembelajaranItem } from '@/components/lkpd/ModelPembelajaranGrid.vue';
import RibbonHeader from '@/components/lkpd/RibbonHeader.vue';
import { sanitizeRichText, type PengaturanLkpd } from '@/types/lkpd-block';

const emit = defineEmits<{
    (event: 'header-selected', section: string): void;
}>();

const props = defineProps<{
    identitasMapel: {
        mata_pelajaran?: string;
        materi?: string;
        satuan_pendidikan?: string;
        tahun_pelajaran?: string;
        tahapan_fase?: string;
        kelas_label?: string;
        semester?: string;
        alokasi_waktu?: string;
        capaian_pembelajaran?: string;
        alur_tujuan_pembelajaran?: string;
        tujuan_pembelajaran?: string[];
        indikator_ketercapaian?: string[];
        model_pembelajaran?: ModelPembelajaranItem[];
    } | null;
    headSections?: Array<
        | 'mata_pelajaran'
        | 'materi'
        | 'satuan_pendidikan'
        | 'tahun_pelajaran'
        | 'tahapan_fase'
        | 'kelas_label'
        | 'semester'
        | 'alokasi_waktu'
        | 'capaian_pembelajaran'
        | 'alur_tujuan_pembelajaran'
        | 'tujuan_pembelajaran'
        | 'indikator_ketercapaian'
        | 'model_pembelajaran'
    >;
    headSelectedItems?: Partial<Record<string, number[]>>;
    headStyle?: Partial<PengaturanLkpd>;
}>();

const sectionLabels: Record<string, string> = {
    mata_pelajaran: 'Mata Pelajaran',
    materi: 'Materi',
    satuan_pendidikan: 'Satuan Pendidikan',
    tahun_pelajaran: 'Tahun Pelajaran',
    tahapan_fase: 'Tahapan (Fase)',
    kelas_label: 'Kelas',
    semester: 'Semester',
    alokasi_waktu: 'Alokasi Waktu',
    capaian_pembelajaran: 'Capaian Pembelajaran',
    alur_tujuan_pembelajaran: 'Alur Tujuan Pembelajaran',
    tujuan_pembelajaran: 'Tujuan Pembelajaran',
    indikator_ketercapaian: 'Indikator Ketercapaian',
    model_pembelajaran: 'Model Pembelajaran',
};

function hasValue(section: string) {
    if (!props.identitasMapel) return false;

    const value = props.identitasMapel[section as keyof typeof props.identitasMapel];
    if (Array.isArray(value)) return value.length > 0;
    return typeof value === 'string' ? value.trim().length > 0 : false;
}

function visibleSections(): string[] {
    if (!props.headSections || props.headSections.length === 0) {
        return [];
    }

    return props.headSections.filter((section) => {
        const items = renderItems(section);

        if (items.length > 0) {
            return headDisplayItems(section).length > 0;
        }

        return hasValue(section);
    });
}

function headDisplayItems(section: string): Array<string | ModelPembelajaranItem> {
    if (!props.identitasMapel) return [];

    const items = renderItems(section);
    const selected = props.headSelectedItems?.[section] ?? items.map((_, index) => index);

    return items.filter((_, index) => selected.includes(index));
}

function renderItems(section: string): Array<string | ModelPembelajaranItem> {
    if (!props.identitasMapel) return [];

    const value = props.identitasMapel[section as keyof typeof props.identitasMapel];

    if (Array.isArray(value)) {
        return value
            .map((item) => {
                if (typeof item === 'string') {
                    return item.trim();
                }

                if (typeof item === 'object' && item && 'teks' in item) {
                    if (typeof item.teks !== 'string') {
                        return null;
                    }

                    return {
                        ...item,
                        teks: item.teks.trim(),
                    } as ModelPembelajaranItem;
                }

                return null;
            })
            .filter((item): item is string | ModelPembelajaranItem => item !== null && (typeof item === 'string' ? item.length > 0 : true));
    }

    if (typeof value !== 'string' || value.trim().length === 0) {
        return [];
    }

    const doc = new DOMParser().parseFromString(value, 'text/html');
    return Array.from(doc.querySelectorAll('li'))
        .map((item) => item.textContent?.trim() ?? '')
        .filter((item) => item.length > 0);
}

function getModelPembelajaranItems(section: string): ModelPembelajaranItem[] {
    return headDisplayItems(section)
        .filter((item): item is ModelPembelajaranItem => typeof item !== 'string')
        .map((item) => ({
            ...item,
            teks: item.teks.trim(),
        }));
}
</script>

<template>
    <div v-if="identitasMapel" class="space-y-6 rounded-md border border-amber-200 bg-amber-50/40 p-4">
        <div
            v-if="props.headStyle?.headTopText"
            class="w-full"
            :style="{
                textAlign: props.headStyle?.headTopTextAlign ?? 'left',
            }"
        >
            <div
                class="head-top-text inline-block rounded-md px-3 py-1 text-left"
                :style="{
                    backgroundColor: props.headStyle?.headTopTextBgColor ?? '#fff7ed',
                    border: `1px solid ${props.headStyle?.headTopTextBorderColor ?? '#f59e0b'}`,
                    color: props.headStyle?.headTopTextColor ?? '#9a4b13',
                    fontSize: `${props.headStyle?.headTopTextFontSize ?? 12}px`,
                    fontWeight: props.headStyle?.headTopTextFontWeight ?? '600',
                    whiteSpace: 'pre-line',
                    maxWidth: '100%',
                }"
                v-html="sanitizeRichText(props.headStyle?.headTopText ?? '')"
            />
        </div>

        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row">
            <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm md:grid-cols-[auto_auto]">
                <template v-for="section in visibleSections()" :key="section">
                    <template v-if="['mata_pelajaran', 'materi', 'satuan_pendidikan', 'tahun_pelajaran', 'tahapan_fase', 'kelas_label', 'semester', 'alokasi_waktu'].includes(section)">
                        <dt class="font-semibold text-amber-900">{{ sectionLabels[section] }}</dt>
                        <dd class="text-amber-900">: {{ identitasMapel[section as keyof typeof identitasMapel] }}</dd>
                    </template>
                </template>
            </dl>
            <School class="size-20 shrink-0 self-center text-amber-800 sm:size-24" />
        </div>

        <template v-for="section in visibleSections()" :key="section">
            <div v-if="section === 'capaian_pembelajaran'" class="space-y-2" @click="emit('header-selected', section)">
                <RibbonHeader
                    title="Capaian Pembelajaran"
                    :top-text="''"
                    :show-letter="Boolean(props.headStyle?.headShowLetter)"
                    :letter="props.headStyle?.headLetter ?? ''"
                    :title-color="props.headStyle?.headTitleColor ?? '#ffffff'"
                    :title-bg-color="props.headStyle?.headTitleBgColor ?? '#9a4b13'"
                    :title-border-color="props.headStyle?.headTitleBorderColor ?? '#78350f'"
                    :title-font-size="props.headStyle?.headTitleFontSize ?? '0.875rem'"
                    :title-font-weight="props.headStyle?.headTitleFontWeight ?? '700'"
                    :top-text-color="props.headStyle?.headTopTextColor ?? '#9a4b13'"
                    :top-text-bg-color="props.headStyle?.headTopTextBgColor ?? '#fff7ed'"
                    :top-text-border-color="props.headStyle?.headTopTextBorderColor ?? '#f59e0b'"
                    :top-text-font-size="typeof props.headStyle?.headTopTextFontSize === 'number' ? `${props.headStyle.headTopTextFontSize}px` : (props.headStyle?.headTopTextFontSize ?? '0.75rem')"
                    :top-text-font-weight="props.headStyle?.headTopTextFontWeight ?? '600'"
                    :letter-color="props.headStyle?.headLetterColor ?? '#78350f'"
                    :letter-bg-color="props.headStyle?.headLetterBgColor ?? '#ffffff'"
                    :letter-border-color="props.headStyle?.headLetterBorderColor ?? '#78350f'"
                    :letter-font-size="props.headStyle?.headLetterFontSize ?? '0.875rem'"
                    :letter-font-weight="props.headStyle?.headLetterFontWeight ?? '700'"
                />
                <ul v-if="headDisplayItems(section).length" class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                    <li v-for="(item, index) in headDisplayItems(section)" :key="index">{{ item }}</li>
                </ul>
                <div v-else class="pl-1 text-sm text-amber-900">Capaian pembelajaran belum diisi.</div>
            </div>

            <div v-else-if="section === 'alur_tujuan_pembelajaran'" class="space-y-2" @click="emit('header-selected', section)">
                <RibbonHeader
                    title="Alur Tujuan Pembelajaran"
                    :top-text="''"
                    :show-letter="Boolean(props.headStyle?.headShowLetter)"
                    :letter="props.headStyle?.headLetter ?? ''"
                    :title-color="props.headStyle?.headTitleColor ?? '#ffffff'"
                    :title-bg-color="props.headStyle?.headTitleBgColor ?? '#9a4b13'"
                    :title-border-color="props.headStyle?.headTitleBorderColor ?? '#78350f'"
                    :title-font-size="props.headStyle?.headTitleFontSize ?? '0.875rem'"
                    :title-font-weight="props.headStyle?.headTitleFontWeight ?? '700'"
                    :top-text-color="props.headStyle?.headTopTextColor ?? '#9a4b13'"
                    :top-text-bg-color="props.headStyle?.headTopTextBgColor ?? '#fff7ed'"
                    :top-text-border-color="props.headStyle?.headTopTextBorderColor ?? '#f59e0b'"
                    :top-text-font-size="typeof props.headStyle?.headTopTextFontSize === 'number' ? `${props.headStyle.headTopTextFontSize}px` : (props.headStyle?.headTopTextFontSize ?? '0.75rem')"
                    :top-text-font-weight="props.headStyle?.headTopTextFontWeight ?? '600'"
                    :letter-color="props.headStyle?.headLetterColor ?? '#78350f'"
                    :letter-bg-color="props.headStyle?.headLetterBgColor ?? '#ffffff'"
                    :letter-border-color="props.headStyle?.headLetterBorderColor ?? '#78350f'"
                    :letter-font-size="props.headStyle?.headLetterFontSize ?? '0.875rem'"
                    :letter-font-weight="props.headStyle?.headLetterFontWeight ?? '700'"
                />
                <ul v-if="headDisplayItems(section).length" class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                    <li v-for="(item, index) in headDisplayItems(section)" :key="index">{{ item }}</li>
                </ul>
                <div v-else class="pl-1 text-sm text-amber-900">Alur tujuan pembelajaran belum diisi.</div>
            </div>

            <div v-else-if="section === 'tujuan_pembelajaran'" class="space-y-2" @click="emit('header-selected', section)">
                <RibbonHeader
                    title="Tujuan Pembelajaran"
                    :top-text="''"
                    :show-letter="Boolean(props.headStyle?.headShowLetter)"
                    :letter="props.headStyle?.headLetter ?? ''"
                    :title-color="props.headStyle?.headTitleColor ?? '#ffffff'"
                    :title-bg-color="props.headStyle?.headTitleBgColor ?? '#9a4b13'"
                    :title-border-color="props.headStyle?.headTitleBorderColor ?? '#78350f'"
                    :title-font-size="props.headStyle?.headTitleFontSize ?? '0.875rem'"
                    :title-font-weight="props.headStyle?.headTitleFontWeight ?? '700'"
                    :top-text-color="props.headStyle?.headTopTextColor ?? '#9a4b13'"
                    :top-text-bg-color="props.headStyle?.headTopTextBgColor ?? '#fff7ed'"
                    :top-text-border-color="props.headStyle?.headTopTextBorderColor ?? '#f59e0b'"
                    :top-text-font-size="typeof props.headStyle?.headTopTextFontSize === 'number' ? `${props.headStyle.headTopTextFontSize}px` : (props.headStyle?.headTopTextFontSize ?? '0.75rem')"
                    :top-text-font-weight="props.headStyle?.headTopTextFontWeight ?? '600'"
                    :letter-color="props.headStyle?.headLetterColor ?? '#78350f'"
                    :letter-bg-color="props.headStyle?.headLetterBgColor ?? '#ffffff'"
                    :letter-border-color="props.headStyle?.headLetterBorderColor ?? '#78350f'"
                    :letter-font-size="props.headStyle?.headLetterFontSize ?? '0.875rem'"
                    :letter-font-weight="props.headStyle?.headLetterFontWeight ?? '700'"
                />
                <ul class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                    <li v-for="(item, index) in headDisplayItems(section)" :key="index">{{ item }}</li>
                </ul>
            </div>

            <div v-else-if="section === 'indikator_ketercapaian'" class="space-y-2" @click="emit('header-selected', section)">
                <RibbonHeader
                    title="Indikator Ketercapaian Tujuan Pembelajaran"
                    :top-text="''"
                    :show-letter="Boolean(props.headStyle?.headShowLetter)"
                    :letter="props.headStyle?.headLetter ?? ''"
                    :title-color="props.headStyle?.headTitleColor ?? '#ffffff'"
                    :title-bg-color="props.headStyle?.headTitleBgColor ?? '#9a4b13'"
                    :title-border-color="props.headStyle?.headTitleBorderColor ?? '#78350f'"
                    :title-font-size="props.headStyle?.headTitleFontSize ?? '0.875rem'"
                    :title-font-weight="props.headStyle?.headTitleFontWeight ?? '700'"
                    :top-text-color="props.headStyle?.headTopTextColor ?? '#9a4b13'"
                    :top-text-bg-color="props.headStyle?.headTopTextBgColor ?? '#fff7ed'"
                    :top-text-border-color="props.headStyle?.headTopTextBorderColor ?? '#f59e0b'"
                    :top-text-font-size="typeof props.headStyle?.headTopTextFontSize === 'number' ? `${props.headStyle.headTopTextFontSize}px` : (props.headStyle?.headTopTextFontSize ?? '0.75rem')"
                    :top-text-font-weight="props.headStyle?.headTopTextFontWeight ?? '600'"
                    :letter-color="props.headStyle?.headLetterColor ?? '#78350f'"
                    :letter-bg-color="props.headStyle?.headLetterBgColor ?? '#ffffff'"
                    :letter-border-color="props.headStyle?.headLetterBorderColor ?? '#78350f'"
                    :letter-font-size="props.headStyle?.headLetterFontSize ?? '0.875rem'"
                    :letter-font-weight="props.headStyle?.headLetterFontWeight ?? '700'"
                />
                <ul class="list-disc space-y-1 pl-6 text-sm text-amber-900">
                    <li v-for="(item, index) in headDisplayItems(section)" :key="index">{{ item }}</li>
                </ul>
            </div>

            <div v-else-if="section === 'model_pembelajaran'" class="space-y-3" @click="emit('header-selected', section)">
                <RibbonHeader
                    title="Model Pembelajaran Problem Based Learning"
                    :top-text="''"
                    :show-letter="Boolean(props.headStyle?.headShowLetter)"
                    :letter="props.headStyle?.headLetter ?? ''"
                    :title-color="props.headStyle?.headTitleColor ?? '#ffffff'"
                    :title-bg-color="props.headStyle?.headTitleBgColor ?? '#9a4b13'"
                    :title-border-color="props.headStyle?.headTitleBorderColor ?? '#78350f'"
                    :title-font-size="props.headStyle?.headTitleFontSize ?? '0.875rem'"
                    :title-font-weight="props.headStyle?.headTitleFontWeight ?? '700'"
                    :top-text-color="props.headStyle?.headTopTextColor ?? '#9a4b13'"
                    :top-text-bg-color="props.headStyle?.headTopTextBgColor ?? '#fff7ed'"
                    :top-text-border-color="props.headStyle?.headTopTextBorderColor ?? '#f59e0b'"
                    :top-text-font-size="typeof props.headStyle?.headTopTextFontSize === 'number' ? `${props.headStyle.headTopTextFontSize}px` : (props.headStyle?.headTopTextFontSize ?? '0.75rem')"
                    :top-text-font-weight="props.headStyle?.headTopTextFontWeight ?? '600'"
                    :letter-color="props.headStyle?.headLetterColor ?? '#78350f'"
                    :letter-bg-color="props.headStyle?.headLetterBgColor ?? '#ffffff'"
                    :letter-border-color="props.headStyle?.headLetterBorderColor ?? '#78350f'"
                    :letter-font-size="props.headStyle?.headLetterFontSize ?? '0.875rem'"
                    :letter-font-weight="props.headStyle?.headLetterFontWeight ?? '700'"
                />
                <ModelPembelajaranGrid :items="getModelPembelajaranItems(section)" />
            </div>
        </template>
    </div>
</template>

<style scoped>
.head-top-text :deep(b),
.head-top-text :deep(strong) {
    font-weight: 700;
}
</style>
