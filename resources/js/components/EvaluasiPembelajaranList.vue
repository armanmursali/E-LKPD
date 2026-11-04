<script setup lang="ts">
import { BrainCircuit, ExternalLink, Pencil, Plus, Trash2, UsersRound } from '@lucide/vue';
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
                <div class="illustration-circle">
                    <component :is="kartu.icon" class="illustration-icon" :stroke-width="1.35" />
                </div>
            </div>
            <div class="test-actions">
                <a v-if="itemByJenis(kartu.items, 'Pre-test')" :href="itemByJenis(kartu.items, 'Pre-test')?.google_form_url" target="_blank" rel="noreferrer">
                    <span>Pre-test {{ kartu.judul }}</span>
                    <ExternalLink class="size-3.5 shrink-0" />
                </a>
                <button v-else-if="canManage" type="button" class="missing-test" @click="emit('addMissing', 'Pre-test', kartu.judul)">
                    <Plus class="size-3.5" /> Tambah Pre-test
                </button>
                <span v-else class="missing-test disabled-test">Pre-test belum tersedia</span>

                <a v-if="itemByJenis(kartu.items, 'Post-test')" :href="itemByJenis(kartu.items, 'Post-test')?.google_form_url" target="_blank" rel="noreferrer">
                    <span>Post-test {{ kartu.judul }}</span>
                    <ExternalLink class="size-3.5 shrink-0" />
                </a>
                <button v-else-if="canManage" type="button" class="missing-test" @click="emit('addMissing', 'Post-test', kartu.judul)">
                    <Plus class="size-3.5" /> Tambah Post-test
                </button>
                <span v-else class="missing-test disabled-test">Post-test belum tersedia</span>
            </div>
        </article>
    </div>

    <div v-else class="empty-state">
        <BrainCircuit class="size-12" />
        <h2>Belum ada angket untuk kelas ini</h2>
        <p>Angket evaluasi Pre-test dan Post-test yang tersedia akan muncul di sini.</p>
    </div>
</template>

<style scoped>
.evaluation-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
.evaluation-card { display: flex; min-height: 330px; flex-direction: column; overflow: hidden; border: 1px solid rgba(104, 72, 46, 0.22); border-radius: 16px; background: #fff; padding: 18px; box-shadow: 0 8px 24px rgba(75, 48, 28, 0.14); animation: card-in 0.45s ease both; animation-delay: calc(var(--card-order) * 90ms); transition: transform 0.2s, box-shadow 0.2s; }
.evaluation-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(75, 48, 28, 0.22); }
.card-heading { display: flex; align-items: start; justify-content: space-between; gap: 8px; padding-bottom: 8px; color: #5e402a; }
.card-heading h2 { margin: 0; font-size: clamp(1.1rem, 1.8vw, 1.35rem); font-weight: 800; line-height: 1.3; text-align: left; }
.card-actions { display: flex; gap: 4px; shrink: 0; }
.card-actions button { border: 1px solid #ead8c5; border-radius: 8px; padding: 5px; background: #fffaf2; color: #8c6544; cursor: pointer; transition: background 0.15s, color 0.15s; }
.card-actions button:hover { background: #f3e4d2; color: #5e402a; }
.illustration { display: flex; min-height: 145px; flex: 1; align-items: center; justify-content: center; color: #9b7147; padding: 12px 0; }
.illustration-circle { display: flex; width: 120px; height: 120px; align-items: center; justify-content: center; border-radius: 999px; background: #fdf5ea; border: 2px dashed #e2c5a3; }
.illustration-icon { width: 68px; height: 68px; }
.test-actions { display: grid; gap: 8px; margin-top: auto; }
.test-actions a, .missing-test { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; border: 0; border-radius: 10px; background: #79563d; color: #fff; padding: 10px 12px; font-size: 0.82rem; font-weight: 700; text-align: center; text-decoration: none; cursor: pointer; transition: background 0.15s, transform 0.15s; }
.test-actions a:hover, .missing-test:hover { background: #5e402a; transform: translateY(-1px); }
.missing-test { background: #f5ebe0; color: #79563d; border: 1px dashed #c7a687; }
.missing-test:hover { background: #ead8c5; color: #5e402a; }
.disabled-test { cursor: default; opacity: 0.7; transform: none !important; }
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 10px; border: 2px dashed rgba(121, 86, 61, 0.45); border-radius: 16px; background: rgba(255, 255, 255, 0.85); padding: 56px 20px; color: #79563d; text-align: center; }
.empty-state h2 { margin: 0; font-size: 1.25rem; font-weight: 800; }
.empty-state p { margin: 0; font-size: 0.9rem; opacity: 0.85; }
@keyframes card-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 700px) { .evaluation-grid { grid-template-columns: 1fr; } .evaluation-card { min-height: 295px; } }
</style>
