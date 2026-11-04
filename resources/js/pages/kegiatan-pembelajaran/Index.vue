<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, Plus, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import KegiatanPembelajaranContent, { type KegiatanItem } from '@/components/lkpd/KegiatanPembelajaranContent.vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Daftar Kelas', href: '/kelas' },
            { title: 'Kegiatan Pembelajaran' },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    tujuanPembelajaran: string[];
    kegiatanPembelajaran: {
        items: KegiatanItem[];
    };
}>();

const iconOptions = [
    { value: 'video', label: 'Video' },
    { value: 'microscope', label: 'Praktikum' },
] as const;

const editMode = ref(false);
const form = useForm<{ items: KegiatanItem[] }>({
    items: props.kegiatanPembelajaran.items.map((item) => ({ ...item })),
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
    form.put(`/kelas/${props.kelas.id}/kegiatan-pembelajaran`, {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
        },
    });
}

function removeItem(index: number) {
    form.items.splice(index, 1);
}

function tambahKegiatan() {
    router.post(`/kelas/${props.kelas.id}/kegiatan-pembelajaran`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Kegiatan Pembelajaran" />

    <div class="relative min-h-screen overflow-hidden bg-amber-50/60 px-4 py-6 sm:px-6 md:px-8 dark:bg-background">
        <div class="mx-auto max-w-4xl space-y-6 pb-16">
            <div>
                <Link
                    :href="`/kelas/${kelas.id}`"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke {{ kelas.nama }}
                </Link>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-white p-6 shadow-md sm:p-8 dark:border-border dark:bg-card">
                <h2 class="mb-4 text-center text-xl font-bold text-amber-900 dark:text-amber-200">Tujuan Pembelajaran</h2>
                <ol v-if="tujuanPembelajaran.length" class="list-decimal space-y-2 pl-6 text-sm leading-relaxed text-amber-950 dark:text-amber-100">
                    <li v-for="(item, index) in tujuanPembelajaran" :key="index">{{ item }}</li>
                </ol>
                <p v-else class="text-center text-sm text-amber-900/60 dark:text-muted-foreground">
                    Tujuan pembelajaran dapat diatur pada menu Identitas Mata Pelajaran.
                </p>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-amber-200 bg-white p-6 shadow-md sm:p-10 dark:border-border dark:bg-card">
                <div class="mb-6 flex flex-wrap items-center justify-end gap-2 sm:absolute sm:top-4 sm:right-4 sm:mb-0">
                    <button
                        type="button"
                        title="Tambah kegiatan pembelajaran"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-800 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700"
                        @click="tambahKegiatan"
                    >
                        <Plus class="size-3.5" />
                        Buat LKPD
                    </button>
                    <button
                        type="button"
                        :aria-label="editMode ? 'Batal edit' : 'Edit kegiatan pembelajaran'"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50/70 px-3 py-2 text-xs font-semibold text-amber-900 shadow-2xs transition hover:bg-amber-100 dark:border-amber-700 dark:bg-secondary dark:text-amber-100"
                        @click="editMode ? cancelEdit() : startEdit()"
                    >
                        <X v-if="editMode" class="size-3.5" />
                        <Pencil v-else class="size-3.5" />
                        <span>{{ editMode ? 'Batal' : 'Edit Judul' }}</span>
                    </button>
                </div>

                <LkpdSectionLabel label="Kegiatan Pembelajaran" />

                <p v-if="!editMode && kegiatanPembelajaran.items.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                    Belum ada kegiatan pembelajaran. Klik <strong>"Buat LKPD"</strong> di atas untuk menambahkan.
                </p>
                <KegiatanPembelajaranContent v-else-if="!editMode" :kelas-id="kelas.id" :items="kegiatanPembelajaran.items" />

                <form v-else class="space-y-4" @submit.prevent="submitEdit">
                    <p v-if="form.items.length === 0" class="text-center text-sm text-muted-foreground">Belum ada kegiatan pembelajaran.</p>
                    <div v-for="(item, index) in form.items" :key="item.id" class="rounded-xl border border-amber-200 bg-amber-50/30 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">{{ index + 1 }}</span>
                                <select v-model="item.icon" class="rounded-md border border-amber-300 bg-white px-2.5 py-1.5 text-sm">
                                    <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                            </div>
                            <button type="button" class="rounded p-1.5 hover:bg-red-50" @click="removeItem(index)">
                                <Trash2 class="size-4 text-red-600" />
                            </button>
                        </div>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-bold text-amber-900">Judul Kegiatan</label>
                                <input v-model="item.judul" type="text" class="mt-1 w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Label Bahan Ajar Tambahan</label>
                                <input v-model="item.bahan_ajar_label" type="text" class="mt-1 w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-sm" />
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
