<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, X } from '@lucide/vue';
import { ref } from 'vue';
import EvaluasiPembelajaranList, { type EvaluasiItem } from '@/components/EvaluasiPembelajaranList.vue';

const props = defineProps<{
    kelas: { id: number; nama: string };
    evaluasi: EvaluasiItem[];
    jenisPilihan: string[];
}>();

const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({
    judul: '',
    jenis: props.jenisPilihan[0] ?? 'Pre-test',
    deskripsi: '',
    google_form_url: '',
});

defineOptions({
    layout: { breadcrumbs: [{ title: 'Detail Kelas' }, { title: 'Evaluasi Pembelajaran' }] },
});

function bukaForm() {
    isFormOpen.value = true;
    editingId.value = null;
    form.reset();
    form.clearErrors();
}

function mulaiEdit(item: EvaluasiItem) {
    isFormOpen.value = true;
    editingId.value = item.id;
    form.defaults({
        judul: item.judul,
        jenis: item.jenis,
        deskripsi: item.deskripsi ?? '',
        google_form_url: item.google_form_url,
    });
    form.reset();
}

function tambahMissing(jenis: string, judul: string) {
    isFormOpen.value = true;
    editingId.value = null;
    form.reset();
    form.judul = judul;
    form.jenis = jenis;
    form.clearErrors();
}

function tutupForm() {
    isFormOpen.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
}

function submit() {
    const options = { onSuccess: tutupForm };

    if (editingId.value) {
        form.put(`/kelas/${props.kelas.id}/evaluasi-pembelajaran/${editingId.value}`, options);
    } else {
        form.post(`/kelas/${props.kelas.id}/evaluasi-pembelajaran`, options);
    }
}

function hapus(item: EvaluasiItem) {
    if (!window.confirm(`Hapus angket "${item.judul}"?`)) return;
    form.delete(`/kelas/${props.kelas.id}/evaluasi-pembelajaran/${item.id}`);
}
</script>

<template>
    <Head title="Evaluasi Pembelajaran" />

    <div class="evaluation-page">
        <header class="evaluation-header">
            <Link href="/kelas" class="brand-mark">E-LKPD WEB</Link>
            <div class="header-context">
                <span>{{ kelas.nama }}</span>
                <Link :href="`/kelas/${kelas.id}`" class="back-link">Kembali</Link>
            </div>
        </header>

        <main class="evaluation-content">
            <div class="intro-row">
                <div>
                    <p class="eyebrow">Evaluasi Pembelajaran</p>
                    <h1>Pilih angket untuk dikerjakan</h1>
                </div>
                <button type="button" class="manage-button" @click="bukaForm"><Plus class="size-4" /> Tambah angket</button>
            </div>

            <EvaluasiPembelajaranList
                :evaluasi="evaluasi"
                :can-manage="true"
                @edit="mulaiEdit"
                @delete="hapus"
                @add-missing="tambahMissing"
            />
        </main>

        <div v-if="isFormOpen" class="form-backdrop" @click.self="tutupForm">
            <section class="form-panel">
                <div class="form-panel-header">
                    <div><p class="eyebrow">{{ editingId ? 'Perbarui data' : 'Angket baru' }}</p><h2>{{ editingId ? 'Edit angket' : 'Tambah angket' }}</h2></div>
                    <button type="button" title="Tutup" @click="tutupForm"><X class="size-5" /></button>
                </div>
                <form class="form-fields" @submit.prevent="submit">
                    <label>Judul kartu<input v-model="form.judul" required type="text" placeholder="Contoh: Keterampilan Berpikir Kritis" /><small v-if="form.errors.judul">{{ form.errors.judul }}</small></label>
                    <label>Jenis angket<select v-model="form.jenis" required><option v-for="jenis in jenisPilihan" :key="jenis">{{ jenis }}</option></select><small v-if="form.errors.jenis">{{ form.errors.jenis }}</small></label>
                    <label>Deskripsi <span>(opsional)</span><textarea v-model="form.deskripsi" rows="3" placeholder="Keterangan singkat angket"></textarea><small v-if="form.errors.deskripsi">{{ form.errors.deskripsi }}</small></label>
                    <label>Link Google Form<input v-model="form.google_form_url" required type="url" placeholder="https://forms.google.com/..." /><small v-if="form.errors.google_form_url">{{ form.errors.google_form_url }}</small></label>
                    <button type="submit" class="save-button" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan angket' }}</button>
                </form>
            </section>
        </div>
    </div>
</template>

<style scoped>
.evaluation-page {
    --terracotta: #9b7147;
    --terracotta-dark: #79563d;
    --sand: #ddb27b;
    min-height: 100%;
    background-color: var(--sand);
    background-image: linear-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.14) 1px, transparent 1px);
    background-size: 30px 30px;
    color: #fff;
}
.evaluation-header { display: flex; align-items: center; justify-content: space-between; padding: 14px clamp(20px, 4vw, 48px); }
.brand-mark, .back-link { color: #fff; font-weight: 800; text-decoration: none; }
.brand-mark { font-size: 1.25rem; letter-spacing: 0.02em; }
.header-context { display: flex; align-items: center; gap: 28px; font-size: 0.95rem; font-weight: 700; }
.header-context > span { opacity: 0.8; }
.back-link:hover { text-decoration: underline; }
.evaluation-content { width: min(100%, 1080px); margin: 0 auto; padding: 12px 24px 56px; }
.intro-row { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 26px; }
.eyebrow { margin: 0 0 5px; color: var(--terracotta-dark); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; }
.intro-row h1 { margin: 0; color: #fff; font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; }
.manage-button, .save-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 7px; background: var(--terracotta-dark); color: #fff; padding: 11px 16px; font-weight: 700; cursor: pointer; box-shadow: 0 3px 0 rgba(83, 51, 32, 0.2); }
.manage-button:hover, .save-button:hover { background: #67472f; }
.form-backdrop { position: fixed; inset: 0; z-index: 10; display: grid; place-items: center; padding: 20px; background: rgba(55, 36, 24, 0.48); }
.form-panel { width: min(100%, 470px); border-radius: 10px; background: #fff; color: #3c2b21; padding: 24px; box-shadow: 0 16px 50px rgba(50, 30, 15, 0.35); }
.form-panel-header { display: flex; align-items: start; justify-content: space-between; }
.form-panel-header h2 { margin: 0; font-size: 1.45rem; color: var(--terracotta-dark); }
.form-fields { display: grid; gap: 14px; margin-top: 20px; }
.form-fields label { display: grid; gap: 6px; color: #604833; font-size: 0.85rem; font-weight: 700; }
.form-fields label span { color: #a58b75; font-weight: 400; }
.form-fields input, .form-fields select, .form-fields textarea { width: 100%; border: 1px solid #d9c7b6; border-radius: 5px; background: #fffdfb; padding: 10px 11px; color: #3c2b21; font: inherit; font-weight: 400; outline: none; }
.form-fields input:focus, .form-fields select:focus, .form-fields textarea:focus { border-color: var(--terracotta); box-shadow: 0 0 0 3px rgba(155, 113, 71, 0.15); }
.form-fields small { color: #b42318; font-weight: 500; }
.save-button { margin-top: 4px; }
@keyframes card-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 700px) { .evaluation-header { padding-inline: 20px; } .header-context > span { display: none; } .evaluation-content { padding-inline: 16px; } .intro-row { align-items: start; flex-direction: column; } }
</style>
