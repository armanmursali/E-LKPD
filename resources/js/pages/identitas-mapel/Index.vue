<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, X } from '@lucide/vue';
import { ref } from 'vue';
import IdentitasMapelContent from '@/components/lkpd/IdentitasMapelContent.vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import RichTextEditor from '@/components/lkpd/RichTextEditor.vue';
import type { ModelPembelajaranItem } from '@/components/lkpd/ModelPembelajaranGrid.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Identitas Mata Pelajaran',
            },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
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

const iconOptions = [
    { value: 'search', label: 'Mencari/Mengarahkan' },
    { value: 'clipboard-list', label: 'Persiapan' },
    { value: 'users', label: 'Kelompok' },
    { value: 'presentation', label: 'Presentasi' },
    { value: 'search-check', label: 'Evaluasi' },
] as const;

const editMode = ref(false);

function toHtmlList(items: string[]) {
    if (!items.length) return '';

    return `<ul>${items.map((item) => `<li>${item}</li>`).join('')}</ul>`;
}

const form = useForm({
    mata_pelajaran: props.identitasMapel.mata_pelajaran,
    materi: props.identitasMapel.materi,
    satuan_pendidikan: props.identitasMapel.satuan_pendidikan,
    tahun_pelajaran: props.identitasMapel.tahun_pelajaran,
    tahapan_fase: props.identitasMapel.tahapan_fase,
    kelas_label: props.identitasMapel.kelas_label,
    semester: props.identitasMapel.semester,
    alokasi_waktu: props.identitasMapel.alokasi_waktu,
    capaian_pembelajaran: props.identitasMapel.capaian_pembelajaran,
    alur_tujuan_pembelajaran: props.identitasMapel.alur_tujuan_pembelajaran,
    tujuan_pembelajaran: toHtmlList(props.identitasMapel.tujuan_pembelajaran),
    indikator_ketercapaian: toHtmlList(props.identitasMapel.indikator_ketercapaian),
    model_pembelajaran: props.identitasMapel.model_pembelajaran.map((item) => ({ ...item })),
});

function startEdit() {
    form.reset();
    editMode.value = true;
}

function cancelEdit() {
    form.reset();
    form.clearErrors();
    editMode.value = false;
}

function submitEdit() {
    form.put(`/kelas/${props.kelas.id}/identitas-mapel`, {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
        },
    });
}
</script>

<template>
    <Head title="Identitas Mata Pelajaran" />

    <div class="relative min-h-screen overflow-hidden bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-4xl px-4 pb-16">
            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit identitas mata pelajaran'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel label="Identitas Mata Pelajaran" />

                <IdentitasMapelContent v-if="!editMode" :identitas-mapel="identitasMapel" />

                <form v-else class="space-y-5" @submit.prevent="submitEdit">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-amber-900">Mata Pelajaran</label>
                            <input v-model="form.mata_pelajaran" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Materi</label>
                            <input v-model="form.materi" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Satuan Pendidikan</label>
                            <input v-model="form.satuan_pendidikan" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Tahun Pelajaran</label>
                            <input v-model="form.tahun_pelajaran" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Tahapan (Fase)</label>
                            <input v-model="form.tahapan_fase" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Kelas</label>
                            <input v-model="form.kelas_label" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Semester</label>
                            <input v-model="form.semester" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-amber-900">Alokasi Waktu</label>
                            <input v-model="form.alokasi_waktu" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-amber-900">Capaian Pembelajaran</label>
                        <RichTextEditor v-model="form.capaian_pembelajaran" placeholder="Tulis capaian pembelajaran..." class="mt-1" />
                    </div>

                    <div>
                        <label class="text-xs font-bold text-amber-900">Alur Tujuan Pembelajaran</label>
                        <RichTextEditor v-model="form.alur_tujuan_pembelajaran" placeholder="Tulis alur tujuan pembelajaran..." class="mt-1" />
                    </div>

                    <div>
                        <label class="text-xs font-bold text-amber-900">Tujuan Pembelajaran</label>
                        <RichTextEditor v-model="form.tujuan_pembelajaran" placeholder="Gunakan tombol daftar untuk membuat list tujuan pertemuan..." class="mt-1" />
                    </div>

                    <div>
                        <label class="text-xs font-bold text-amber-900">Indikator Ketercapaian</label>
                        <RichTextEditor v-model="form.indikator_ketercapaian" placeholder="Gunakan tombol daftar untuk membuat list indikator..." class="mt-1" />
                    </div>

                    <div>
                        <label class="text-xs font-bold text-amber-900">Model Pembelajaran Problem Based Learning</label>
                        <div class="mt-2 space-y-3">
                            <div v-for="(item, index) in form.model_pembelajaran" :key="index" class="rounded-md border border-amber-200 p-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">{{ index + 1 }}</span>
                                    <select v-model="item.icon" class="rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                        <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                                <textarea v-model="item.teks" rows="2" class="mt-2 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                        <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
