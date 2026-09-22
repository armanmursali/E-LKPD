<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Pencil, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ParagraphContent from '@/components/lkpd/ParagraphContent.vue';
import PerkenalanContent from '@/components/lkpd/PerkenalanContent.vue';

type Sampul = {
    judul: string;
    mapel: string;
    jenjang: string;
    kelas_label: string;
    fase: string;
    kurikulum: string;
    penulis: string;
    pembimbing: string | string[] | null;
    validator_media: string;
    validator_materi: string;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pendahuluan',
              
            },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    pendahuluan: {
        sampul: Sampul;
        kata_pengantar: string[];
    };
}>();

const pages = [
    { key: 'perkenalan', label: 'Perkenalan' },
    { key: 'pengantar', label: 'Kata Pengantar' },
] as const;

const currentPage = ref(0);
const currentPageKey = computed(() => pages[currentPage.value].key);

const sampul = computed(() => ({
    ...props.pendahuluan.sampul,
    pembimbing: Array.isArray(props.pendahuluan.sampul.pembimbing)
        ? props.pendahuluan.sampul.pembimbing.filter(Boolean)
        : props.pendahuluan.sampul.pembimbing?.trim()
            ? [props.pendahuluan.sampul.pembimbing.trim()]
            : [],
}));

function nextPage() {
    currentPage.value = (currentPage.value + 1) % pages.length;
}

function prevPage() {
    currentPage.value = (currentPage.value - 1 + pages.length) % pages.length;
}

const editMode = ref(false);
const form = useForm({
    judul: sampul.value.judul,
    mapel: sampul.value.mapel,
    jenjang: sampul.value.jenjang,
    kelas_label: sampul.value.kelas_label,
    fase: sampul.value.fase,
    kurikulum: sampul.value.kurikulum,
    penulis: sampul.value.penulis,
    pembimbing: [...sampul.value.pembimbing],
    validator_media: sampul.value.validator_media,
    validator_materi: sampul.value.validator_materi,
    kata_pengantar: props.pendahuluan.kata_pengantar.join('\n\n'),
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
    form.put(`/kelas/${props.kelas.id}/pendahuluan`, {
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false;
        },
    });
}

function addPembimbing() {
    form.pembimbing.push('');
}

function removePembimbing(index: number) {
    if (form.pembimbing.length === 1) {
        form.pembimbing[0] = '';
        return;
    }

    form.pembimbing.splice(index, 1);
}
</script>

<template>
    <Head title="Pendahuluan" />

    <div class="relative min-h-screen overflow-hidden bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto flex max-w-4xl items-center gap-3 px-4 pb-16 sm:gap-6">
            <button
                type="button"
                aria-label="Halaman sebelumnya"
                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-amber-100/70 text-amber-900 shadow transition hover:bg-amber-100"
                @click="prevPage"
            >
                <ChevronLeft class="size-5" />
            </button>

            <div class="relative flex-1 overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit pendahuluan'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel :label="pages[currentPage].label" />

                <Transition name="slide" mode="out-in">
                    <div v-if="currentPageKey === 'perkenalan'" key="perkenalan" class="text-center">
                        <PerkenalanContent v-if="!editMode" :sampul="sampul" />

                        <form v-else class="mx-auto max-w-lg space-y-3 text-left" @submit.prevent="submitEdit">
                            <div>
                                <label class="text-xs font-bold text-amber-900">Judul</label>
                                <textarea v-model="form.judul" rows="2" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                <p v-if="form.errors.judul" class="mt-1 text-xs text-destructive">{{ form.errors.judul }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-bold text-amber-900">Mapel</label>
                                    <input v-model="form.mapel" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-amber-900">Jenjang</label>
                                    <input v-model="form.jenjang" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-amber-900">Label Kelas</label>
                                    <input v-model="form.kelas_label" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-amber-900">Fase</label>
                                    <input v-model="form.fase" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Kurikulum</label>
                                <input v-model="form.kurikulum" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Penulis</label>
                                <input v-model="form.penulis" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <div class="mb-1 flex items-center justify-between">
                                    <label class="text-xs font-bold text-amber-900">Pembimbing</label>
                                    <button type="button" class="text-xs font-semibold text-amber-800 hover:underline" @click="addPembimbing">+ Tambah pembimbing</button>
                                </div>
                                <div v-for="(pembimbing, index) in form.pembimbing" :key="index" class="mb-2 flex gap-2">
                                    <input v-model="form.pembimbing[index]" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" :placeholder="`Nama pembimbing ${index + 1}`" />
                                    <button v-if="form.pembimbing.length > 1" type="button" class="mt-1 rounded-md border px-3 text-sm text-red-700 hover:bg-red-50" @click="removePembimbing(index)">Hapus</button>
                                </div>
                                <p v-if="form.errors.pembimbing" class="mt-1 text-xs text-destructive">{{ form.errors.pembimbing }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Validator Ahli Media</label>
                                <input v-model="form.validator_media" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-amber-900">Validator Ahli Materi</label>
                                <input v-model="form.validator_materi" type="text" class="mt-1 w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                                <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">Simpan</button>
                            </div>
                        </form>
                    </div>

                    <div v-else key="pengantar">
                        <ParagraphContent v-if="!editMode" :paragraphs="pendahuluan.kata_pengantar" />
                        <form v-else class="space-y-3" @submit.prevent="submitEdit">
                            <label class="text-xs font-bold text-amber-900">Kata Pengantar (pisahkan paragraf dengan baris kosong)</label>
                            <textarea v-model="form.kata_pengantar" rows="10" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.kata_pengantar" class="text-xs text-destructive">{{ form.errors.kata_pengantar }}</p>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                                <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">Simpan</button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>

            <button
                type="button"
                aria-label="Halaman berikutnya"
                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-amber-100/70 text-amber-900 shadow transition hover:bg-amber-100"
                @click="nextPage"
            >
                <ChevronRight class="size-5" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.25s ease;
}
.slide-enter-from {
    opacity: 0;
    transform: translateX(24px);
}
.slide-leave-to {
    opacity: 0;
    transform: translateX(-24px);
}
</style>
