<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, X } from '@lucide/vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import PetunjukLkpdContent, { type PetunjukItem } from '@/components/lkpd/PetunjukLkpdContent.vue';
import { ref } from 'vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Petunjuk Penggunaan LKPD',
            },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    petunjukLkpd: {
        items: PetunjukItem[];
    };
}>();

const iconOptions = [
    { value: 'id-card', label: 'Identitas' },
    { value: 'book-open', label: 'Buku' },
    { value: 'target', label: 'Target/Tujuan' },
    { value: 'list-checks', label: 'Checklist' },
    { value: 'pencil-line', label: 'Menulis' },
    { value: 'message-question', label: 'Tanya/Diskusi' },
] as const;

const editMode = ref(false);
const form = useForm<{ items: PetunjukItem[] }>({
    items: props.petunjukLkpd.items.map((item) => ({ ...item })),
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
    form.put(`/kelas/${props.kelas.id}/petunjuk-lkpd`, {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
        },
    });
}
</script>

<template>
    <Head title="Petunjuk Penggunaan LKPD" />

    <div class="relative min-h-screen overflow-hidden bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-4xl px-4 pb-16">
            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit petunjuk LKPD'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel label="Petunjuk Penggunaan LKPD" />

                <PetunjukLkpdContent v-if="!editMode" :items="petunjukLkpd.items" />

                <form v-else class="space-y-4" @submit.prevent="submitEdit">
                    <div v-for="(item, index) in form.items" :key="index" class="rounded-md border border-amber-200 p-3">
                        <div class="flex items-center gap-3">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-amber-800 text-xs font-bold text-white">{{ index + 1 }}</span>
                            <select v-model="item.icon" class="rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                <option v-for="opt in iconOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                        <textarea v-model="item.teks" rows="2" class="mt-2 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
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
