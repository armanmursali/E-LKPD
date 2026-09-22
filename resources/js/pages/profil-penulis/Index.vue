<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ImagePlus, Pencil, X } from '@lucide/vue';
import { ref } from 'vue';
import LkpdSectionLabel from '@/components/lkpd/LkpdSectionLabel.vue';
import ProfilPenulisContent from '@/components/lkpd/ProfilPenulisContent.vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Profil Penulis' }],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    profilPenulis: { paragraf: string; gambar: string | null };
}>();

const editMode = ref(false);
const imageInput = ref<HTMLInputElement | null>(null);
const preview = ref<string | null>(props.profilPenulis.gambar);
const hapusGambar = ref(false);
const form = useForm({
    paragraf: props.profilPenulis.paragraf,
    gambar: null as File | null,
    hapus_gambar: false,
});

function startEdit() {
    form.paragraf = props.profilPenulis.paragraf;
    form.gambar = null;
    form.hapus_gambar = false;
    hapusGambar.value = false;
    preview.value = props.profilPenulis.gambar;
    editMode.value = true;
}

function cancelEdit() {
    form.reset();
    form.clearErrors();
    preview.value = props.profilPenulis.gambar;
    hapusGambar.value = false;
    editMode.value = false;
}

function chooseImage(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.gambar = file;
    hapusGambar.value = false;
    preview.value = URL.createObjectURL(file);
}

function removeImage() {
    form.gambar = null;
    hapusGambar.value = true;
    form.hapus_gambar = true;
    preview.value = null;
    if (imageInput.value) imageInput.value.value = '';
}

function submitEdit() {
    form.hapus_gambar = hapusGambar.value;
    form.post(`/kelas/${props.kelas.id}/profil-penulis`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => { editMode.value = false; },
    });
}
</script>

<template>
    <Head title="Profil Penulis" />

    <div class="min-h-screen bg-background">
        <div class="flex items-center justify-between px-6 py-5 sm:px-10">
            <Link href="/kelas" class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</Link>
            <Link :href="`/kelas/${kelas.id}`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto max-w-5xl px-4 pb-16">
            <div class="relative overflow-hidden rounded-lg bg-white p-6 shadow-xl sm:p-10">
                <button
                    type="button"
                    :aria-label="editMode ? 'Batal edit' : 'Edit profil penulis'"
                    class="absolute top-4 right-4 flex size-8 items-center justify-center rounded-full border text-amber-900 shadow-sm transition hover:bg-amber-50"
                    @click="editMode ? cancelEdit() : startEdit()"
                >
                    <X v-if="editMode" class="size-4" />
                    <Pencil v-else class="size-4" />
                </button>

                <LkpdSectionLabel label="Profil Penulis" />

                <ProfilPenulisContent v-if="!editMode" :paragraf="profilPenulis.paragraf" :gambar="profilPenulis.gambar" />

                <form v-else class="mt-8 space-y-5" @submit.prevent="submitEdit">
                    <div class="grid gap-5 md:grid-cols-[220px_1fr]">
                        <div class="space-y-3">
                            <div class="flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-amber-200 bg-amber-50">
                                <img v-if="preview" :src="preview" alt="Pratinjau foto penulis" class="h-full w-full object-cover" />
                                <ImagePlus v-else class="size-10 text-amber-700" />
                            </div>
                            <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-muted-foreground file:mr-2 file:rounded-md file:border-0 file:bg-amber-800 file:px-3 file:py-2 file:text-xs file:font-medium file:text-white" @change="chooseImage" />
                            <button v-if="preview" type="button" class="w-full text-sm text-red-700 hover:underline" @click="removeImage">Hapus gambar</button>
                            <p v-if="form.errors.gambar" class="text-xs text-red-600">{{ form.errors.gambar }}</p>
                        </div>
                        <div class="space-y-2">
                            <label for="paragraf" class="text-sm font-medium text-amber-900">Biografi penulis</label>
                            <textarea id="paragraf" v-model="form.paragraf" rows="12" required class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm leading-relaxed outline-none focus:ring-2 focus:ring-amber-700" placeholder="Tulis biografi penulis di sini. Pisahkan paragraf dengan satu baris kosong."></textarea>
                            <p v-if="form.errors.paragraf" class="text-xs text-red-600">{{ form.errors.paragraf }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="rounded-md border px-4 py-2 text-sm" @click="cancelEdit">Batal</button>
                        <button type="submit" class="rounded-md bg-amber-800 px-4 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
