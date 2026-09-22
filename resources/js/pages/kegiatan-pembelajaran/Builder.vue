<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AlignCenter, AlignJustify, AlignLeft, AlignRight, ArrowDown, ArrowUp, CheckSquare, CircleDot, Clapperboard, Columns2, GripVertical, Image, List, Plus, Text, Trash2, Type, UserRound } from '@lucide/vue';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import DynamicHeadContent from '@/components/lkpd/DynamicHeadContent.vue';
import ImageUploadField from '@/components/lkpd/ImageUploadField.vue';
import IdentityPreview from '@/components/lkpd/IdentityPreview.vue';
import LkpdBlockPreview from '@/components/lkpd/LkpdBlockPreview.vue';
import RichTextEditor from '@/components/lkpd/RichTextEditor.vue';
import {
    BLOCK_TYPE_LABELS,
    createBlock,
    DEFAULT_PENGATURAN,
    DYNAMIC_HEAD_SECTION_OPTIONS,
    F4_HEIGHT_MM,
    F4_WIDTH_MM,
    type DynamicHeadSectionId,
    type LkpdBlock,
    type LkpdBlockType,
    type PengaturanLkpd,
    type ChoiceOption,
    createTableCell,
    type TableBlock,
    type TableCell,
    type FillBlankSegment,
} from '@/types/lkpd-block';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Buat LKPD',
            },
        ],
    },
});

const props = defineProps<{
    kelas: { id: number; nama: string; deskripsi: string | null };
    nomor: number;
    judul: string;
    blocks: LkpdBlock[];
    pengaturan: PengaturanLkpd;
    aktif: boolean;
    identitasMapel?: {
        mata_pelajaran?: string;
        materi?: string;
        satuan_pendidikan?: string;
        tahun_pelajaran?: string;
        tahapan_fase?: string;
        kelas_label?: string;
        semester?: string;
        alokasi_waktu?: string;
        capaian_pembelajaran?: string;
        alur_tujuan_pembelajaran?: string;
        tujuan_pembelajaran?: string[];
        indikator_ketercapaian?: string[];
        model_pembelajaran?: Array<{ nomor: number; icon: string; teks: string }>;
    } | null;
}>();

const normalizedBlocks = props.blocks.map((block) => {
    if (block.type === 'table') {
        return {
            ...block,
            columnWidths: block.columnWidths?.length === block.rows[0]?.length
                ? block.columnWidths
                : block.rows[0].map(() => 100 / block.rows[0].length),
            rowHeights: block.rowHeights?.length === block.rows.length
                ? block.rowHeights
                : block.rows.map(() => 80),
            headerRows: Math.min(block.headerRows ?? 0, block.rows.length),
            headerColor: block.headerColor ?? '#fef3c7',
            headerTextColor: block.headerTextColor ?? '#78350f',
            rows: block.rows.map((row) => row.map((cell) => ({
                ...createTableCell(),
                ...cell,
                jarakBaris: cell.jarakBaris ?? 1.5,
            }))),
        };
    }

    if (block.type === 'paragraph') {
        return { ...block, jarakBaris: block.jarakBaris ?? 1.5, warnaTeks: block.warnaTeks ?? '#451a03' };
    }

    if (block.type === 'fill_blank') {
        const legacySegments: FillBlankSegment[] = [];
        const parts = block.parts ?? [''];
        const answers = block.answers ?? [];

        parts.forEach((part, index) => {
            legacySegments.push({ type: 'text', value: part ?? '' });
            if (index < answers.length) legacySegments.push({ type: 'input', value: answers[index] ?? '' });
        });

        return {
            ...block,
            segments: (block.segments?.length ? block.segments : legacySegments).map((segment) => ({
                ...segment,
                value: segment.type === 'input' ? '' : segment.value,
            })),
        };
    }

    if (block.type === 'label') {
        return {
            ...block,
            badgeText: block.badgeText ?? '1',
            badgeBgColor: block.badgeBgColor ?? '#fef3c7',
            badgeBorderColor: block.badgeBorderColor ?? '#b45309',
            badgeTextColor: block.badgeTextColor ?? '#451a03',
            badgeFontSize: block.badgeFontSize ?? 14,
            badgeSize: block.badgeSize ?? 32,
            badgeBold: block.badgeBold ?? true,
            bgColor: block.bgColor ?? '#fef3c7',
            borderColor: block.borderColor ?? '#b45309',
            textColor: block.textColor ?? '#451a03',
            rounded: block.rounded ?? 16,
            image: block.image ?? '',
            imagePosition: block.imagePosition ?? 'right',
            imageSize: block.imageSize ?? 32,
        };
    }

    if (block.type === 'image' || block.type === 'image_text') {
        return { ...block, lebar: block.lebar ?? (block.type === 'image_text' ? 33 : 100) };
    }

    if (block.type === 'input_matching') {
        return {
            ...block,
            metode: block.metode ?? 'seret',
            gunakanBackground: block.gunakanBackground ?? true,
            warnaBackground: block.warnaBackground ?? '#fffbeb',
            warnaTeks: block.warnaTeks ?? '#78350f',
            gunakanBorder: block.gunakanBorder ?? true,
            warnaBorder: block.warnaBorder ?? '#f59e0b',
            pairs: block.pairs.map((pair) => ({
                ...pair,
                lebarKiri: pair.lebarKiri ?? 100,
                lebarKanan: pair.lebarKanan ?? 100,
                modeKiri: pair.modeKiri ?? (pair.gambarKiri ? 'gambar' : 'teks'),
                modeKanan: pair.modeKanan ?? (pair.gambarKanan ? 'gambar' : 'teks'),
            })),
        };
    }

    if (block.type !== 'input_radio' && block.type !== 'input_checkbox') return { ...block };

    return {
        ...block,
        gunakanBackground: block.gunakanBackground ?? true,
        warnaBackground: block.warnaBackground ?? '#fffbeb',
        warnaTeks: block.warnaTeks ?? '#78350f',
        gunakanBorder: block.gunakanBorder ?? true,
        warnaBorder: block.warnaBorder ?? '#f59e0b',
        options: (block.options as unknown[]).map((option) => {
            if (typeof option === 'string') return { teks: option, gambar: '', lebar: 100, mode: 'teks' };

            const choice = option as Partial<ChoiceOption>;

            return {
                teks: choice.teks ?? '',
                gambar: choice.gambar ?? '',
                lebar: choice.lebar ?? 100,
                mode: choice.mode ?? (choice.gambar ? 'gambar' : 'teks'),
            };
        }) as ChoiceOption[],
    };
});

const legacyPengaturan = props.pengaturan as PengaturanLkpd & {
    tampilIdentitas?: boolean;
    posisiIdentitas?: 'atas' | 'setelah' | 'bawah';
    identitasSetelah?: number;
};

if (legacyPengaturan.tampilIdentitas && !normalizedBlocks.some((block) => block.type === 'identity')) {
    const identityBlock = { type: 'identity' as const };

    if (legacyPengaturan.posisiIdentitas === 'bawah') normalizedBlocks.push(identityBlock);
    else if (legacyPengaturan.posisiIdentitas === 'setelah') normalizedBlocks.splice(legacyPengaturan.identitasSetelah ?? 1, 0, identityBlock);
    else normalizedBlocks.unshift(identityBlock);
}

const form = useForm<{ judul: string; blocks: LkpdBlock[]; pengaturan: PengaturanLkpd; aktif: boolean }>({
    judul: props.judul,
    blocks: normalizedBlocks,
    aktif: props.aktif,
    pengaturan: {
        ...DEFAULT_PENGATURAN,
        ...(props.pengaturan ?? {}),
        headSections: props.pengaturan.headSections ?? [],
        headSelectedItems: props.pengaturan.headSelectedItems ?? {},
    },
});

const paperStyle = computed(() => ({
    width: `${F4_WIDTH_MM}mm`,
    minHeight: `${F4_HEIGHT_MM}mm`,
    paddingTop: `${form.pengaturan.marginAtas}mm`,
    paddingBottom: `${form.pengaturan.marginBawah}mm`,
    paddingLeft: `${form.pengaturan.marginKiri}mm`,
    paddingRight: `${form.pengaturan.marginKanan}mm`,
    lineHeight: form.pengaturan.jarakBaris,
}));

const mediaUploadUrl = `/kelas/${props.kelas.id}/kegiatan-pembelajaran/${props.nomor}/builder/media`;
const headTopTextEditorRef = ref<HTMLElement | null>(null);
const savedHeadTopTextRange = ref<Range | null>(null);
const showHeadStyleEditor = ref(false);

const previewWrapperRef = ref<HTMLElement | null>(null);
const pageRef = ref<HTMLElement | null>(null);
const previewScale = ref(1);
const previewNaturalWidth = ref(0);
const previewNaturalHeight = ref(0);
let resizeObserver: ResizeObserver | null = null;

function updatePreviewScale() {
    const wrapper = previewWrapperRef.value;
    const page = pageRef.value;
    if (!wrapper || !page || page.offsetWidth === 0) return;

    const wrapperStyle = getComputedStyle(wrapper);
    const horizontalPadding = parseFloat(wrapperStyle.paddingLeft) + parseFloat(wrapperStyle.paddingRight);
    const availableWidth = Math.max(0, wrapper.clientWidth - horizontalPadding);

    previewScale.value = Math.min(1, availableWidth / page.offsetWidth);
    previewNaturalWidth.value = page.offsetWidth;
    previewNaturalHeight.value = page.offsetHeight;
}

onMounted(() => {
    updatePreviewScale();
    resizeObserver = new ResizeObserver(() => updatePreviewScale());
    if (previewWrapperRef.value) resizeObserver.observe(previewWrapperRef.value);
    if (pageRef.value) resizeObserver.observe(pageRef.value);
});

function syncHeadTopTextEditor() {
    const editor = headTopTextEditorRef.value;

    if (!editor || document.activeElement === editor) return;

    editor.innerHTML = form.pengaturan.headTopText || '';
}

watch(showHeadStyleEditor, async (visible) => {
    if (!visible) return;

    await nextTick();
    syncHeadTopTextEditor();
});

onBeforeUnmount(() => resizeObserver?.disconnect());

const blockTypeOptions: { value: LkpdBlockType; icon: typeof Image }[] = [
    { value: 'identity', icon: UserRound },
    { value: 'table', icon: Columns2 },
    { value: 'paragraph', icon: Text },
    { value: 'label', icon: Type },
    { value: 'image', icon: Image },
    { value: 'image_text', icon: Columns2 },
    { value: 'video', icon: Clapperboard },
    { value: 'input_short_text', icon: Type },
    { value: 'input_long_text', icon: Type },
    { value: 'input_image', icon: Image },
    { value: 'fill_blank', icon: Type },
    { value: 'input_radio', icon: CircleDot },
    { value: 'input_checkbox', icon: CheckSquare },
    { value: 'input_matching', icon: List },
];

function addBlock(type: LkpdBlockType) {
    if (type === 'identity' && form.blocks.some((block) => block.type === 'identity')) {
        activeBlockIndex.value = form.blocks.findIndex((block) => block.type === 'identity');
        selectedHeadSection.value = null;
        activeHeadEditSection.value = null;
        showHeadStyleEditor.value = false;
        return;
    }

    form.blocks.push(createBlock(type));
    activeBlockIndex.value = form.blocks.length - 1;
    selectedHeadSection.value = null;
    activeHeadEditSection.value = null;
    showHeadStyleEditor.value = false;
}

function addTableRow(block: TableBlock) {
    block.rows.push(block.rows[0].map(() => createTableCell()));
}

function addFillSegmentAtEnd(block: Extract<LkpdBlock, { type: 'fill_blank' }>, type: FillBlankSegment['type']) {
    block.segments.push({ type, value: '' });
}

function removeFillSegment(block: Extract<LkpdBlock, { type: 'fill_blank' }>, index: number) {
    if (block.segments.length <= 1) return;
    block.segments.splice(index, 1);
}

function removeTableRow(block: TableBlock, index: number) {
    if (block.rows.length <= 1) return;
    block.rows.splice(index, 1);
}

function addTableColumn(block: TableBlock) {
    const oldCount = block.rows[0]?.length ?? 1;
    block.rows.forEach((row) => row.push(createTableCell()));
    block.columnWidths = Array.from({ length: oldCount + 1 }, () => 100 / (oldCount + 1));
}

function removeTableColumn(block: TableBlock, index: number) {
    if ((block.rows[0]?.length ?? 0) <= 1) return;
    block.rows.forEach((row) => row.splice(index, 1));
    const nextCount = block.rows[0]?.length ?? 1;
    block.columnWidths = Array.from({ length: nextCount }, () => 100 / nextCount);
}

function setTableHeaderRows(block: TableBlock, count: number) {
    block.headerRows = Math.min(Math.max(count, 0), block.rows.length);
}

function setTableCellType(cell: TableCell, type: TableCell['type']) {
    cell.type = type;
    cell.text = '';
    cell.url = '';
}

function startTableColumnResize(block: TableBlock, index: number, event: PointerEvent) {
    event.preventDefault();
    event.stopPropagation();
    const startX = event.clientX;
    const startWidth = block.columnWidths?.[index] ?? 100 / block.rows[0].length;
    const nextIndex = index + 1;
    const nextStartWidth = block.columnWidths?.[nextIndex] ?? startWidth;
    const totalWidth = (event.currentTarget as HTMLElement).closest('table')?.getBoundingClientRect().width ?? 1;

    const resize = (moveEvent: PointerEvent) => {
        const delta = ((moveEvent.clientX - startX) / totalWidth) * 100;
        const nextWidth = Math.min(startWidth + nextStartWidth - 10, Math.max(10, startWidth + delta));
        if (block.columnWidths) {
            block.columnWidths[index] = nextWidth;
            block.columnWidths[nextIndex] = startWidth + nextStartWidth - nextWidth;
        }
    };
    const stop = () => {
        window.removeEventListener('pointermove', resize);
        window.removeEventListener('pointerup', stop);
    };
    window.addEventListener('pointermove', resize);
    window.addEventListener('pointerup', stop, { once: true });
}

function startTableRowResize(block: TableBlock, index: number, event: PointerEvent) {
    event.preventDefault();
    event.stopPropagation();
    const startY = event.clientY;
    const startHeight = block.rowHeights?.[index] ?? 80;

    const resize = (moveEvent: PointerEvent) => {
        const nextHeight = Math.min(600, Math.max(40, startHeight + moveEvent.clientY - startY));
        if (block.rowHeights) block.rowHeights[index] = nextHeight;
    };
    const stop = () => {
        window.removeEventListener('pointermove', resize);
        window.removeEventListener('pointerup', stop);
    };
    window.addEventListener('pointermove', resize);
    window.addEventListener('pointerup', stop, { once: true });
}

function removeBlock(index: number) {
    form.blocks.splice(index, 1);
    if (index < activeBlockIndex.value) activeBlockIndex.value -= 1;
    if (activeBlockIndex.value >= form.blocks.length) activeBlockIndex.value = Math.max(0, form.blocks.length - 1);
}

function moveBlock(index: number, direction: -1 | 1) {
    const target = index + direction;
    if (target < 0 || target >= form.blocks.length) return;
    const [block] = form.blocks.splice(index, 1);
    form.blocks.splice(target, 0, block);
    if (activeBlockIndex.value === index) activeBlockIndex.value = target;
    else if (activeBlockIndex.value === target) activeBlockIndex.value = index;
}

function addOption(block: Extract<LkpdBlock, { options: { teks: string; gambar: string }[] }>) {
    block.options.push({ teks: '', gambar: '', lebar: 100, mode: 'teks' });
}

function setOptionMode(
    block: Extract<LkpdBlock, { options: { teks: string; gambar: string }[] }>,
    index: number,
    mode: 'teks' | 'gambar',
) {
    const option = block.options[index];
    option.mode = mode;
    if (mode === 'teks') option.gambar = '';
    else option.teks = '';
}

function updateOptionText(
    block: Extract<LkpdBlock, { options: { teks: string; gambar: string }[] }>,
    index: number,
    event: Event,
) {
    const option = block.options[index];
    option.teks = (event.target as HTMLInputElement).value;
    option.mode = 'teks';
    option.gambar = '';
}

function updateOptionImage(
    block: Extract<LkpdBlock, { options: { teks: string; gambar: string }[] }>,
    index: number,
    url: string,
) {
    block.options[index].gambar = url;
    block.options[index].mode = 'gambar';
    block.options[index].teks = '';
}

function removeOption(block: Extract<LkpdBlock, { options: { teks: string; gambar: string }[] }>, index: number) {
    if (block.options.length <= 2) return;
    block.options.splice(index, 1);
}

function addPair(block: Extract<LkpdBlock, { type: 'input_matching' }>) {
    block.pairs.push({ kiri: '', kanan: '', gambarKiri: '', gambarKanan: '', lebarKiri: 100, lebarKanan: 100, modeKiri: 'teks', modeKanan: 'teks' });
}

function setPairMode(block: Extract<LkpdBlock, { type: 'input_matching' }>, index: number, side: 'kiri' | 'kanan', mode: 'teks' | 'gambar') {
    const pair = block.pairs[index];

    if (side === 'kiri') {
        pair.modeKiri = mode;
        if (mode === 'teks') pair.gambarKiri = '';
        else pair.kiri = '';
    } else {
        pair.modeKanan = mode;
        if (mode === 'teks') pair.gambarKanan = '';
        else pair.kanan = '';
    }
}

function updatePairText(block: Extract<LkpdBlock, { type: 'input_matching' }>, index: number, side: 'kiri' | 'kanan', event: Event) {
    const pair = block.pairs[index];
    const value = (event.target as HTMLInputElement).value;

    if (side === 'kiri') {
        pair.kiri = value;
        pair.modeKiri = 'teks';
        pair.gambarKiri = '';
    } else {
        pair.kanan = value;
        pair.modeKanan = 'teks';
        pair.gambarKanan = '';
    }
}

function updatePairImage(block: Extract<LkpdBlock, { type: 'input_matching' }>, index: number, side: 'kiri' | 'kanan', url: string) {
    const pair = block.pairs[index];

    if (side === 'kiri') {
        pair.gambarKiri = url;
        pair.modeKiri = 'gambar';
        pair.kiri = '';
    } else {
        pair.gambarKanan = url;
        pair.modeKanan = 'gambar';
        pair.kanan = '';
    }
}

function removePair(block: Extract<LkpdBlock, { type: 'input_matching' }>, index: number) {
    if (block.pairs.length <= 2) return;
    block.pairs.splice(index, 1);
}

const isEmpty = computed(() => form.blocks.length === 0);
const activeBlockIndex = ref(0);
const selectedHeadSection = ref<string | null>(null);
const activeHeadEditSection = ref<DynamicHeadSectionId | null>(null);
const newFillSegmentType = ref<FillBlankSegment['type']>('text');

function handleHeaderPreviewSelection(section: string) {
    selectedHeadSection.value = section;
    if (DYNAMIC_HEAD_SECTION_OPTIONS.some((option) => option.id === section)) {
        activeHeadEditSection.value = section as DynamicHeadSectionId;
    }
    showHeadStyleEditor.value = true;
}

function parseListItems(value: string | string[] | undefined): string[] {
    if (Array.isArray(value)) {
        return value.filter((item) => typeof item === 'string' && item.trim().length > 0);
    }

    if (typeof value !== 'string' || value.trim().length === 0) {
        return [];
    }

    const doc = new DOMParser().parseFromString(value, 'text/html');
    const items = Array.from(doc.querySelectorAll('li'))
        .map((item) => item.textContent?.trim() ?? '')
        .filter((item) => item.length > 0);

    return items;
}

function getHeadSectionItems(section: DynamicHeadSectionId) {
    if (!props.identitasMapel) return [];

    switch (section) {
        case 'tujuan_pembelajaran':
            return parseListItems(props.identitasMapel.tujuan_pembelajaran);
        case 'indikator_ketercapaian':
            return parseListItems(props.identitasMapel.indikator_ketercapaian);
        case 'model_pembelajaran':
            return parseListItems(props.identitasMapel.model_pembelajaran?.map((item) => item.teks));
        case 'capaian_pembelajaran':
            return parseListItems(props.identitasMapel.capaian_pembelajaran);
        case 'alur_tujuan_pembelajaran':
            return parseListItems(props.identitasMapel.alur_tujuan_pembelajaran);
        default:
            return [];
    }
}

function getSelectedHeadItems(section: DynamicHeadSectionId): number[] {
    if (form.pengaturan.headSelectedItems && Object.prototype.hasOwnProperty.call(form.pengaturan.headSelectedItems, section)) {
        return form.pengaturan.headSelectedItems[section] ?? [];
    }

    return getHeadSectionItems(section).map((_, index) => index);
}

function isHeadSectionSelected(section: DynamicHeadSectionId) {
    return form.pengaturan.headSections?.includes(section) ?? false;
}

function isHeadListSection(section: DynamicHeadSectionId) {
    return getHeadSectionItems(section).length > 0;
}

function toggleHeadSection(section: DynamicHeadSectionId) {
    const currentSections = [...(form.pengaturan.headSections ?? [])];

    if (currentSections.includes(section)) {
        form.pengaturan.headSections = currentSections.filter((item) => item !== section);
        const nextSelectedItems = { ...(form.pengaturan.headSelectedItems ?? {}) };
        delete nextSelectedItems[section];
        form.pengaturan.headSelectedItems = nextSelectedItems;
        if (activeHeadEditSection.value === section) activeHeadEditSection.value = null;
        return;
    }

    form.pengaturan.headSections = [...currentSections, section];
    activeHeadEditSection.value = section;

    if (isHeadListSection(section)) {
        const nextSelectedItems = getHeadSectionItems(section).map((_, index) => index);
        form.pengaturan.headSelectedItems = {
            ...(form.pengaturan.headSelectedItems ?? {}),
            [section]: nextSelectedItems,
        };
    }
}

function activateHeadEditSection(section: DynamicHeadSectionId) {
    if (isHeadSectionSelected(section)) activeHeadEditSection.value = section;
}

function isHeadItemSelected(section: DynamicHeadSectionId, itemIndex: number) {
    return getSelectedHeadItems(section).includes(itemIndex);
}

function toggleHeadItem(section: DynamicHeadSectionId, itemIndex: number) {
    const current = new Set(getSelectedHeadItems(section));

    if (current.has(itemIndex)) {
        current.delete(itemIndex);
    } else {
        current.add(itemIndex);
    }

    form.pengaturan.headSelectedItems = {
        ...(form.pengaturan.headSelectedItems ?? {}),
        [section]: Array.from(current).sort((a, b) => a - b),
    };
}

function selectBlock(index: number) {
    activeBlockIndex.value = index;
    selectedHeadSection.value = null;
    activeHeadEditSection.value = null;
    showHeadStyleEditor.value = false;
}

function saveHeadTopTextSelection() {
    const selection = window.getSelection();

    if (!selection || !selection.rangeCount || !headTopTextEditorRef.value?.contains(selection.anchorNode)) return;

    savedHeadTopTextRange.value = selection.getRangeAt(0).cloneRange();
}

function applyTopTextFormat(command: 'bold' | 'italic' | 'underline' | 'fontSize', value?: string) {
    const editor = headTopTextEditorRef.value;

    if (!editor) {
        return;
    }

    editor.focus();

    const selection = window.getSelection();
    selection?.removeAllRanges();

    if (savedHeadTopTextRange.value && !savedHeadTopTextRange.value.collapsed) {
        selection?.addRange(savedHeadTopTextRange.value);
    } else if (editor.textContent?.trim()) {
        const range = document.createRange();
        range.selectNodeContents(editor);
        selection?.addRange(range);
    }

    document.execCommand(command, false, value);
    form.pengaturan.headTopText = editor.innerHTML;
    saveHeadTopTextSelection();
}

function onHeadTopTextInput(event: Event) {
    const target = event.target as HTMLElement | null;

    if (!target) {
        return;
    }

    form.pengaturan.headTopText = target.innerHTML;
}

function submit() {
    form.put(`/kelas/${props.kelas.id}/kegiatan-pembelajaran/${props.nomor}/builder`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Buat LKPD - ${form.judul}`" />

    <div class="relative min-h-screen bg-background lg:h-screen lg:overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5">
            <span class="text-lg font-extrabold tracking-wide text-foreground">E-LKPD WEB</span>
            <Link :href="`/kelas/${kelas.id}/kegiatan-pembelajaran`" class="text-sm font-semibold text-foreground hover:underline">Kembali</Link>
        </div>

        <div class="mx-auto p-5 px-6 pb-16 lg:h-[calc(100vh-76px)] lg:overflow-hidden">
            <h1 class="mb-1 text-xl font-bold text-amber-900">Buat LKPD</h1>
            <input v-model="form.judul" type="text" maxlength="255" class="mb-6 w-full max-w-2xl rounded-md border border-amber-300 bg-white px-3 py-2 text-sm font-medium text-amber-950" />
            <p v-if="form.errors.judul" class="-mt-4 mb-4 text-sm text-red-600">{{ form.errors.judul }}</p>

            <div class="grid gap-6 lg:h-full lg:grid-cols-2">
                <div class="space-y-4 pb-6 lg:min-h-0 lg:overflow-y-auto lg:pr-2 lg:pb-8">
                    <div class="space-y-3 rounded-lg border bg-white p-3 shadow-sm">
                        <p class="text-xs font-bold tracking-wide text-amber-900 uppercase">Head Dinamis</p>
                        <p class="text-xs text-muted-foreground">Pilih bagian dari data Identitas Mata Pelajaran yang ingin ditampilkan di bagian atas LKPD.</p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <label v-for="section in DYNAMIC_HEAD_SECTION_OPTIONS" :key="section.id" class="flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50/50 px-2 py-1.5 text-xs text-amber-900" @click="activateHeadEditSection(section.id)">
                                <input :checked="isHeadSectionSelected(section.id)" type="checkbox" class="accent-amber-800" @change="toggleHeadSection(section.id)" />
                                <span>{{ section.label }}</span>
                            </label>
                        </div>

                        <div v-for="section in DYNAMIC_HEAD_SECTION_OPTIONS" :key="`items-${section.id}`" class="space-y-2">
                            <div
                                v-if="activeHeadEditSection === section.id && isHeadSectionSelected(section.id) && isHeadListSection(section.id)"
                                class="rounded-md border border-amber-200 bg-amber-50/40 p-2"
                            >
                                <div class="grid gap-1.5">
                                    <label v-for="(item, itemIndex) in getHeadSectionItems(section.id)" :key="`${section.id}-${itemIndex}`" class="flex items-center gap-2 rounded border border-amber-200 bg-white/80 px-2 py-1.5 text-xs text-amber-900">
                                        <input :checked="isHeadItemSelected(section.id, itemIndex)" type="checkbox" class="accent-amber-800" @change="toggleHeadItem(section.id, itemIndex)" />
                                        <span>{{ item }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="showHeadStyleEditor" class="space-y-3 rounded-lg border bg-white p-3 shadow-sm">
                        <p class="text-xs font-bold tracking-wide text-amber-900 uppercase">Styling Header Dinamis</p>
                        <p v-if="selectedHeadSection" class="text-[10px] font-semibold uppercase tracking-wide text-amber-700">
                            Header aktif: {{ selectedHeadSection }}
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="text-xs text-muted-foreground sm:col-span-2">
                                Teks paling atas
                                <div class="mt-1 flex items-center gap-1 rounded-md border border-amber-300 bg-amber-50/40 px-1 py-1">
                                    <button type="button" class="rounded px-2 py-1 text-xs font-bold text-amber-900 hover:bg-amber-100" @mousedown="saveHeadTopTextSelection" @click.prevent="applyTopTextFormat('bold')">B</button>
                                    <button type="button" class="rounded px-2 py-1 text-xs italic text-amber-900 hover:bg-amber-100" @mousedown="saveHeadTopTextSelection" @click.prevent="applyTopTextFormat('italic')">I</button>
                                    <button type="button" class="rounded px-2 py-1 text-xs underline text-amber-900 hover:bg-amber-100" @mousedown="saveHeadTopTextSelection" @click.prevent="applyTopTextFormat('underline')">U</button>
                                    <select class="rounded border border-amber-300 px-2 py-1 text-xs text-amber-900" title="Ukuran huruf" @mousedown="saveHeadTopTextSelection" @change="applyTopTextFormat('fontSize', ($event.target as HTMLSelectElement).value)">
                                        <option value="">Ukuran</option>
                                        <option value="1">12 px</option>
                                        <option value="2">14 px</option>
                                        <option value="3">16 px</option>
                                        <option value="4">18 px</option>
                                        <option value="5">24 px</option>
                                        <option value="6">32 px</option>
                                    </select>
                                </div>
                                <div
                                    ref="headTopTextEditorRef"
                                    contenteditable="true"
                                    class="mt-1 min-h-[80px] w-full rounded-md border border-amber-300 bg-white px-2 py-1.5 text-sm text-amber-950 outline-none"
                                    :style="{
                                        whiteSpace: 'pre-line',
                                        color: form.pengaturan.headTopTextColor,
                                        fontSize: `${form.pengaturan.headTopTextFontSize ?? 12}px`,
                                        fontWeight: form.pengaturan.headTopTextFontWeight ?? '400',
                                    }"
                                    @input="onHeadTopTextInput"
                                ></div>
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Teks atas warna
                                <input v-model="form.pengaturan.headTopTextColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Teks atas bg
                                <input v-model="form.pengaturan.headTopTextBgColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Teks atas border
                                <input v-model="form.pengaturan.headTopTextBorderColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ukuran teks atas (px)
                                <input v-model.number="form.pengaturan.headTopTextFontSize" type="number" min="8" max="64" step="1" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Posisi teks atas
                                <div class="mt-1 flex items-center gap-1">
                                    <button type="button" title="Rata kiri" aria-label="Rata kiri" class="rounded p-1.5 hover:bg-amber-100" :class="form.pengaturan.headTopTextAlign === 'left' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="form.pengaturan.headTopTextAlign = 'left'"><AlignLeft class="size-4" /></button>
                                    <button type="button" title="Rata tengah" aria-label="Rata tengah" class="rounded p-1.5 hover:bg-amber-100" :class="form.pengaturan.headTopTextAlign === 'center' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="form.pengaturan.headTopTextAlign = 'center'"><AlignCenter class="size-4" /></button>
                                    <button type="button" title="Rata kanan" aria-label="Rata kanan" class="rounded p-1.5 hover:bg-amber-100" :class="form.pengaturan.headTopTextAlign === 'right' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="form.pengaturan.headTopTextAlign = 'right'"><AlignRight class="size-4" /></button>
                                    <button type="button" title="Rata kiri-kanan" aria-label="Rata kiri-kanan" class="rounded p-1.5 hover:bg-amber-100" :class="form.pengaturan.headTopTextAlign === 'justify' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="form.pengaturan.headTopTextAlign = 'justify'"><AlignJustify class="size-4" /></button>
                                </div>
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ketebalan teks atas
                                <select v-model="form.pengaturan.headTopTextFontWeight" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                    <option value="600">600</option>
                                    <option value="700">700</option>
                                    <option value="800">800</option>
                                    <option value="900">900</option>
                                </select>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-muted-foreground">
                                <input v-model="form.pengaturan.headShowLetter" type="checkbox" class="accent-amber-800" />
                                Tampilkan letter
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Letter
                                <input v-model="form.pengaturan.headLetter" type="text" maxlength="2" :disabled="!form.pengaturan.headShowLetter" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm disabled:bg-muted/50" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Judul warna
                                <input v-model="form.pengaturan.headTitleColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Judul bg
                                <input v-model="form.pengaturan.headTitleBgColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Judul border
                                <input v-model="form.pengaturan.headTitleBorderColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ukuran judul
                                <input v-model="form.pengaturan.headTitleFontSize" type="text" placeholder="0.875rem" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ketebalan judul
                                <select v-model="form.pengaturan.headTitleFontWeight" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                    <option value="600">600</option>
                                    <option value="700">700</option>
                                    <option value="800">800</option>
                                    <option value="900">900</option>
                                </select>
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Letter warna
                                <input v-model="form.pengaturan.headLetterColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Letter bg
                                <input v-model="form.pengaturan.headLetterBgColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                Letter border
                                <input v-model="form.pengaturan.headLetterBorderColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ukuran letter
                                <input v-model="form.pengaturan.headLetterFontSize" type="text" placeholder="0.875rem" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Ketebalan letter
                                <select v-model="form.pengaturan.headLetterFontWeight" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                    <option value="600">600</option>
                                    <option value="700">700</option>
                                    <option value="800">800</option>
                                    <option value="900">900</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-3 rounded-lg border bg-white p-3 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-xs font-bold tracking-wide text-amber-900 uppercase">Pengaturan Halaman (Kertas F4)</p>
                            <label class="flex items-center gap-2 text-xs font-semibold text-amber-900">
                                <input v-model="form.aktif" type="checkbox" class="accent-amber-800" />
                                LKPD aktif untuk siswa
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            <label class="text-xs text-muted-foreground">
                                Margin Atas (mm)
                                <input v-model.number="form.pengaturan.marginAtas" type="number" min="0" max="50" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Margin Bawah (mm)
                                <input v-model.number="form.pengaturan.marginBawah" type="number" min="0" max="50" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Margin Kiri (mm)
                                <input v-model.number="form.pengaturan.marginKiri" type="number" min="0" max="50" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                            <label class="text-xs text-muted-foreground">
                                Margin Kanan (mm)
                                <input v-model.number="form.pengaturan.marginKanan" type="number" min="0" max="50" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm" />
                            </label>
                        </div>
                        <label class="block text-xs text-muted-foreground">
                            Jarak Baris
                            <select v-model.number="form.pengaturan.jarakBaris" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                <option :value="1">Rapat (1)</option>
                                <option :value="1.15">Normal (1.15)</option>
                                <option :value="1.5">Renggang (1.5)</option>
                                <option :value="2">Sangat Renggang (2)</option>
                            </select>
                        </label>
                        <label class="block text-xs text-muted-foreground">
                            Jenis Pengerjaan
                            <select v-model="form.pengaturan.jenisPengerjaan" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                <option value="individu">Individu</option>
                                <option value="kelompok">Kelompok</option>
                            </select>
                        </label>
                    </div>

                    <div class="flex flex-wrap gap-2 rounded-lg border bg-white p-3 shadow-sm">
                        <button
                            v-for="opt in blockTypeOptions"
                            :key="opt.value"
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-md border border-amber-300 px-2.5 py-1.5 text-xs font-medium text-amber-900 hover:bg-amber-50"
                            @click="addBlock(opt.value)"
                        >
                            <Plus class="size-3.5" />
                            {{ BLOCK_TYPE_LABELS[opt.value] }}
                        </button>
                    </div>

                    <p v-if="isEmpty" class="rounded-md border border-dashed border-amber-300 p-6 text-center text-sm text-muted-foreground">
                        Belum ada isi. Tambahkan blok dari tombol di atas.
                    </p>

                    <template v-for="(block, index) in form.blocks" :key="index">
                        <div v-if="index === activeBlockIndex" class="rounded-lg border bg-white p-4 shadow-sm ring-2 ring-amber-300">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-900">
                                <GripVertical class="size-3.5 text-muted-foreground" />
                                {{ BLOCK_TYPE_LABELS[block.type] }}
                            </span>
                            <div class="flex items-center gap-1">
                                <button type="button" class="rounded p-1 hover:bg-amber-50" :disabled="index === 0" @click="moveBlock(index, -1)">
                                    <ArrowUp class="size-4 text-amber-800" />
                                </button>
                                <button type="button" class="rounded p-1 hover:bg-amber-50" :disabled="index === form.blocks.length - 1" @click="moveBlock(index, 1)">
                                    <ArrowDown class="size-4 text-amber-800" />
                                </button>
                                <button type="button" class="rounded p-1 hover:bg-red-50" @click="removeBlock(index)">
                                    <Trash2 class="size-4 text-red-600" />
                                </button>
                            </div>
                        </div>

                        <!-- Identity -->
                        <div v-if="block.type === 'identity'" class="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                            Identitas siswa akan ditampilkan pada posisi ini di LKPD.
                        </div>

                        <!-- Fill in the blanks -->
                        <div v-else-if="block.type === 'fill_blank'" class="space-y-3">
                            <p class="text-xs text-muted-foreground">Tambah segmen sesuai urutan kalimat. Setiap segmen bisa berupa teks biasa atau input siswa.</p>
                            <div class="space-y-2 rounded-md border border-amber-200 bg-amber-50/40 p-3">
                                <div v-for="(segment, segmentIndex) in block.segments" :key="segmentIndex" class="flex items-center gap-2 rounded-md border border-amber-200 bg-white p-2">
                                    <span class="w-6 shrink-0 text-center text-xs font-semibold text-amber-700">{{ segmentIndex + 1 }}</span>
                                    <select v-model="segment.type" class="w-32 shrink-0 rounded border border-amber-300 px-2 py-1.5 text-xs" @change="segment.value = ''">
                                        <option value="text">Teks</option>
                                        <option value="input">Input siswa</option>
                                    </select>
                                    <input v-if="segment.type === 'text'" v-model="segment.value" type="text" placeholder="Tulis teks di sini" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                    <span v-else class="w-full rounded-md border border-dashed border-amber-400 bg-amber-50 px-3 py-2 text-sm text-amber-800">Input yang akan diisi siswa</span>
                                    <button type="button" class="shrink-0 rounded p-1 text-red-600 hover:bg-red-50" title="Hapus segmen" :disabled="block.segments.length <= 1" @click="removeFillSegment(block, segmentIndex)">×</button>
                                </div>
                                <div class="flex items-center justify-end gap-2 border-t border-amber-200 pt-3">
                                    <select v-model="newFillSegmentType" class="rounded border border-amber-300 px-2 py-1.5 text-xs text-amber-900">
                                        <option value="text">Teks</option>
                                        <option value="input">Input siswa</option>
                                    </select>
                                    <button type="button" class="rounded border border-amber-300 px-3 py-1.5 text-xs font-medium text-amber-900 hover:bg-amber-50" @click="addFillSegmentAtEnd(block, newFillSegmentType)">+ Tambah</button>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div v-else-if="block.type === 'table'" class="space-y-3">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="rounded border border-amber-300 px-2 py-1 text-xs text-amber-900 hover:bg-amber-50" @click="addTableRow(block)">+ Baris</button>
                                <button type="button" class="rounded border border-amber-300 px-2 py-1 text-xs text-amber-900 hover:bg-amber-50" @click="addTableColumn(block)">+ Kolom</button>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Header atas
                                    <select :value="block.headerRows ?? 0" class="rounded border border-amber-300 px-2 py-1 text-xs" @change="setTableHeaderRows(block, Number(($event.target as HTMLSelectElement).value))">
                                        <option :value="0">Tidak ada</option>
                                        <option v-for="rowIndex in block.rows.length" :key="rowIndex" :value="rowIndex">{{ rowIndex }} baris</option>
                                    </select>
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Warna
                                    <input v-model="block.headerColor" type="color" class="size-7 cursor-pointer rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Teks header
                                    <input v-model="block.headerTextColor" type="color" class="size-7 cursor-pointer rounded border border-amber-300 p-0.5" />
                                </label>
                            </div>
                            <div class="overflow-x-auto rounded border border-amber-200">
                                <table class="w-full min-w-[600px] table-fixed border-collapse text-sm">
                                    <colgroup>
                                        <col v-for="(width, columnIndex) in block.columnWidths" :key="columnIndex" :style="{ width: `${width}%` }" />
                                        <col class="w-8" />
                                    </colgroup>
                                    <tbody>
                                        <tr v-for="(row, ri) in block.rows" :key="ri" class="border-b border-amber-200 last:border-b-0" :style="{ height: `${block.rowHeights?.[ri] ?? 80}px`, backgroundColor: ri < (block.headerRows ?? 0) ? block.headerColor : undefined, color: ri < (block.headerRows ?? 0) ? block.headerTextColor : undefined }">
                                            <td v-for="(cell, ci) in row" :key="ci" class="relative min-w-0 border-r border-amber-200 p-2 align-top last:border-r-0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <select v-model="cell.type" class="w-full rounded border border-amber-300 px-2 py-1 text-xs" @change="setTableCellType(cell, cell.type)">
                                                        <option value="paragraph">Paragraf</option>
                                                        <option value="image">Gambar</option>
                                                        <option value="input_short_text">Jawaban singkat</option>
                                                        <option value="input_long_text">Jawaban panjang</option>
                                                    </select>
                                                    <button type="button" class="text-xs text-red-600" title="Hapus kolom" @click="removeTableColumn(block, ci)">×</button>
                                                </div>
                                                <RichTextEditor v-if="cell.type === 'paragraph'" v-model="cell.text" placeholder="Isi cell..." />
                                                <ImageUploadField v-else-if="cell.type === 'image'" v-model="cell.url" :upload-url="mediaUploadUrl" />
                                                <input v-else-if="cell.type === 'input_short_text'" v-model="cell.text" type="text" placeholder="Jawaban singkat" class="w-full rounded border border-amber-300 px-2 py-1.5 text-sm" />
                                                <textarea v-else v-model="cell.text" rows="3" placeholder="Jawaban panjang" class="w-full rounded border border-amber-300 px-2 py-1.5 text-sm" />
                                                <span v-if="ci < row.length - 1" class="absolute inset-y-0 -right-2 z-10 w-4 cursor-col-resize touch-none hover:bg-amber-300/60" title="Seret untuk mengubah lebar kolom" @pointerdown.stop="startTableColumnResize(block, ci, $event)" />
                                                <span class="absolute inset-x-0 -bottom-2 z-10 h-4 cursor-row-resize touch-none hover:bg-amber-300/60" title="Seret untuk mengubah tinggi baris" @pointerdown.stop="startTableRowResize(block, ri, $event)" />
                                            </td>
                                            <td class="w-8 p-1 align-top"><button type="button" class="text-xs text-red-600" title="Hapus baris" @click="removeTableRow(block, ri)">×</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Paragraph -->
                        <div v-else-if="block.type === 'paragraph'" class="space-y-2">
                            <RichTextEditor v-model="block.text" placeholder="Tulis paragraf..." />
                            <div class="flex items-center gap-1">
                                <button type="button" title="Rata kiri" aria-label="Rata kiri" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'left' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'left'"><AlignLeft class="size-4" /></button>
                                <button type="button" title="Rata tengah" aria-label="Rata tengah" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'center' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'center'"><AlignCenter class="size-4" /></button>
                                <button type="button" title="Rata kanan" aria-label="Rata kanan" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'right' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'right'"><AlignRight class="size-4" /></button>
                                <button type="button" title="Rata kiri-kanan" aria-label="Rata kiri-kanan" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'justify' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'justify'"><AlignJustify class="size-4" /></button>
                            </div>
                            <label class="block text-xs text-muted-foreground">
                                Jarak antar baris: {{ block.jarakBaris ?? 1.5 }}
                                <input v-model.number="block.jarakBaris" type="range" min="1" max="3" step="0.05" class="mt-1 w-full accent-amber-800" />
                            </label>
                            <label class="flex items-center gap-1 text-xs text-muted-foreground">Warna teks <input v-model="block.warnaTeks" type="color" class="size-6 rounded border border-amber-300" /></label>
                        </div>

                        <!-- Label -->
                        <div v-else-if="block.type === 'label'" class="space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="text-xs text-muted-foreground">Badge</label>
                                <input v-model="block.badgeText" type="text" placeholder="1 / A / B" class="w-24 rounded-md border border-amber-300 px-3 py-2 text-sm" />
                                <label class="w-full text-xs text-muted-foreground">Teks</label>
                                <input v-model="block.text" type="text" placeholder="Label" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            </div>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Background
                                    <input v-model="block.bgColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Border
                                    <input v-model="block.borderColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Teks
                                    <input v-model="block.textColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Radius
                                    <input v-model.number="block.rounded" type="number" min="0" max="50" class="w-full rounded-md border border-amber-300 px-2 py-1 text-sm" />
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Badge bg
                                    <input v-model="block.badgeBgColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Badge border
                                    <input v-model="block.badgeBorderColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Badge teks
                                    <input v-model="block.badgeTextColor" type="color" class="size-7 rounded border border-amber-300 p-0.5" />
                                </label>
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    Ukuran badge
                                    <input v-model.number="block.badgeFontSize" type="number" min="10" max="30" class="w-full rounded-md border border-amber-300 px-2 py-1 text-sm" />
                                </label>
                            </div>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <label class="block text-xs text-muted-foreground">
                                    Bulat badge: {{ block.badgeSize ?? 32 }} px
                                    <input v-model.number="block.badgeSize" type="range" min="20" max="80" step="1" class="mt-1 w-full accent-amber-800" />
                                </label>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center gap-1 text-xs text-muted-foreground">
                                    <input v-model="block.badgeBold" type="checkbox" class="accent-amber-800" /> Tebal badge
                                </label>
                                <label class="text-xs text-muted-foreground">
                                    Posisi gambar
                                    <select v-model="block.imagePosition" class="mt-1 rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                        <option value="left">Kiri</option>
                                        <option value="right">Kanan</option>
                                    </select>
                                </label>
                            </div>
                            <div class="space-y-2">
                                <ImageUploadField v-model="block.image" :upload-url="mediaUploadUrl" />
                                <label class="block text-xs text-muted-foreground">
                                    Ukuran gambar: {{ block.imageSize ?? 32 }} px
                                    <input v-model.number="block.imageSize" type="range" min="16" max="96" step="1" class="mt-1 w-full accent-amber-800" />
                                </label>
                            </div>
                        </div>

                        <!-- Image -->
                        <div v-else-if="block.type === 'image'" class="space-y-2">
                            <ImageUploadField v-model="block.url" :upload-url="mediaUploadUrl" />
                            <label class="block text-xs text-muted-foreground">Ukuran gambar: {{ block.lebar ?? 100 }}%
                                <input v-model.number="block.lebar" type="range" min="10" max="100" step="1" class="mt-1 w-full accent-amber-800" />
                            </label>
                            <input v-model="block.caption" type="text" placeholder="Keterangan gambar (opsional)" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            <select v-model="block.position" class="rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                <option value="left">Kiri</option>
                                <option value="center">Tengah</option>
                                <option value="right">Kanan</option>
                                <option value="full">Selebar Konten</option>
                            </select>
                        </div>

                        <!-- Image + text side by side -->
                        <div v-else-if="block.type === 'image_text'" class="space-y-2">
                            <ImageUploadField v-model="block.url" :upload-url="mediaUploadUrl" />
                            <label class="block text-xs text-muted-foreground">Ukuran gambar: {{ block.lebar ?? 33 }}%
                                <input v-model.number="block.lebar" type="range" min="10" max="100" step="1" class="mt-1 w-full accent-amber-800" />
                            </label>
                            <input v-model="block.caption" type="text" placeholder="Keterangan gambar (opsional)" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            <select v-model="block.position" class="rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                <option value="left">Gambar di Kiri</option>
                                <option value="right">Gambar di Kanan</option>
                            </select>
                            <RichTextEditor v-model="block.text" placeholder="Tulis teks di samping gambar..." />
                            <div class="flex items-center gap-1">
                                <button type="button" title="Rata kiri" aria-label="Rata kiri" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'left' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'left'"><AlignLeft class="size-4" /></button>
                                <button type="button" title="Rata tengah" aria-label="Rata tengah" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'center' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'center'"><AlignCenter class="size-4" /></button>
                                <button type="button" title="Rata kanan" aria-label="Rata kanan" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'right' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'right'"><AlignRight class="size-4" /></button>
                                <button type="button" title="Rata kiri-kanan" aria-label="Rata kiri-kanan" class="rounded p-1.5 hover:bg-amber-100" :class="block.align === 'justify' ? 'bg-amber-100 text-amber-900' : 'text-muted-foreground'" @click="block.align = 'justify'"><AlignJustify class="size-4" /></button>
                            </div>
                        </div>

                        <!-- Video -->
                        <div v-else-if="block.type === 'video'" class="space-y-2">
                            <input v-model="block.url" type="text" placeholder="Tempel link YouTube (https://www.youtube.com/watch?v=...)" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                            <input v-model="block.caption" type="text" placeholder="Keterangan video (opsional)" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" />
                        </div>

                        <!-- Short / Long text -->
                        <div v-else-if="block.type === 'input_short_text' || block.type === 'input_long_text' || block.type === 'input_image'" class="space-y-2">
                            <RichTextEditor v-model="block.label" placeholder="Tulis pertanyaan..." />
                        </div>

                        <!-- Radio / Checkbox -->
                        <div v-else-if="block.type === 'input_radio' || block.type === 'input_checkbox'" class="space-y-2">
                            <RichTextEditor v-model="block.label" placeholder="Tulis pertanyaan..." />
                            <div class="flex flex-wrap items-center gap-3 rounded-md border border-amber-200 bg-amber-50/50 p-2 text-xs text-muted-foreground">
                                <label class="flex items-center gap-1"><input v-model="block.gunakanBackground" type="checkbox" class="accent-amber-800" /> Gunakan background</label>
                                <label class="flex items-center gap-1">Background <input v-model="block.warnaBackground" type="color" class="size-6 rounded border border-amber-300" :disabled="!block.gunakanBackground" /></label>
                                <label class="flex items-center gap-1">Teks <input v-model="block.warnaTeks" type="color" class="size-6 rounded border border-amber-300" /></label>
                                <label class="flex items-center gap-1"><input v-model="block.gunakanBorder" type="checkbox" class="accent-amber-800" /> Border</label>
                                <label class="flex items-center gap-1">Warna border <input v-model="block.warnaBorder" type="color" class="size-6 rounded border border-amber-300" :disabled="!block.gunakanBorder" /></label>
                            </div>
                            <div v-for="(_, oi) in block.options" :key="oi" class="flex items-center gap-2">
                                <div class="min-w-0 flex-1 space-y-2">
                                    <div class="flex gap-1">
                                        <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.options[oi].mode !== 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setOptionMode(block, oi, 'teks')">Teks</button>
                                        <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.options[oi].mode === 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setOptionMode(block, oi, 'gambar')">Gambar</button>
                                    </div>
                                    <input v-if="block.options[oi].mode !== 'gambar'" :value="block.options[oi].teks" type="text" :placeholder="`Opsi ${oi + 1}`" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" @input="updateOptionText(block, oi, $event)" />
                                    <template v-else>
                                        <ImageUploadField :model-value="block.options[oi].gambar" :upload-url="mediaUploadUrl" @update:model-value="updateOptionImage(block, oi, $event)" />
                                        <label class="block text-xs text-muted-foreground">Ukuran gambar: {{ block.options[oi].lebar ?? 100 }}%
                                            <input v-model.number="block.options[oi].lebar" type="range" min="10" max="100" step="1" class="mt-1 w-full accent-amber-800" />
                                        </label>
                                    </template>
                                </div>
                                <button type="button" class="shrink-0 rounded p-1 hover:bg-red-50" :disabled="block.options.length <= 2" @click="removeOption(block, oi)">
                                    <Trash2 class="size-4 text-red-600" />
                                </button>
                            </div>
                            <button type="button" class="text-xs font-medium text-amber-800 hover:underline" @click="addOption(block)">+ Tambah opsi</button>
                        </div>

                        <!-- Matching -->
                        <div v-else-if="block.type === 'input_matching'" class="space-y-2">
                            <RichTextEditor v-model="block.label" placeholder="Tulis pertanyaan..." />
                            <div class="flex flex-wrap items-center gap-3 rounded-md border border-amber-200 bg-amber-50/50 p-2 text-xs text-muted-foreground">
                                <label class="flex items-center gap-1"><input v-model="block.gunakanBackground" type="checkbox" class="accent-amber-800" /> Gunakan background</label>
                                <label class="flex items-center gap-1">Background <input v-model="block.warnaBackground" type="color" class="size-6 rounded border border-amber-300" :disabled="!block.gunakanBackground" /></label>
                                <label class="flex items-center gap-1">Teks <input v-model="block.warnaTeks" type="color" class="size-6 rounded border border-amber-300" /></label>
                                <label class="flex items-center gap-1"><input v-model="block.gunakanBorder" type="checkbox" class="accent-amber-800" /> Border</label>
                                <label class="flex items-center gap-1">Warna border <input v-model="block.warnaBorder" type="color" class="size-6 rounded border border-amber-300" :disabled="!block.gunakanBorder" /></label>
                            </div>
                            <label class="block text-xs text-muted-foreground">
                                Cara menjawab
                                <select v-model="block.metode" class="mt-1 w-full rounded-md border border-amber-300 px-2 py-1.5 text-sm">
                                    <option value="seret">Seret item ke pasangan</option>
                                    <option value="tarik_garis">Tarik garis ke pasangan</option>
                                </select>
                            </label>
                            <div v-for="(_, pi) in block.pairs" :key="pi" class="space-y-2 rounded-md border border-amber-200 p-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-full space-y-1">
                                        <div class="flex gap-1">
                                            <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.pairs[pi].modeKiri !== 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setPairMode(block, pi, 'kiri', 'teks')">Teks kiri</button>
                                            <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.pairs[pi].modeKiri === 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setPairMode(block, pi, 'kiri', 'gambar')">Gambar kiri</button>
                                        </div>
                                        <input v-if="block.pairs[pi].modeKiri !== 'gambar'" :value="block.pairs[pi].kiri" type="text" placeholder="Item" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" @input="updatePairText(block, pi, 'kiri', $event)" />
                                    </div>
                                    <div class="w-full space-y-1">
                                        <div class="flex gap-1">
                                            <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.pairs[pi].modeKanan !== 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setPairMode(block, pi, 'kanan', 'teks')">Teks kanan</button>
                                            <button type="button" class="rounded border px-2 py-1 text-xs" :class="block.pairs[pi].modeKanan === 'gambar' ? 'border-amber-800 bg-amber-800 text-white' : 'border-amber-300 text-amber-900'" @click="setPairMode(block, pi, 'kanan', 'gambar')">Gambar kanan</button>
                                        </div>
                                        <input v-if="block.pairs[pi].modeKanan !== 'gambar'" :value="block.pairs[pi].kanan" type="text" placeholder="Pasangan" class="w-full rounded-md border border-amber-300 px-3 py-2 text-sm" @input="updatePairText(block, pi, 'kanan', $event)" />
                                    </div>
                                    <button type="button" class="shrink-0 rounded p-1 hover:bg-red-50" :disabled="block.pairs.length <= 2" @click="removePair(block, pi)">
                                        <Trash2 class="size-4 text-red-600" />
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <ImageUploadField v-if="block.pairs[pi].modeKiri === 'gambar'" :model-value="block.pairs[pi].gambarKiri ?? ''" :upload-url="mediaUploadUrl" @update:model-value="updatePairImage(block, pi, 'kiri', $event)" />
                                        <label v-if="block.pairs[pi].modeKiri === 'gambar'" class="mt-2 block text-xs text-muted-foreground">Ukuran kiri: {{ block.pairs[pi].lebarKiri ?? 100 }}%
                                            <input v-model.number="block.pairs[pi].lebarKiri" type="range" min="10" max="100" step="1" class="mt-1 w-full accent-amber-800" />
                                        </label>
                                    </div>
                                    <div>
                                        <ImageUploadField v-if="block.pairs[pi].modeKanan === 'gambar'" :model-value="block.pairs[pi].gambarKanan ?? ''" :upload-url="mediaUploadUrl" @update:model-value="updatePairImage(block, pi, 'kanan', $event)" />
                                        <label v-if="block.pairs[pi].modeKanan === 'gambar'" class="mt-2 block text-xs text-muted-foreground">Ukuran kanan: {{ block.pairs[pi].lebarKanan ?? 100 }}%
                                            <input v-model.number="block.pairs[pi].lebarKanan" type="range" min="10" max="100" step="1" class="mt-1 w-full accent-amber-800" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="text-xs font-medium text-amber-800 hover:underline" @click="addPair(block)">+ Tambah pasangan</button>
                        </div>
                        </div>
                    </template>

                    <div class="flex justify-end">
                        <p v-if="Object.keys(form.errors).length" class="mr-auto text-sm text-red-600">
                            Periksa kembali isi blok yang ditandai sebelum menyimpan.
                        </p>
                        <button type="button" class="rounded-md bg-amber-800 px-5 py-2 text-sm font-medium text-white disabled:opacity-60" :disabled="form.processing" @click="submit">
                            Simpan LKPD
                        </button>
                    </div>
                </div>

                <div class="lg:min-h-0 lg:self-stretch">
                    <p class="mb-2 text-xs font-bold tracking-wide text-muted-foreground uppercase">Pratinjau (Ukuran Kertas F4)</p>
                    <div ref="previewWrapperRef" class="h-[calc(100%-24px)] overflow-y-auto overflow-x-hidden rounded-lg border bg-neutral-200 p-4">
                        <div
                            class="mx-auto"
                            :style="{ width: `${previewNaturalWidth * previewScale}px`, height: `${previewNaturalHeight * previewScale}px` }"
                        >
                            <div
                                ref="pageRef"
                                class="lkpd-page flex flex-col bg-white shadow"
                                :style="{ ...paperStyle, transform: `scale(${previewScale})`, transformOrigin: 'top left' }"
                            >
                                <div class="flow-root space-y-4">
                                    <DynamicHeadContent
                                        v-if="form.pengaturan.headSections?.length"
                                        :identitas-mapel="props.identitasMapel ?? null"
                                        :head-sections="form.pengaturan.headSections"
                                        :head-selected-items="form.pengaturan.headSelectedItems ?? {}"
                                        :head-style="form.pengaturan"
                                        @header-selected="handleHeaderPreviewSelection"
                                    />

                                    <div
                                        v-for="(block, index) in form.blocks"
                                        :key="index"
                                        class="cursor-pointer rounded-md transition-shadow hover:ring-2 hover:ring-amber-300"
                                        :class="activeBlockIndex === index ? 'ring-2 ring-amber-800' : ''"
                                        @click="selectBlock(index)"
                                    >
                                        <IdentityPreview
                                            v-if="block.type === 'identity'"
                                            :jenis-pengerjaan="form.pengaturan.jenisPengerjaan"
                                        />
                                        <LkpdBlockPreview v-else :block="block" />
                                    </div>
                                </div>
                                <p v-if="isEmpty" class="text-center text-sm text-muted-foreground">Belum ada isi untuk ditampilkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.lkpd-page {
    box-sizing: border-box;
    border: 1px solid #d4d4d4;
}

@media print {
    @page {
        size: 215mm 330mm;
        margin: 0;
    }

    .lkpd-page {
        box-shadow: none;
    }
}
</style>
