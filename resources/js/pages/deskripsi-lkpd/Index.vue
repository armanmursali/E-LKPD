<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Pencil, X } from '@lucide/vue';
import { ref } from 'vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Daftar Kelas', href: '/kelas' },
            { title: 'Deskripsi LKPD' },
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

    <div class="relative min-h-screen overflow-hidden bg-amber-50/60 px-4 py-6 sm:px-6 md:px-8 dark:bg-background">
        <div class="mx-auto max-w-4xl space-y-5 pb-16">
            <div>
                <Link
                    :href="`/kelas/${kelas.id}`"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke {{ kelas.nama }}
                </Link>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-amber-200 bg-white p-6 shadow-md sm:p-10 dark:border-border dark:bg-card">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit deskripsi LKPD'"
                    class="absolute top-4 right-4 inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50/70 px-3 py-1.5 text-xs font-semibold text-amber-900 shadow-2xs transition hover:bg-amber-100 dark:border-amber-700 dark:bg-secondary dark:text-amber-100"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-3.5" />
                    <Pencil v-else class="size-3.5" />
                    <span>{{ editMode ? 'Batal' : 'Edit' }}</span>
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
