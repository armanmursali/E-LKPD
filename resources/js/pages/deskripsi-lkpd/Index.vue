<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Pencil, X } from '@lucide/vue';
import { ref } from 'vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Deskripsi LKPD',
            },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    deskripsiLkpd: {
        paragraf: string[];
    };
}>();

const editMode = ref(false);
const form = useForm({
    paragraf: props.deskripsiLkpd.paragraf.join('\n\n'),
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
    form.put(`/kelas/${props.kelas.id}/deskripsi-lkpd`, {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
        },
    });
}
</script>

<template>
    <Head title="Deskripsi LKPD" />

    <div class="relative min-h-screen overflow-hidden bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-4xl px-4 pb-16">
            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit deskripsi LKPD'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel label="Deskripsi LKPD" />

                <ParagraphContent v-if="!editMode" :paragraphs="deskripsiLkpd.paragraf" />

                <form v-else class="space-y-3" @submit.prevent="submitEdit">
                    <label class="text-xs font-bold text-amber-900">Deskripsi LKPD (pisahkan paragraf dengan baris kosong)</label>
                    <textarea v-model="form.paragraf" rows="12" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                    <p v-if="form.errors.paragraf" class="text-xs text-destructive">{{ form.errors.paragraf }}</p>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                        <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
