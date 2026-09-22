<script setup lang="ts">
import { BrainCircuit, Pencil, Trash2, UsersRound } from '@lucide/vue';
import { computed } from 'vue';

export type EvaluasiItem = {
    id: number;
    judul: string;
    jenis: string;
    deskripsi: string | null;
    google_form_url: string;
};

const props = withDefaults(defineProps<{
    evaluasi: EvaluasiItem[];
    canManage?: boolean;
}>(), {
    canManage: false,
});

const emit = defineEmits<{
    edit: [item: EvaluasiItem];
    delete: [item: EvaluasiItem];
    addMissing: [jenis: string, judul: string];
}>();

const kartuEvaluasi = computed(() => {
    const groups = new Map<string, EvaluasiItem[]>();

    for (const item of props.evaluasi) {
        const judul = item.judul.trim() || 'Evaluasi Pembelajaran';
        groups.set(judul, [...(groups.get(judul) ?? []), item]);
    }

    return Array.from(groups, ([judul, items], index) => ({
        judul,
        items,
        icon: index % 2 === 0 ? BrainCircuit : UsersRound,
    }));
});

function itemByJenis(items: EvaluasiItem[], jenis: string) {
    return items.find((item) => item.jenis === jenis);
}
</script>

<template>
    <div v-if="kartuEvaluasi.length" class="evaluation-grid">
        <article v-for="(kartu, index) in kartuEvaluasi" :key="kartu.judul" class="evaluation-card" :style="{ '--card-order': index }">
            <div class="card-heading">
                <h2>{{ kartu.judul }}</h2>
                <div v-if="canManage && kartu.items[0]" class="card-actions">
                    <button type="button" title="Edit angket" @click="emit('edit', kartu.items[0])"><Pencil class="size-4" /></button>
                    <button type="button" title="Hapus angket" @click="emit('delete', kartu.items[0])"><Trash2 class="size-4" /></button>
                </div>
            </div>
            <div class="illustration" :class="index % 2 === 0 ? 'illustration-brain' : 'illustration-people'">
                <component :is="kartu.icon" class="illustration-icon" :stroke-width="1.35" />
            </div>
            <div class="test-actions">
                <a v-if="itemByJenis(kartu.items, 'Pre-test')" :href="itemByJenis(kartu.items, 'Pre-test')?.google_form_url" target="_blank" rel="noreferrer">Pre-test {{ kartu.judul }}</a>
                <button v-else-if="canManage" type="button" class="missing-test" @click="emit('addMissing', 'Pre-test', kartu.judul)">Tambah Pre-test</button>
                <span v-else class="missing-test disabled-test">Pre-test belum tersedia</span>
                <a v-if="itemByJenis(kartu.items, 'Post-test')" :href="itemByJenis(kartu.items, 'Post-test')?.google_form_url" target="_blank" rel="noreferrer">Post-test {{ kartu.judul }}</a>
                <button v-else-if="canManage" type="button" class="missing-test" @click="emit('addMissing', 'Post-test', kartu.judul)">Tambah Post-test</button>
                <span v-else class="missing-test disabled-test">Post-test belum tersedia</span>
            </div>
        </article>
    </div>

    <div v-else class="empty-state">
        <BrainCircuit class="size-12" />
        <h2>Belum ada angket untuk kelas ini</h2>
        <p>Angket yang tersedia akan muncul di sini.</p>
    </div>
</template>

<style scoped>
.evaluation-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px; }
.evaluation-card { display: flex; min-height: 315px; flex-direction: column; overflow: hidden; border: 1px solid rgba(104, 72, 46, 0.28); border-radius: 9px; background: #fff; box-shadow: 0 3px 5px rgba(75, 48, 28, 0.28); animation: card-in 0.45s ease both; animation-delay: calc(var(--card-order) * 90ms); }
.card-heading { display: flex; align-items: start; justify-content: center; gap: 8px; padding: 12px 12px 6px; color: #79563d; text-align: center; }
.card-heading h2 { margin: 0; font-size: clamp(1.15rem, 2vw, 1.5rem); font-weight: 800; line-height: 1.25; }
.card-actions { display: flex; gap: 4px; margin-top: 2px; }
.card-actions button { border: 0; background: transparent; color: #9f8265; cursor: pointer; }
.card-actions button:hover { color: #79563d; }
.illustration { display: flex; min-height: 150px; flex: 1; align-items: center; justify-content: center; color: #9b7147; }
.illustration-icon { width: min(48%, 135px); height: min(48%, 135px); }
.illustration-people .illustration-icon { width: min(50%, 145px); height: min(50%, 145px); }
.test-actions { display: grid; gap: 6px; padding: 0 12px 12px; }
.test-actions a, .missing-test { display: block; width: 100%; border: 0; border-radius: 4px; background: #79563d; color: #fff; padding: 8px 7px; font-size: 0.8rem; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer; }
.test-actions a:hover, .missing-test:hover { background: #62442f; }
.missing-test { background: #b99a7b; }
.disabled-test { cursor: default; opacity: 0.7; }
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 12px; border: 2px dashed rgba(121, 86, 61, 0.5); border-radius: 10px; padding: 64px 20px; color: #79563d; text-align: center; }
.empty-state h2, .empty-state p { margin: 0; }
@keyframes card-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 700px) { .evaluation-grid { grid-template-columns: 1fr; } .evaluation-card { min-height: 295px; } .illustration { min-height: 135px; } }
</style>
