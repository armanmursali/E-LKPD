<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, ImagePlus, Save, Sparkles } from '@lucide/vue';
import { ref } from 'vue';

type Kelas = { id: number; nama: string; deskripsi: string | null; public_hero_image: string | null };
const props = defineProps<{ kelas: Kelas | null }>();
const isEditing = Boolean(props.kelas);
const form = useForm<{ nama: string; deskripsi: string; public_hero_image: string | File | null }>({
    nama: props.kelas?.nama ?? '',
    deskripsi: props.kelas?.deskripsi ?? '',
    public_hero_image: props.kelas?.public_hero_image ?? null,
});
const imageInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(props.kelas?.public_hero_image ?? null);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Kelas',
                href: '/kelas',
            },
            {
                title: 'Form Kelas',
            },
        ],
    },
});

function submit() {
    form.transform((data) => ({
        ...data,
        _method: isEditing ? 'put' : undefined,
    })).post(isEditing ? `/kelas/${props.kelas!.id}` : '/kelas', { forceFormData: true });
}

function chooseImage(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.public_hero_image = file;
    imagePreview.value = URL.createObjectURL(file);
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Kelas' : 'Tambah Kelas'" />
    <div class="min-h-full flex-1 bg-amber-50/60 px-4 py-6 sm:px-6 md:px-8 md:py-10 dark:bg-background">
        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <Link
                    href="/kelas"
                    class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-amber-900 shadow-xs transition hover:border-amber-300 hover:bg-amber-50 dark:border-border dark:bg-card dark:text-amber-100"
                >
                    <ArrowLeft class="size-3.5" />
                    Kembali ke daftar kelas
                </Link>
                <div class="mt-4 flex items-center gap-3">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-amber-800 text-white shadow-sm">
                        <BookOpen class="size-5" />
                    </span>
                    <div>
                        <p class="text-xs font-bold tracking-[0.18em] text-amber-700 uppercase dark:text-amber-400">
                            {{ isEditing ? 'Perbarui Kelas' : 'Kelas Baru' }}
                        </p>
                        <h1 class="text-2xl font-bold tracking-tight text-amber-950 sm:text-3xl dark:text-amber-50">
                            {{ isEditing ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
                        </h1>
                    </div>
                </div>
            </div>

            <form
                class="space-y-6 rounded-2xl border border-amber-200 bg-white p-6 shadow-sm sm:p-8 dark:border-border dark:bg-card"
                @submit.prevent="submit"
            >
                <div class="space-y-2">
                    <label for="nama" class="block text-sm font-bold text-amber-950 dark:text-amber-100">
                        Nama Kelas <span class="text-red-600">*</span>
                    </label>
                    <input
                        id="nama"
                        v-model="form.nama"
                        type="text"
                        required
                        class="w-full rounded-lg border border-amber-300 bg-white px-3.5 py-2.5 text-sm text-amber-950 transition outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-500/20 dark:border-border dark:bg-background dark:text-foreground"
                        placeholder="Contoh: Biologi Kelas X - Ekosistem"
                    />
                    <p v-if="form.errors.nama" class="text-sm text-destructive">{{ form.errors.nama }}</p>
                </div>

                <div class="space-y-2">
                    <label for="deskripsi" class="block text-sm font-bold text-amber-950 dark:text-amber-100">
                        Deskripsi Kelas <span class="font-normal text-amber-900/60 dark:text-muted-foreground">(opsional)</span>
                    </label>
                    <textarea
                        id="deskripsi"
                        v-model="form.deskripsi"
                        rows="4"
                        class="w-full rounded-lg border border-amber-300 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-amber-950 transition outline-none focus:border-amber-600 focus:ring-2 focus:ring-amber-500/20 dark:border-border dark:bg-background dark:text-foreground"
                        placeholder="Jelaskan secara singkat topik atau tujuan pembelajaran di kelas ini..."
                    ></textarea>
                    <p v-if="form.errors.deskripsi" class="text-sm text-destructive">{{ form.errors.deskripsi }}</p>
                </div>

                <div class="space-y-3">
                    <label for="public_hero_image" class="block text-sm font-bold text-amber-950 dark:text-amber-100">
                        Gambar Hero Halaman Siswa <span class="font-normal text-amber-900/60 dark:text-muted-foreground">(opsional)</span>
                    </label>
                    <div class="rounded-xl border-2 border-dashed border-amber-200 bg-amber-50/50 p-4 dark:border-border dark:bg-muted/30">
                        <div v-if="imagePreview" class="mb-3 overflow-hidden rounded-lg border border-amber-200">
                            <img :src="imagePreview" alt="Pratinjau gambar hero" class="h-44 w-full object-cover" />
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <ImagePlus class="size-5 text-amber-700" />
                            <input
                                id="public_hero_image"
                                ref="imageInput"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="flex-1 text-sm text-amber-900/80 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-amber-800 file:px-3.5 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-amber-700"
                                @change="chooseImage"
                            />
                        </div>
                        <p class="mt-2.5 flex items-center gap-1.5 text-xs text-amber-900/65 dark:text-muted-foreground">
                            <Sparkles class="size-3.5 shrink-0 text-amber-600" />
                            Unggah gambar yang tampil di bagian atas halaman publik siswa. Kosongkan untuk memakai ilustrasi bawaan.
                        </p>
                    </div>
                    <p v-if="form.errors.public_hero_image" class="text-sm text-destructive">{{ form.errors.public_hero_image }}</p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-amber-100 pt-5 dark:border-border">
                    <Link
                        href="/kelas"
                        class="rounded-lg border border-amber-300 px-4 py-2.5 text-sm font-semibold text-amber-900 transition hover:bg-amber-50 dark:border-border dark:text-foreground dark:hover:bg-muted"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-amber-700 hover:shadow-md disabled:opacity-50"
                    >
                        <Save class="size-4" />
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Kelas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
