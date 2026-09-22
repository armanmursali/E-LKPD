<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, X } from '@lucide/vue';
import { ref } from 'vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import DaftarPustakaContent, { type DaftarPustakaItem } from '@/components/lkpd/DaftarPustakaContent.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Daftar Pustaka' }],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    daftarPustaka: { items: DaftarPustakaItem[] };
}>();

function referenceError(index: number) {
    return form.errors[`items.${index}.referensi` as keyof typeof form.errors] as string | undefined;
}

const editMode = ref(false);
const form = useForm<{ items: DaftarPustakaItem[] }>({
    items: props.daftarPustaka.items.map((item) => ({ ...item })),
});

function startEdit() {
    form.items = props.daftarPustaka.items.length
        ? props.daftarPustaka.items.map((item) => ({ ...item }))
        : [{ nomor: 1, referensi: '' }];
    editMode.value = true;
}

function cancelEdit() {
    form.reset();
    form.clearErrors();
    editMode.value = false;
}

function addItem() {
    form.items.push({ nomor: form.items.length + 1, referensi: '' });
}

function removeItem(index: number) {
    if (form.items.length === 1) {
        form.items[0].referensi = '';
        return;
    }

    form.items.splice(index, 1);
    form.items.forEach((item, itemIndex) => { item.nomor = itemIndex + 1; });
}

function submitEdit() {
    form.put(`/kelas/${props.kelas.id}/daftar-pustaka`, {
        preserveScroll: true,
        onSuccess: () => { editMode.value = false; },
    });
}
</script>

<template>
    <Head title="Daftar Pustaka" />

    <div class="min-h-screen bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <Link href="/kelas" class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</Link>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-5xl px-4 pb-16">
            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit daftar pustaka'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel label="Daftar Pustaka" />

                <DaftarPustakaContent v-if="!editMode" :items="daftarPustaka.items" />

                <form v-else class="mt-8 space-y-4" @submit.prevent="submitEdit">
                    <div v-for="(item, index) in form.items" :key="index" class="flex items-start gap-3 rounded-md border border-amber-200 p-3">
                        <span class="mt-2 flex size-7 shrink-0 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">{{ index + 1 }}</span>
                        <div class="flex-1">
                            <textarea v-model="item.referensi" rows="3" required class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-amber-700" placeholder="Contoh: Campbell, N. A., Reece, J. B., ..."></textarea>
                            <p v-if="referenceError(index)" class="mt-1 text-xs text-red-600">{{ referenceError(index) }}</p>
                        </div>
                        <button type="button" title="Hapus referensi" class="mt-2 text-red-700 hover:text-red-900" @click="removeItem(index)"><Trash2 class="size-4" /></button>
                    </div>
                    <button type="button" class="inline-flex items-center gap-2 rounded-md border border-amber-800 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-50" @click="addItem"><Plus class="size-4" /> Tambah referensi</button>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                        <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
