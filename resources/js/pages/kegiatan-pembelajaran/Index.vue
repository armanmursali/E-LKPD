<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import KegiatanPembelajaranContent, { type KegiatanItem } from '@/components/lkpd/KegiatanPembelajaranContent.vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kegiatan Pembelajaran',
            },
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

    <div class="relative min-h-screen overflow-hidden bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-4xl space-y-6 px-4 pb-16">
            <div class="rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <h2 class="mb-4 text-center text-xl font-bold text-amber-900">Tujuan Pembelajaran</h2>
                <ol class="list-decimal space-y-1 pl-6 text-sm text-amber-900">
                    <li v-for="(item, index) in tujuanPembelajaran" :key="index">{{ item }}</li>
                </ol>
            </div>

            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <div class="absolute top-4 right-4 flex items-center gap-2">
                    <button
                        type="button"
                        title="Tambah kegiatan pembelajaran"
                        class="inline-flex items-center gap-1.5 rounded-md bg-amber-800 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-amber-900"
                        @click="tambahKegiatan"
                    >
                        <Plus class="size-3.5" />
                        Buat LKPD
                    </button>
                    <button
                        type="button"
                        :aria-label="editMode ? 'Batal edit' : 'Edit kegiatan pembelajaran'"
                        class="flex size-8 shrink-0 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                        @click="editMode ? cancelEdit() : startEdit()"
                    >
                        <X v-if="editMode" class="size-4" />
                        <Pencil v-else class="size-4" />
                    </button>
                </div>

                <LkpdSectionLabel label="Kegiatan Pembelajaran" />

                <p v-if="!editMode && kegiatanPembelajaran.items.length === 0" class="text-center text-sm text-muted-foreground">
                    Belum ada kegiatan pembelajaran. Klik "Buat LKPD" di atas untuk menambahkan.
                </p>
                <KegiatanPembelajaranContent v-else-if="!editMode" :kelas-id="kelas.id" :items="kegiatanPembelajaran.items" />

                <form v-else class="space-y-4" @submit.prevent="submitEdit">
                    <p v-if="form.items.length === 0" class="text-center text-sm text-muted-foreground">Belum ada kegiatan pembelajaran.</p>
                    <div v-for="(item, index) in form.items" :key="item.id" class="rounded-md border border-amber-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">{{ index + 1 }}</span>
                                <select v-model="item.icon" class="rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                    <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                            </div>
                            <button type="button" class="rounded p-1 hover:bg-red-50" @click="removeItem(index)">
                                <Trash2 class="size-4 text-red-600" />
                            </button>
                        </div>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-bold text-amber-900">Judul</label>
                                <input v-model="item.judul" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Label Bahan Ajar Tambahan</label>
                                <input v-model="item.bahan_ajar_label" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
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
