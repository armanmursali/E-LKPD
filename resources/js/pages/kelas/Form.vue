<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

type Kelas = { id: number; nama: string; deskripsi: string | null; public_hero_image: string | null };
const props = defineProps<{ kelas: Kelas | null }>();
const isEditing = Boolean(props.kelas);
const form = useForm<{ nama: string; deskripsi: string; public_hero_image: string | File | null }>({ nama: props.kelas?.nama ?? '', deskripsi: props.kelas?.deskripsi ?? '', public_hero_image: props.kelas?.public_hero_image ?? null });
const imageInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(props.kelas?.public_hero_image ?? null);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Form',
              
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
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-8">
        <div><Link href="/kelas" class="text-sm text-muted-foreground hover:underline">Kembali ke daftar kelas</Link><h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ isEditing ? 'Edit kelas' : 'Tambah kelas' }}</h1></div>
        <form class="max-w-2xl space-y-5 rounded-xl border bg-card p-6 shadow-sm" @submit.prevent="submit">
            <div class="space-y-2"><label for="nama" class="text-sm font-medium">Nama kelas</label><input id="nama" v-model="form.nama" type="text" required class="w-full rounded-md border bg-background px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="Contoh: Matematika Dasar" /><p v-if="form.errors.nama" class="text-sm text-destructive">{{ form.errors.nama }}</p></div>
            <div class="space-y-2"><label for="deskripsi" class="text-sm font-medium">Deskripsi <span class="font-normal text-muted-foreground">(opsional)</span></label><textarea id="deskripsi" v-model="form.deskripsi" rows="5" class="w-full rounded-md border bg-background px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="Jelaskan kelas ini..."></textarea><p v-if="form.errors.deskripsi" class="text-sm text-destructive">{{ form.errors.deskripsi }}</p></div>
            <div class="space-y-2"><label for="public_hero_image" class="text-sm font-medium">Gambar hero halaman siswa <span class="font-normal text-muted-foreground">(opsional)</span></label><input id="public_hero_image" ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" class="w-full rounded-md border bg-background px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-amber-800 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white" @change="chooseImage" /><img v-if="imagePreview" :src="imagePreview" alt="Pratinjau gambar hero" class="mt-2 h-28 w-full rounded-md object-cover" /><p class="text-xs text-muted-foreground">Unggah gambar yang tampil di bagian atas halaman publik kelas. Kosongkan untuk memakai ilustrasi kartun bawaan.</p><p v-if="form.errors.public_hero_image" class="text-sm text-destructive">{{ form.errors.public_hero_image }}</p></div>
            <div class="flex justify-end gap-3"><Link href="/kelas" class="rounded-md border px-4 py-2 text-sm hover:bg-muted">Batal</Link><button type="submit" :disabled="form.processing" class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:opacity-90 disabled:opacity-50">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button></div>
        </form>
    </div>
</template>
