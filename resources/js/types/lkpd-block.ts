export type ImageBlock = {
    type: 'image';
    url: string;
    position: 'left' | 'right' | 'center' | 'full';
    caption: string;
    lebar?: number;
};

export type ParagraphBlock = {
    type: 'paragraph';
    text: string;
    align: 'left' | 'center' | 'right' | 'justify';
    jarakBaris?: number;
    warnaTeks?: string;
};

export type LabelBlock = {
    type: 'label';
    text: string;
    badgeText?: string;
    badgeBgColor?: string;
    badgeBorderColor?: string;
    badgeTextColor?: string;
    badgeFontSize?: number;
    badgeSize?: number;
    badgeBold?: boolean;
    bgColor?: string;
    borderColor?: string;
    textColor?: string;
    rounded?: number;
    image: string;
    imagePosition: 'left' | 'right';
    imageSize?: number;
};

export type ImageTextBlock = {
    type: 'image_text';
    url: string;
    caption: string;
    position: 'left' | 'right';
    text: string;
    align: 'left' | 'center' | 'right' | 'justify';
    lebar?: number;
};

export type ShortTextBlock = {
    type: 'input_short_text';
    label: string;
};

export type LongTextBlock = {
    type: 'input_long_text';
    label: string;
};

export type UploadImageBlock = {
    type: 'input_image';
    label: string;
};

export type FillBlankBlock = {
    type: 'fill_blank';
    segments: FillBlankSegment[];
    parts: string[];
    answers: string[];
};

export type FillBlankSegment = {
    type: 'text' | 'input';
    value: string;
};

export type ChoiceOption = {
    teks: string;
    gambar: string;
    lebar?: number;
    mode?: 'teks' | 'gambar';
};

export type QuestionStyle = {
    gunakanBackground?: boolean;
    warnaBackground?: string;
    warnaTeks?: string;
    gunakanBorder?: boolean;
    warnaBorder?: string;
};

export type RadioBlock = {
    type: 'input_radio';
    label: string;
    options: ChoiceOption[];
    gunakanBackground?: boolean;
    warnaBackground?: string;
    warnaTeks?: string;
    gunakanBorder?: boolean;
    warnaBorder?: string;
};

export type CheckboxBlock = {
    type: 'input_checkbox';
    label: string;
    options: ChoiceOption[];
    gunakanBackground?: boolean;
    warnaBackground?: string;
    warnaTeks?: string;
    gunakanBorder?: boolean;
    warnaBorder?: string;
};

export type MatchingPair = {
    kiri: string;
    kanan: string;
    gambarKiri?: string;
    gambarKanan?: string;
    lebarKiri?: number;
    lebarKanan?: number;
    modeKiri?: 'teks' | 'gambar';
    modeKanan?: 'teks' | 'gambar';
};

export type MatchingBlock = {
    type: 'input_matching';
    label: string;
    pairs: MatchingPair[];
    metode?: 'seret' | 'tarik_garis';
    gunakanBackground?: boolean;
    warnaBackground?: string;
    warnaTeks?: string;
    gunakanBorder?: boolean;
    warnaBorder?: string;
};

export type VideoBlock = {
    type: 'video';
    url: string;
    caption: string;
};

export type IdentityBlock = {
    type: 'identity';
};

export type TableCellType = 'paragraph' | 'image' | 'input_short_text' | 'input_long_text';

export type TableCell = {
    type: TableCellType;
    text: string;
    url: string;
    lebar?: number;
    jarakBaris?: number;
};

export type TableBlock = {
    type: 'table';
    rows: TableCell[][];
    columnWidths?: number[];
    rowHeights?: number[];
    headerRows?: number;
    headerColor?: string;
    headerTextColor?: string;
};

export type LkpdBlock =
    | ImageBlock
    | ImageTextBlock
    | VideoBlock
    | ParagraphBlock
    | LabelBlock
    | ShortTextBlock
    | LongTextBlock
    | UploadImageBlock
    | FillBlankBlock
    | RadioBlock
    | CheckboxBlock
    | MatchingBlock
    | IdentityBlock
    | TableBlock;

export type LkpdBlockType = LkpdBlock['type'];

export const BLOCK_TYPE_LABELS: Record<LkpdBlockType, string> = {
    image: 'Gambar',
    image_text: 'Gambar + Teks Berdampingan',
    video: 'Video (YouTube)',
    paragraph: 'Paragraf',
    label: 'Label',
    input_short_text: 'Jawaban Teks Singkat',
    input_long_text: 'Jawaban Teks Panjang',
    input_image: 'Jawaban Upload Gambar',
    fill_blank: 'Melengkapi Kata',
    input_radio: 'Pilihan Ganda (Satu Jawaban)',
    input_checkbox: 'Pilih Lebih dari Satu',
    input_matching: 'Menjodohkan',
    identity: 'Identitas Siswa',
    table: 'Tabel',
};

export function createBlock(type: LkpdBlockType): LkpdBlock {
    switch (type) {
        case 'image':
            return { type, url: '', position: 'center', caption: '', lebar: 100 };
        case 'image_text':
            return { type, url: '', caption: '', position: 'left', text: '', align: 'justify', lebar: 33 };
        case 'video':
            return { type, url: '', caption: '' };
        case 'paragraph':
            return { type, text: '', align: 'justify', jarakBaris: 1.5, warnaTeks: '#451a03' };
        case 'label':
            return {
                type,
                text: '',
                badgeText: '1',
                badgeBgColor: '#fef3c7',
                badgeBorderColor: '#b45309',
                badgeTextColor: '#451a03',
                badgeFontSize: 14,
                badgeSize: 32,
                badgeBold: true,
                bgColor: '#fef3c7',
                borderColor: '#b45309',
                textColor: '#451a03',
                rounded: 16,
                image: '',
                imagePosition: 'right',
                imageSize: 32,
            };
        case 'input_short_text':
            return { type, label: '' };
        case 'input_long_text':
            return { type, label: '' };
        case 'input_image':
            return { type, label: '' };
        case 'fill_blank':
            return { type, segments: [{ type: 'text', value: '' }], parts: ['', ''], answers: [''] };
        case 'input_radio':
            return { type, label: '', gunakanBackground: true, warnaBackground: '#fffbeb', warnaTeks: '#78350f', options: [{ teks: '', gambar: '', lebar: 100, mode: 'teks' }, { teks: '', gambar: '', lebar: 100, mode: 'teks' }] };
        case 'input_checkbox':
            return { type, label: '', gunakanBackground: true, warnaBackground: '#fffbeb', warnaTeks: '#78350f', options: [{ teks: '', gambar: '', lebar: 100, mode: 'teks' }, { teks: '', gambar: '', lebar: 100, mode: 'teks' }] };
        case 'input_matching':
            return { type, label: '', metode: 'seret', gunakanBackground: true, warnaBackground: '#fffbeb', warnaTeks: '#78350f', pairs: [{ kiri: '', kanan: '', gambarKiri: '', gambarKanan: '', lebarKiri: 100, lebarKanan: 100, modeKiri: 'teks', modeKanan: 'teks' }, { kiri: '', kanan: '', gambarKiri: '', gambarKanan: '', lebarKiri: 100, lebarKanan: 100, modeKiri: 'teks', modeKanan: 'teks' }] };
        case 'identity':
            return { type };
        case 'table':
            return {
                type,
                rows: [
                    [createTableCell(), createTableCell()],
                    [createTableCell(), createTableCell()],
                ],
                columnWidths: [50, 50],
                rowHeights: [80, 80],
                headerRows: 0,
                headerColor: '#fef3c7',
                headerTextColor: '#78350f',
            };
    }
}

export function createTableCell(): TableCell {
    return { type: 'paragraph', text: '', url: '', jarakBaris: 1.5 };
}

/** F4 paper is 215mm x 330mm. */
export const F4_WIDTH_MM = 215;
export const F4_HEIGHT_MM = 330;

export type JenisPengerjaan = 'individu' | 'kelompok';

export type DynamicHeadSectionId =
    | 'mata_pelajaran'
    | 'materi'
    | 'satuan_pendidikan'
    | 'tahun_pelajaran'
    | 'tahapan_fase'
    | 'kelas_label'
    | 'semester'
    | 'alokasi_waktu'
    | 'capaian_pembelajaran'
    | 'alur_tujuan_pembelajaran'
    | 'tujuan_pembelajaran'
    | 'indikator_ketercapaian'
    | 'model_pembelajaran';

export type PengaturanLkpd = {
    jenisPengerjaan: JenisPengerjaan;
    marginAtas: number;
    marginBawah: number;
    marginKiri: number;
    marginKanan: number;
    jarakBaris: number;
    headSections?: DynamicHeadSectionId[];
    headSelectedItems?: Partial<Record<DynamicHeadSectionId, number[]>>;
    headShowLetter?: boolean;
    headLetter?: string;
    headTopText?: string;
    headTopTextAlign?: 'left' | 'center' | 'right' | 'justify';
    headTopTextColor?: string;
    headTopTextBgColor?: string;
    headTopTextBorderColor?: string;
    headTopTextFontSize?: string | number;
    headTopTextFontWeight?: string | number;
    headTopTextBold?: boolean;
    headTopTextUnderline?: boolean;
    headTopTextItalic?: boolean;
    headTitleColor?: string;
    headTitleBgColor?: string;
    headTitleBorderColor?: string;
    headTitleFontSize?: string;
    headTitleFontWeight?: string | number;
    headLetterColor?: string;
    headLetterBgColor?: string;
    headLetterBorderColor?: string;
    headLetterFontSize?: string;
    headLetterFontWeight?: string | number;
};

export const DEFAULT_PENGATURAN: PengaturanLkpd = {
    jenisPengerjaan: 'individu',
    marginAtas: 20,
    marginBawah: 20,
    marginKiri: 25,
    marginKanan: 20,
    jarakBaris: 1.5,
    headSections: [],
    headSelectedItems: {},
    headShowLetter: false,
    headLetter: '',
    headTopText: '',
    headTopTextAlign: 'left',
    headTopTextColor: '#9a4b13',
    headTopTextBgColor: '#fff7ed',
    headTopTextBorderColor: '#f59e0b',
    headTopTextFontSize: 12,
    headTopTextFontWeight: '600',
    headTopTextBold: false,
    headTopTextUnderline: false,
    headTopTextItalic: false,
    headTitleColor: '#ffffff',
    headTitleBgColor: '#9a4b13',
    headTitleBorderColor: '#78350f',
    headTitleFontSize: '0.875rem',
    headTitleFontWeight: '700',
    headLetterColor: '#78350f',
    headLetterBgColor: '#ffffff',
    headLetterBorderColor: '#78350f',
    headLetterFontSize: '0.875rem',
    headLetterFontWeight: '700',
};

export const DYNAMIC_HEAD_SECTION_OPTIONS: Array<{ id: DynamicHeadSectionId; label: string }> = [
    { id: 'mata_pelajaran', label: 'Mata Pelajaran' },
    { id: 'materi', label: 'Materi' },
    { id: 'satuan_pendidikan', label: 'Satuan Pendidikan' },
    { id: 'tahun_pelajaran', label: 'Tahun Pelajaran' },
    { id: 'tahapan_fase', label: 'Tahapan (Fase)' },
    { id: 'kelas_label', label: 'Kelas' },
    { id: 'semester', label: 'Semester' },
    { id: 'alokasi_waktu', label: 'Alokasi Waktu' },
    { id: 'capaian_pembelajaran', label: 'Capaian Pembelajaran' },
    { id: 'alur_tujuan_pembelajaran', label: 'Alur Tujuan Pembelajaran' },
    { id: 'tujuan_pembelajaran', label: 'Tujuan Pembelajaran' },
    { id: 'indikator_ketercapaian', label: 'Indikator Ketercapaian' },
    { id: 'model_pembelajaran', label: 'Model Pembelajaran' },
];

/** Extract the video ID from a youtube.com/youtu.be URL, or null if it doesn't look like one. */
export function extractYoutubeId(url: string): string | null {
    const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{6,})/);
    return match ? match[1] : null;
}

const ALLOWED_RICH_TEXT_TAGS = new Set(['b', 'strong', 'u', 'i', 'em', 'br', 'div', 'p', 'ul', 'li', 'span']);

/** Keep only basic text formatting tags, stripping everything else (incl. attributes) to prevent injected markup/scripts. */
export function sanitizeRichText(value: string): string {
    return value.replace(/<\/?([a-z0-9]+)([^>]*)>/gi, (match, tagName: string, attributes: string) => {
        const tag = tagName.toLowerCase();
        if (tag === 'font' && !match.startsWith('</')) {
            const color = attributes.match(/color=["']?(#[0-9a-f]{6})["']?/i)?.[1];
            const size = attributes.match(/size=["']?([1-7])["']?/i)?.[1];
            const fontSize = size ? ({ 1: '12px', 2: '14px', 3: '16px', 4: '18px', 5: '24px', 6: '32px', 7: '48px' } as Record<string, string>)[size] : null;
            const styles = [color ? `color:${color}` : '', fontSize ? `font-size:${fontSize}` : ''].filter(Boolean);
            return styles.length ? `<span style="${styles.join(';')}">` : '<span>';
        }
        if (tag === 'font' && match.startsWith('</')) return '</span>';
        if (tag === 'span' && !match.startsWith('</')) {
            const color = attributes.match(/style=["'][^"']*color:\s*(#[0-9a-f]{6})[^"']*["']/i)?.[1];
            const fontSize = attributes.match(/style=["'][^"']*font-size:\s*([0-9.]+(?:px|rem|em|%)?)[^"']*["']/i)?.[1];
            const fontWeight = attributes.match(/style=["'][^"']*font-weight:\s*(normal|bold|[1-9]00)[^"']*["']/i)?.[1];
            const fontStyle = attributes.match(/style=["'][^"']*font-style:\s*(normal|italic|oblique)[^"']*["']/i)?.[1];
            const textDecoration = attributes.match(/style=["'][^"']*text-decoration(?:-line)?:\s*(none|underline|line-through)[^"']*["']/i)?.[1];
            const styles = [
                color ? `color:${color}` : '',
                fontSize ? `font-size:${fontSize}` : '',
                fontWeight ? `font-weight:${fontWeight}` : '',
                fontStyle ? `font-style:${fontStyle}` : '',
                textDecoration ? `text-decoration:${textDecoration}` : '',
            ].filter(Boolean);
            return styles.length ? `<span style="${styles.join(';')}">` : '<span>';
        }
        if (!ALLOWED_RICH_TEXT_TAGS.has(tag)) return '';
        return match.startsWith('</') ? `</${tag}>` : `<${tag}>`;
    });
}
