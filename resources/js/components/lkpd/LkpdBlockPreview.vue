<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { extractYoutubeId, sanitizeRichText, type LkpdBlock } from '@/types/lkpd-block';

const props = defineProps<{
    block: LkpdBlock;
    interactive?: boolean;
    answerName?: string;
    answers?: Record<string, unknown>;
}>();

function savedAnswer() {
    return props.answerName ? props.answers?.[props.answerName] : undefined;
}

function savedAnswerText() {
    const value = savedAnswer();
    return Array.isArray(value) ? value.join(', ') : typeof value === 'object' && value !== null ? JSON.stringify(value) : String(value ?? '');
}

function optionSelected(value: string) {
    const answer = savedAnswer();
    if (Array.isArray(answer)) return answer.includes(value);
    if (answer === value) return true;
    return Object.entries(props.answers ?? {}).some(([key, item]) => key.startsWith(`${props.answerName}.`) && item === value);
}

function savedPhoto() {
    if (!props.answerName || !props.answers) return '';
    const key = props.answerName.replace(/^jawaban\./, 'foto_jawaban.');
    return String(props.answers[key] ?? '');
}

function formFieldName(name: string | undefined, array = false) {
    if (!name) return undefined;
    const parts = name.split('.');
    const root = parts.shift();
    return `${root}${parts.map((part) => `[${part}]`).join('')}${array ? '[]' : ''}`;
}

function photoFieldName() {
    return formFieldName(`foto_jawaban.${props.answerName?.replace(/^jawaban\./, '') ?? '0'}`);
}

const matchingAnswer = computed(() => ({
    aligned: alignedAnswers.value,
    lines: lineAnswers.value,
}));

const draggedRightIndex = ref<number | null>(null);
const selectedLeftIndex = ref<number | null>(null);
const alignedAnswers = ref<Record<number, number>>({});
const lineAnswers = ref<Record<number, number>>({});
const lineContainer = ref<HTMLElement | null>(null);
const draggingLine = ref<{ leftIndex: number; x: number; y: number } | null>(null);
const lineAnchors = ref<Record<string, { x: number; y: number }>>({});
let lineResizeObserver: ResizeObserver | null = null;

if (!props.interactive && props.block.type === 'input_matching') {
    try {
        const saved = savedAnswer();
        const parsed = typeof saved === 'string' ? JSON.parse(saved) : saved;
        if (parsed?.aligned) alignedAnswers.value = parsed.aligned;
        if (parsed?.lines) lineAnswers.value = parsed.lines;
    } catch {}
}

async function updateLineAnchors() {
    await nextTick();
    const container = lineContainer.value;
    if (!container || props.block.type !== 'input_matching') return;

    const containerRect = container.getBoundingClientRect();
    const nextAnchors: Record<string, { x: number; y: number }> = {};

    props.block.pairs.forEach((_, index) => {
        (['left', 'right'] as const).forEach((side) => {
            const selector = side === 'left' ? `[data-matching-left="${index}"]` : `[data-matching-right-anchor="${index}"]`;
            const anchor = container.querySelector(selector);
            if (!anchor) return;

            const anchorRect = anchor.getBoundingClientRect();
            nextAnchors[`${side}-${index}`] = {
                x: ((anchorRect.left + anchorRect.width / 2 - containerRect.left) / containerRect.width) * 100,
                y: ((anchorRect.top + anchorRect.height / 2 - containerRect.top) / containerRect.height) * 100,
            };
        });
    });

    lineAnchors.value = nextAnchors;
}

onMounted(() => {
    if (props.block.type !== 'input_matching') return;
    updateLineAnchors();
    lineResizeObserver = new ResizeObserver(updateLineAnchors);
    if (lineContainer.value) lineResizeObserver.observe(lineContainer.value);
});

onBeforeUnmount(() => lineResizeObserver?.disconnect());

function startDrag(index: number) {
    if (props.interactive) draggedRightIndex.value = index;
}

function dropOnLeft(leftIndex: number) {
    if (draggedRightIndex.value === null) return;
    alignedAnswers.value[leftIndex] = draggedRightIndex.value;
    draggedRightIndex.value = null;
}

function selectedRight(leftIndex: number) {
    if (props.block.type !== 'input_matching') return null;
    const rightIndex = alignedAnswers.value[leftIndex];
    return rightIndex === undefined ? null : props.block.pairs[rightIndex];
}

function selectLineLeft(index: number) {
    if (props.interactive) selectedLeftIndex.value = index;
}

function selectLineRight(index: number) {
    if (!props.interactive || selectedLeftIndex.value === null) return;
    lineAnswers.value[selectedLeftIndex.value] = index;
    selectedLeftIndex.value = null;
}

function lineY(index: number, count: number) {
    const rowHeight = 48;
    const rowGap = 8;
    const totalHeight = count * rowHeight + Math.max(0, count - 1) * rowGap;

    return ((index * (rowHeight + rowGap) + rowHeight / 2) / totalHeight) * 100;
}

function startLineDrag(index: number, event: PointerEvent) {
    if (!props.interactive || props.block.type !== 'input_matching') return;
    const rect = lineContainer.value?.getBoundingClientRect();
    if (!rect) return;
    (event.currentTarget as HTMLElement).setPointerCapture?.(event.pointerId);
    draggingLine.value = { leftIndex: index, x: 50, y: lineY(index, props.block.pairs.length) };
    window.addEventListener('pointermove', moveLineDrag);
    window.addEventListener('pointerup', finishLineDrag, { once: true });
}

function moveLineDrag(event: PointerEvent) {
    const rect = lineContainer.value?.getBoundingClientRect();
    if (!rect || !draggingLine.value) return;
    draggingLine.value.x = ((event.clientX - rect.left) / rect.width) * 100;
    draggingLine.value.y = ((event.clientY - rect.top) / rect.height) * 100;
}

function finishLineDrag(event: PointerEvent) {
    const target = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-matching-right]') as HTMLElement | null;
    if (target && draggingLine.value) {
        lineAnswers.value[draggingLine.value.leftIndex] = Number(target.dataset.matchingRight);
    }
    draggingLine.value = null;
    window.removeEventListener('pointermove', moveLineDrag);
}

function lineAnchor(index: number, side: 'left' | 'right') {
    return lineAnchors.value[`${side}-${index}`] ?? {
        x: side === 'left' ? 0 : 100,
        y: lineY(index, props.block.type === 'input_matching' ? props.block.pairs.length : 1),
    };
}

const youtubeId = computed(() => (props.block.type === 'video' ? extractYoutubeId(props.block.url) : null));

const alignClass: Record<string, string> = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
    justify: 'text-justify',
};

const positionClass: Record<string, string> = {
    left: 'sm:float-left sm:mr-4 sm:mb-2 w-full sm:w-2/5',
    right: 'sm:float-right sm:ml-4 sm:mb-2 w-full sm:w-2/5',
    center: 'mx-auto w-full sm:w-2/3',
    full: 'w-full',
};

type StyledQuestion = Extract<LkpdBlock, { type: 'input_radio' | 'input_checkbox' | 'input_matching' }>;

function questionStyle(block: LkpdBlock): Record<string, string> {
    if (!['input_radio', 'input_checkbox', 'input_matching'].includes(block.type)) return {};

    const question = block as StyledQuestion;

    return {
        backgroundColor: question.gunakanBackground === false ? 'transparent' : (question.warnaBackground ?? 'var(--lkpd-question-background)'),
        color: question.warnaTeks ?? 'var(--lkpd-question-foreground)',
        borderColor: question.gunakanBorder === false ? 'transparent' : (question.warnaBorder ?? 'var(--lkpd-question-border)'),
        borderWidth: question.gunakanBorder === false ? '0' : '1px',
    };
}

function questionLabel(block: LkpdBlock): string {
    if ('label' in block && typeof block.label === 'string') {
        return block.label || 'Pertanyaan';
    }

    return '';
}
</script>

<template>
    <div
        v-if="block.type === 'paragraph'"
        :class="['max-w-full break-words text-sm text-amber-950 [overflow-wrap:anywhere] sm:text-base dark:text-foreground [&_ul]:list-disc [&_ul]:pl-5 [&_li]:my-0.5', alignClass[block.align]]"
        :style="{ lineHeight: block.jarakBaris ?? 1.5, color: block.warnaTeks ?? '#451a03' }"
        v-html="sanitizeRichText(block.text) || 'Paragraf kosong.'"
    />

    <figure v-else-if="block.type === 'image'" :class="[positionClass[block.position], 'max-w-full']">
        <img v-if="block.url" :src="block.url" :alt="block.caption" class="rounded-md border border-amber-300 object-contain dark:border-border" :style="{ width: `${block.lebar ?? 100}%` }" />
        <div v-else class="flex aspect-video w-full items-center justify-center rounded-md border border-dashed border-amber-400 bg-amber-50 text-xs text-amber-700 dark:border-border dark:bg-muted dark:text-muted-foreground">
            Belum ada gambar
        </div>
        <figcaption v-if="block.caption" class="mt-1 text-center text-xs text-amber-800 italic dark:text-muted-foreground">{{ block.caption }}</figcaption>
    </figure>

    <div v-else-if="block.type === 'label'" class="relative w-full overflow-visible">
        <div
            class="relative flex w-full items-center border text-sm font-semibold"
            :style="{
                backgroundColor: block.bgColor ?? '#fef3c7',
                borderColor: block.borderColor ?? '#b45309',
                color: block.textColor ?? '#451a03',
                borderRadius: `${block.rounded ?? 16}px`,
                paddingTop: '0.5rem',
                paddingBottom: '0.5rem',
                paddingLeft: block.badgeText || block.badgeText === '' ? `${(block.badgeSize ?? 32) + 12}px` : '0.75rem',
                paddingRight: block.image ? `${(block.imageSize ?? 32) + 12}px` : '0.75rem',
            }"
        >
            <span
                v-if="block.badgeText || block.badgeText === ''"
                class="absolute left-0 top-1/2 flex -translate-y-1/2 items-center justify-center rounded-full border-2"
                :style="{
                    borderColor: block.badgeBorderColor ?? block.borderColor ?? '#b45309',
                    backgroundColor: block.badgeBgColor ?? block.bgColor ?? '#fef3c7',
                    color: block.badgeTextColor ?? block.textColor ?? '#451a03',
                    width: `${block.badgeSize ?? 32}px`,
                    height: `${block.badgeSize ?? 32}px`,
                    fontSize: `${block.badgeFontSize ?? 14}px`,
                    fontWeight: block.badgeBold ? '700' : '500',
                }"
            >
                {{ block.badgeText || '•' }}
            </span>
            <span class="min-w-0 flex-1 text-left">
                {{ block.text || 'Label' }}
            </span>
            <img
                v-if="block.image"
                :src="block.image"
                alt=""
                class="absolute top-1/2 rounded-full object-cover"
                :style="{
                    width: `${block.imageSize ?? 32}px`,
                    height: `${block.imageSize ?? 32}px`,
                    right: block.imagePosition === 'right' ? '0.75rem' : undefined,
                    left: block.imagePosition === 'left' ? `${(block.badgeSize ?? 32) + 18}px` : undefined,
                    transform: 'translateY(-50%)',
                }"
            />
        </div>
    </div>

    <div v-else-if="block.type === 'image_text'" class="flow-root min-w-0 max-w-full">
        <figure
            :class="[
                'mb-3 max-w-full sm:mb-2',
                block.position === 'right' ? 'sm:float-right sm:ml-4' : 'sm:float-left sm:mr-4',
            ]"
            :style="{ width: `${block.lebar ?? 33}%` }"
        >
            <img v-if="block.url" :src="block.url" :alt="block.caption" class="w-full rounded-md border border-amber-300 object-contain dark:border-border" />
            <div v-else class="flex aspect-video w-full items-center justify-center rounded-md border border-dashed border-amber-400 bg-amber-50 text-xs text-amber-700 dark:border-border dark:bg-muted dark:text-muted-foreground">
                Belum ada gambar
            </div>
            <figcaption v-if="block.caption" class="mt-1 text-center text-xs text-amber-800 italic dark:text-muted-foreground">{{ block.caption }}</figcaption>
        </figure>
        <div :class="['min-w-0 max-w-full break-words text-sm leading-relaxed text-amber-950 [overflow-wrap:anywhere] sm:text-base dark:text-foreground', alignClass[block.align]]" v-html="sanitizeRichText(block.text) || 'Teks kosong.'" />
    </div>

    <div v-else-if="block.type === 'fill_blank'" class="max-w-full break-words text-sm leading-relaxed text-amber-950 [overflow-wrap:anywhere] sm:text-base dark:text-foreground">
        <div class="flex flex-wrap items-end gap-x-1 gap-y-1 text-justify">
            <template v-for="(segment, index) in block.segments" :key="index">
                <span v-if="segment.type === 'text'" class="inline-block" v-html="sanitizeRichText(segment.value)" />
                <input
                    v-else
                    type="text"
                    :name="formFieldName(`${answerName}.${index}`)"
                    :value="!interactive ? (answers?.[`${answerName}.${index}`] ?? '') : undefined"
                    :aria-label="`Jawaban kosong ${index + 1}`"
                    :disabled="!interactive"
                    class="mx-1 min-w-[4rem] flex-1 border-0 border-b-2 border-amber-700 bg-transparent px-1 py-0.5 text-center text-sm text-current outline-none focus:border-amber-900 disabled:opacity-60 dark:border-amber-400"
                />
            </template>
        </div>
    </div>

    <div v-else-if="block.type === 'input_image'" class="rounded-md border border-amber-300 p-3 dark:border-border" :style="questionStyle(block)">
        <p class="text-sm text-current" v-html="sanitizeRichText(block.label) || 'Upload gambar siswa'" />
        <input v-if="interactive" :name="photoFieldName()" type="file" accept="image/*" class="mt-2 w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-xs dark:border-border dark:bg-muted file:mr-2 file:rounded file:border-0 file:bg-amber-800 file:px-2 file:py-1 file:text-white" />
        <img v-else-if="savedPhoto()" :src="savedPhoto()" alt="Foto jawaban siswa" class="mt-2 max-h-64 rounded object-contain" />
        <div v-else class="mt-2 flex min-h-[110px] items-center justify-center rounded-md border border-dashed border-amber-400 bg-amber-50 px-3 py-4 text-center text-xs text-amber-700 dark:border-border dark:bg-muted dark:text-muted-foreground">Siswa belum mengunggah gambar</div>
    </div>

    <div v-else-if="block.type === 'table'" class="max-w-full overflow-x-auto">
        <table class="w-full table-fixed border-collapse text-sm text-amber-950 dark:text-foreground">
            <colgroup>
                <col v-for="(width, columnIndex) in block.columnWidths" :key="columnIndex" :style="{ width: `${width}%` }" />
            </colgroup>
            <tbody>
                <tr v-for="(row, rowIndex) in block.rows" :key="rowIndex" :style="{ height: `${block.rowHeights?.[rowIndex] ?? 80}px`, backgroundColor: rowIndex < (block.headerRows ?? 0) ? block.headerColor : undefined, color: rowIndex < (block.headerRows ?? 0) ? block.headerTextColor : undefined }">
                    <component :is="rowIndex < (block.headerRows ?? 0) ? 'th' : 'td'" v-for="(cell, cellIndex) in row" :key="cellIndex" class="border border-amber-300 p-2 text-left align-top" :style="{ backgroundColor: rowIndex < (block.headerRows ?? 0) ? block.headerColor : undefined, color: rowIndex < (block.headerRows ?? 0) ? block.headerTextColor : undefined }">
                        <div v-if="cell.type === 'paragraph'" class="break-words [overflow-wrap:anywhere]" :style="{ lineHeight: cell.jarakBaris ?? 1.5 }" v-html="sanitizeRichText(cell.text) || '&nbsp;'" />
                        <img v-else-if="cell.type === 'image' && cell.url" :src="cell.url" alt="" class="max-w-full rounded object-contain" :style="{ width: `${cell.lebar ?? 100}%` }" />
                        <input v-else-if="cell.type === 'input_short_text'" :name="formFieldName(answerName)" :value="!interactive ? savedAnswerText() : undefined" :disabled="!interactive" type="text" class="w-full rounded border border-amber-300 bg-white px-2 py-1.5 text-current dark:border-border dark:bg-muted disabled:opacity-60" />
                        <textarea v-else-if="cell.type === 'input_long_text'" :name="formFieldName(answerName)" :disabled="!interactive" rows="3" class="w-full rounded border border-amber-300 bg-white px-2 py-1.5 text-current dark:border-border dark:bg-muted disabled:opacity-60">{{ !interactive ? savedAnswerText() : '' }}</textarea>
                        <span v-else class="text-xs text-amber-700">Belum ada isi</span>
                    </component>
                </tr>
            </tbody>
        </table>
    </div>

    <figure v-else-if="block.type === 'video'">
        <div v-if="youtubeId" class="aspect-video w-full overflow-hidden rounded-md border border-amber-300">
            <iframe
                :src="`https://www.youtube.com/embed/${youtubeId}`"
                class="size-full"
                title="Video pembelajaran"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            />
        </div>
        <div v-else class="flex aspect-video w-full items-center justify-center rounded-md border border-dashed border-amber-400 bg-amber-50 text-xs text-amber-700 dark:border-border dark:bg-muted dark:text-muted-foreground">
            Belum ada link YouTube yang valid
        </div>
        <figcaption v-if="block.caption" class="mt-1 text-center text-xs text-amber-800 italic">{{ block.caption }}</figcaption>
    </figure>

    <div v-else class="rounded-md border border-amber-300 p-3" :style="questionStyle(block)">
        <p v-if="interactive || questionLabel(block)" class="text-sm text-current" v-html="sanitizeRichText(questionLabel(block)) || (interactive ? 'Pertanyaan' : '')" />
        <p v-if="block.type === 'input_matching'" class="mt-1 text-xs font-semibold text-amber-800">
            Mode: {{ block.metode === 'tarik_garis' ? 'Tarik garis' : 'Sejajarkan pasangan' }}
        </p>

        <input v-if="block.type === 'input_short_text'" :name="formFieldName(answerName)" :value="!interactive ? savedAnswerText() : undefined" type="text" :disabled="!interactive" placeholder="Jawaban singkat siswa" class="mt-2 w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-sm text-current dark:border-border dark:bg-muted disabled:opacity-60" />

        <textarea v-else-if="block.type === 'input_long_text'" :name="formFieldName(answerName)" :disabled="!interactive" rows="3" placeholder="Jawaban panjang siswa" class="mt-2 w-full rounded-md border border-amber-300 bg-white px-3 py-2 text-sm text-current dark:border-border dark:bg-muted disabled:opacity-60">{{ !interactive ? savedAnswerText() : '' }}</textarea>

        <div v-else-if="block.type === 'input_radio'" class="mt-2 space-y-1">
            <label v-for="(opt, i) in block.options" :key="i" class="flex items-center gap-2 text-sm text-current">
                <input :name="formFieldName(answerName)" :value="opt.teks" type="radio" :checked="!interactive && optionSelected(opt.teks)" :disabled="!interactive" />
                <span class="min-w-0 flex-1">
                    <img v-if="opt.gambar" :src="opt.gambar" alt="" class="mb-1 rounded object-contain object-left" :style="{ width: `${opt.lebar ?? 100}%` }" />
                    <span v-if="(opt.mode ?? (opt.gambar ? 'gambar' : 'teks')) !== 'gambar'">{{ opt.teks || `Opsi ${i + 1}` }}</span>
                </span>
            </label>
        </div>

        <div v-else-if="block.type === 'input_checkbox'" class="mt-2 space-y-1">
            <label v-for="(opt, i) in block.options" :key="i" class="flex items-center gap-2 text-sm text-current">
                <input :name="formFieldName(answerName, interactive)" :value="opt.teks" type="checkbox" :checked="!interactive && optionSelected(opt.teks)" :disabled="!interactive" />
                <span class="min-w-0 flex-1">
                    <img v-if="opt.gambar" :src="opt.gambar" alt="" class="mb-1 rounded object-contain object-left" :style="{ width: `${opt.lebar ?? 100}%` }" />
                    <span v-if="(opt.mode ?? (opt.gambar ? 'gambar' : 'teks')) !== 'gambar'">{{ opt.teks || `Opsi ${i + 1}` }}</span>
                </span>
            </label>
        </div>

        <div v-else-if="block.type === 'input_matching' && (block.metode ?? 'seret') === 'seret'" class="mt-3 space-y-3 text-sm text-current">
            <input v-if="interactive" type="hidden" :name="formFieldName(answerName)" :value="JSON.stringify(matchingAnswer)" />
            <p class="text-xs italic text-amber-800">Tarik kartu pasangan ke kotak yang sesuai.</p>
            <div v-for="(pair, i) in block.pairs" :key="i" class="grid grid-cols-[minmax(0,1fr)_1.5rem_minmax(0,1fr)] items-center gap-2">
                <div class="rounded border border-amber-300 bg-white p-2">
                    <img v-if="pair.gambarKiri" :src="pair.gambarKiri" alt="" class="mx-auto max-h-20 rounded object-contain" />
                    <span v-if="(pair.modeKiri ?? (pair.gambarKiri ? 'gambar' : 'teks')) !== 'gambar'">{{ pair.kiri || `Item ${i + 1}` }}</span>
                </div>
                <span class="text-center text-lg text-amber-700">→</span>
                <div class="min-h-12 rounded border-2 border-dashed border-amber-400 bg-white p-2" @dragover.prevent @drop.prevent="dropOnLeft(i)">
                    <template v-if="selectedRight(i)"><img v-if="selectedRight(i)?.gambarKanan" :src="selectedRight(i)?.gambarKanan" alt="" class="mx-auto max-h-20 rounded object-contain" /><span v-if="(selectedRight(i)?.modeKanan ?? (selectedRight(i)?.gambarKanan ? 'gambar' : 'teks')) !== 'gambar'">{{ selectedRight(i)?.kanan }}</span></template>
                    <span v-else class="text-xs text-amber-600">Letakkan pasangan di sini</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 border-t border-amber-200 pt-3 sm:grid-cols-3">
                <button v-for="(pair, i) in block.pairs" :key="`right-${i}`" type="button" draggable="true" class="cursor-grab rounded border border-amber-300 bg-amber-50 p-2 text-left active:cursor-grabbing" @dragstart="startDrag(i)"><img v-if="pair.gambarKanan" :src="pair.gambarKanan" alt="" class="mx-auto max-h-16 rounded object-contain" /><span v-if="(pair.modeKanan ?? (pair.gambarKanan ? 'gambar' : 'teks')) !== 'gambar'">{{ pair.kanan || `Pasangan ${i + 1}` }}</span></button>
            </div>
        </div>

        <div v-else-if="block.type === 'input_matching'" ref="lineContainer" class="relative mt-3 text-sm text-current">
            <input v-if="interactive" type="hidden" :name="formFieldName(answerName)" :value="JSON.stringify(matchingAnswer)" />
            <p class="mb-3 text-xs italic text-amber-800">Tarik titik di ujung item kiri ke titik pasangan. Garis boleh menyilang.</p>
            <div class="pointer-events-none absolute inset-0 z-10">
                <svg class="size-full overflow-visible" preserveAspectRatio="none" aria-hidden="true">
                    <line v-for="(rightIndex, leftIndex) in lineAnswers" :key="`answer-line-${leftIndex}`" :x1="`${lineAnchor(Number(leftIndex), 'left').x}%`" :y1="`${lineAnchor(Number(leftIndex), 'left').y}%`" :x2="`${lineAnchor(rightIndex, 'right').x}%`" :y2="`${lineAnchor(rightIndex, 'right').y}%`" stroke="#92400e" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                    <line v-if="draggingLine" :x1="`${lineAnchor(draggingLine.leftIndex, 'left').x}%`" :y1="`${lineAnchor(draggingLine.leftIndex, 'left').y}%`" :x2="`${draggingLine.x}%`" :y2="`${draggingLine.y}%`" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="5 4" vector-effect="non-scaling-stroke" />
                </svg>
            </div>
            <div class="relative z-20 grid grid-cols-[minmax(0,1fr)_3rem_minmax(0,1fr)] items-stretch gap-2">
                <div class="space-y-2"><div v-for="(pair, i) in block.pairs" :key="`left-${i}`" class="flex min-h-12 items-center justify-between gap-2 rounded border border-amber-300 bg-white p-2 text-left"><span class="min-w-0 flex-1"> <img v-if="pair.gambarKiri" :src="pair.gambarKiri" alt="" class="mb-1 max-h-16 rounded object-contain object-left" :style="{ width: `${pair.lebarKiri ?? 100}%` }" /><span v-if="(pair.modeKiri ?? (pair.gambarKiri ? 'gambar' : 'teks')) !== 'gambar'">{{ pair.kiri || `Item ${i + 1}` }}</span></span><button type="button" :data-matching-left="i" aria-label="Tarik garis dari item" :disabled="!interactive" class="size-4 shrink-0 touch-none rounded-full border-2 border-amber-800 bg-white disabled:cursor-default" :class="selectedLeftIndex === i ? 'ring-2 ring-amber-300' : ''" @pointerdown="startLineDrag(i, $event)" @click="selectLineLeft(i)" /></div></div>
                <div />
                <div class="space-y-2"><button v-for="(pair, i) in block.pairs" :key="`right-line-${i}`" :data-matching-right="i" type="button" :disabled="!interactive" class="flex min-h-12 w-full items-center rounded border bg-white p-2 text-left disabled:cursor-default" :class="Object.values(lineAnswers).includes(i) ? 'border-emerald-600 bg-emerald-50' : 'border-amber-300'" @click="selectLineRight(i)"><span :data-matching-right-anchor="i" class="mr-2 size-4 shrink-0 rounded-full border-2 border-amber-800 bg-white" /><span class="min-w-0 flex-1"><img v-if="pair.gambarKanan" :src="pair.gambarKanan" alt="" class="mb-1 max-h-16 rounded object-contain object-left" :style="{ width: `${pair.lebarKanan ?? 100}%` }" /><span v-if="(pair.modeKanan ?? (pair.gambarKanan ? 'gambar' : 'teks')) !== 'gambar'">{{ pair.kanan || `Pasangan ${i + 1}` }}</span></span></button></div>
            </div>
        </div>
    </div>
</template>
