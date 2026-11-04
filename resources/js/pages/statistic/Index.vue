<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { BarChart3, BookOpen, TrendingUp, Users } from "@lucide/vue";
import { computed, ref, watch } from "vue";

type ClassItem = { id: number; nama: string };
type Task = { number: number; label: string; average: number; count: number };
type Student = { name: string; scores: Record<string, number> };
type Statistics = {
  students: Student[];
  tasks: Task[];
  summary: { students: number; submissions: number; average: number | null };
};

const props = defineProps<{
  classes: ClassItem[];
  selectedClassId: number | null;
  statistics: Statistics;
}>();

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: "Statistik Nilai",
      },
    ],
  },
});

const chartWidth = 760;
const chartHeight = 270;
const chartPadding = { top: 28, right: 28, bottom: 44, left: 46 };
const maxScore = 100;
const studentColors = ["#b45309", "#0369a1", "#15803d", "#be123c", "#7c3aed", "#0f766e"];
const selectedStudentNames = ref<string[]>(
  props.statistics.students.map((student) => student.name)
);

const averagePoints = computed(() => {
  if (!props.statistics.tasks.length) return "";
  return props.statistics.tasks
    .map((task, index) => `${pointX(index)},${pointY(task.average)}`)
    .join(" ");
});

const averageAreaPoints = computed(() => {
  if (!props.statistics.tasks.length) return "";
  const firstX = pointX(0);
  const lastX = pointX(props.statistics.tasks.length - 1);
  const baselineY = pointY(0);
  return `${firstX},${baselineY} ${averagePoints.value} ${lastX},${baselineY}`;
});

const studentRows = computed(() =>
  props.statistics.students.map((student) => ({
    ...student,
    average: Object.values(student.scores).length
      ? Math.round(
          (Object.values(student.scores).reduce((sum, score) => sum + score, 0) /
            Object.values(student.scores).length) *
            10
        ) / 10
      : null,
  }))
);

function changeClass(event: Event) {
  const classId = Number((event.target as HTMLSelectElement).value);
  router.get("/statistic", { class_id: classId }, { preserveState: true, replace: true });
}

function pointX(index: number) {
  const width = chartWidth - chartPadding.left - chartPadding.right;
  return (
    chartPadding.left +
    (props.statistics.tasks.length === 1
      ? width / 2
      : (index / (props.statistics.tasks.length - 1)) * width)
  );
}

function pointY(score: number) {
  const height = chartHeight - chartPadding.top - chartPadding.bottom;
  return chartPadding.top + height - (score / maxScore) * height;
}

function studentPoints(student: Student) {
  return props.statistics.tasks
    .map((task, index) =>
      student.scores[String(task.number)] === undefined
        ? null
        : `${pointX(index)},${pointY(student.scores[String(task.number)])}`
    )
    .filter((point): point is string => point !== null)
    .join(" ");
}

function studentCircles(student: Student) {
  return props.statistics.tasks
    .map((task, index) => {
      const score = student.scores[String(task.number)];
      if (score === undefined) return null;
      return {
        cx: pointX(index),
        cy: pointY(score),
        score,
        taskNumber: task.number,
      };
    })
    .filter((item): item is { cx: number; cy: number; score: number; taskNumber: number } => item !== null);
}

const chartStudents = computed(() =>
  props.statistics.students
    .map((student, index) => ({ student, index }))
    .filter(({ student }) => selectedStudentNames.value.includes(student.name))
);

watch(
  () => props.statistics.students,
  (students) => {
    selectedStudentNames.value = students.map((student) => student.name);
  }
);

function toggleStudent(name: string) {
  selectedStudentNames.value = selectedStudentNames.value.includes(name)
    ? selectedStudentNames.value.filter((studentName) => studentName !== name)
    : [...selectedStudentNames.value, name];
}

function showAllStudents() {
  selectedStudentNames.value = props.statistics.students.map((student) => student.name);
}

function hideAllStudents() {
  selectedStudentNames.value = [];
}

function studentColor(index: number) {
  return studentColors[index % studentColors.length];
}
</script>

<template>
  <Head title="Statistik Nilai" />
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-5 md:p-8 dark:bg-background">
    <div class="mx-auto max-w-6xl space-y-6">
      <div class="flex flex-wrap items-end justify-between gap-4 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6 dark:border-border dark:bg-card">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400">
            Analisis kelas
          </p>
          <h1 class="mt-1.5 text-2xl font-bold text-amber-950 sm:text-3xl dark:text-amber-50">
            Statistik Nilai Siswa
          </h1>
          <p class="mt-1 text-sm text-amber-900/70 dark:text-muted-foreground">
            Pantau perkembangan nilai rata-rata dan tren individu dari setiap tugas LKPD.
          </p>
        </div>
        <label class="flex w-full flex-col items-stretch gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-900 sm:w-auto sm:flex-row sm:items-center sm:gap-3 dark:text-amber-200">
          <span>Pilih Kelas</span>
          <select
            :value="selectedClassId ?? ''"
            class="w-full rounded-xl border border-amber-300 bg-amber-50/50 px-3.5 py-2.5 text-sm font-semibold text-amber-950 shadow-2xs outline-none focus:border-amber-600 focus:bg-white sm:w-60 dark:border-border dark:bg-background dark:text-foreground"
            @change="changeClass"
          >
            <option v-if="!classes.length" value="">Belum ada kelas</option>
            <option v-for="item in classes" :key="item.id" :value="item.id">
              {{ item.nama }}
            </option>
          </select>
        </label>
      </div>

      <div
        v-if="!classes.length"
        class="rounded-2xl border border-amber-200 bg-white p-12 text-center text-amber-900/70 dark:border-border dark:bg-card"
      >
        Belum ada kelas yang dapat dianalisis.
      </div>
      <template v-else>
        <div class="grid gap-4 sm:grid-cols-3">
          <div class="flex items-start justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-amber-900/60 dark:text-muted-foreground">Siswa teridentifikasi</p>
              <p class="mt-2 text-3xl font-black text-amber-950 dark:text-amber-50">
                {{ statistics.summary.students }}
              </p>
            </div>
            <span class="flex size-11 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
              <Users class="size-5" />
            </span>
          </div>
          <div class="flex items-start justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-amber-900/60 dark:text-muted-foreground">Jawaban dinilai</p>
              <p class="mt-2 text-3xl font-black text-amber-950 dark:text-amber-50">
                {{ statistics.summary.submissions }}
              </p>
            </div>
            <span class="flex size-11 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
              <BookOpen class="size-5" />
            </span>
          </div>
          <div class="flex items-start justify-between rounded-2xl border border-amber-200 bg-white p-5 shadow-sm dark:border-border dark:bg-card">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-amber-900/60 dark:text-muted-foreground">Rata-rata kelas</p>
              <p class="mt-2 text-3xl font-black text-amber-950 dark:text-amber-50">
                {{
                  statistics.summary.average === null
                    ? "Belum ada"
                    : statistics.summary.average
                }}
              </p>
            </div>
            <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800">
              <BarChart3 class="size-5" />
            </span>
          </div>
        </div>

        <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6 dark:border-border dark:bg-card">
          <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <span class="flex size-10 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                <TrendingUp class="size-5" />
              </span>
              <div>
                <h2 class="text-lg font-bold text-amber-950 dark:text-amber-50">Rata-rata per tugas</h2>
                <p class="text-sm text-amber-900/60 dark:text-muted-foreground">
                  Grafik perkembangan nilai rata-rata kelas di setiap tugas LKPD.
                </p>
              </div>
            </div>
          </div>
          <div
            v-if="!statistics.tasks.length"
            class="py-16 text-center text-sm text-amber-900/60"
          >
            Belum ada nilai yang dapat ditampilkan.
          </div>
          <div v-else class="mt-6 overflow-x-auto">
            <svg
              :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
              class="h-auto min-w-[620px] w-full"
              role="img"
              aria-label="Grafik rata-rata nilai per tugas"
            >
              <defs>
                <linearGradient id="amberAreaGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#d97706" stop-opacity="0.28" />
                  <stop offset="100%" stop-color="#d97706" stop-opacity="0.0" />
                </linearGradient>
              </defs>
              <line
                v-for="score in [0, 25, 50, 75, 100]"
                :key="score"
                :x1="chartPadding.left"
                :x2="chartWidth - chartPadding.right"
                :y1="pointY(score)"
                :y2="pointY(score)"
                stroke="#f1d9b5"
                stroke-dasharray="4 4"
                stroke-width="1"
              />
              <text
                v-for="score in [0, 25, 50, 75, 100]"
                :key="`label-${score}`"
                :x="10"
                :y="pointY(score) + 4"
                fill="#92400e"
                font-size="11"
                font-weight="600"
              >
                {{ score }}
              </text>
              <polygon
                v-if="statistics.tasks.length > 1"
                :points="averageAreaPoints"
                fill="url(#amberAreaGrad)"
              />
              <polyline
                :points="averagePoints"
                fill="none"
                stroke="#92400e"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="3.5"
              />
              <g v-for="(task, index) in statistics.tasks" :key="task.number">
                <circle
                  :cx="pointX(index)"
                  :cy="pointY(task.average)"
                  r="6.5"
                  fill="#d97706"
                  stroke="white"
                  stroke-width="3"
                >
                  <title>{{ task.label }}: Rata-rata {{ task.average }} ({{ task.count }} jawaban)</title>
                </circle>
                <text
                  :x="pointX(index)"
                  :y="pointY(task.average) - 12"
                  text-anchor="middle"
                  fill="#78350f"
                  font-size="11"
                  font-weight="700"
                >
                  {{ task.average }}
                </text>
                <text
                  :x="pointX(index)"
                  :y="chartHeight - 12"
                  text-anchor="middle"
                  fill="#78350f"
                  font-size="12"
                  font-weight="600"
                >
                  T{{ task.number }}
                </text>
              </g>
            </svg>
          </div>
        </section>

        <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6 dark:border-border dark:bg-card">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
              <h2 class="text-lg font-bold text-amber-950 dark:text-amber-50">Tren nilai siswa</h2>
              <p class="text-sm text-amber-900/60 dark:text-muted-foreground">
                Klik nama siswa di bawah untuk memfilter garis perkembangan nilai individu.
              </p>
            </div>
            <div class="flex max-w-full flex-wrap justify-end gap-2 text-xs">
              <button
                type="button"
                class="rounded-lg border border-amber-200 bg-amber-50/60 px-2.5 py-1.5 font-bold text-amber-800 hover:bg-amber-100"
                @click="showAllStudents"
              >
                Pilih Semua
              </button>
              <button
                type="button"
                class="rounded-lg border border-amber-200 px-2.5 py-1.5 font-semibold text-amber-800 hover:bg-amber-50"
                @click="hideAllStudents"
              >
                Kosongkan
              </button>
              <button
                v-for="(student, index) in statistics.students"
                :key="`legend-${student.name}`"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 font-semibold transition"
                :class="
                  selectedStudentNames.includes(student.name)
                    ? 'border-amber-400 bg-amber-50 text-amber-950 shadow-2xs'
                    : 'border-gray-200 text-gray-400 opacity-60'
                "
                @click="toggleStudent(student.name)"
              >
                <span
                  class="size-2.5 rounded-full"
                  :style="{ backgroundColor: studentColor(index) }"
                />
                {{ student.name }}
              </button>
            </div>
          </div>
          <div v-if="chartStudents.length" class="mt-6 overflow-x-auto">
            <svg
              :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
              class="h-auto min-w-[620px] w-full"
              role="img"
              aria-label="Grafik tren nilai semua siswa"
            >
              <line
                v-for="score in [0, 25, 50, 75, 100]"
                :key="`student-grid-${score}`"
                :x1="chartPadding.left"
                :x2="chartWidth - chartPadding.right"
                :y1="pointY(score)"
                :y2="pointY(score)"
                stroke="#f1d9b5"
                stroke-dasharray="4 4"
                stroke-width="1"
              />
              <text
                v-for="score in [0, 25, 50, 75, 100]"
                :key="`student-label-${score}`"
                :x="10"
                :y="pointY(score) + 4"
                fill="#92400e"
                font-size="11"
                font-weight="600"
              >
                {{ score }}
              </text>
              <g v-for="entry in chartStudents" :key="`student-group-${entry.student.name}`">
                <polyline
                  :points="studentPoints(entry.student)"
                  fill="none"
                  :stroke="studentColor(entry.index)"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  opacity="0.85"
                />
                <circle
                  v-for="pt in studentCircles(entry.student)"
                  :key="`${entry.student.name}-${pt.taskNumber}`"
                  :cx="pt.cx"
                  :cy="pt.cy"
                  r="5"
                  :fill="studentColor(entry.index)"
                  stroke="white"
                  stroke-width="2"
                >
                  <title>{{ entry.student.name }} · T{{ pt.taskNumber }}: {{ pt.score }}</title>
                </circle>
              </g>
              <text
                v-for="(task, index) in statistics.tasks"
                :key="`student-task-label-${task.number}`"
                :x="pointX(index)"
                :y="chartHeight - 12"
                text-anchor="middle"
                fill="#78350f"
                font-size="12"
                font-weight="600"
              >
                T{{ task.number }}
              </text>
            </svg>
          </div>
          <div v-else class="py-12 text-center text-sm text-amber-900/60">
            Pilih minimal satu nama siswa untuk menampilkan grafik.
          </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm dark:border-border dark:bg-card">
          <div class="border-b border-amber-100 p-5 sm:p-6 dark:border-border">
            <h2 class="text-lg font-bold text-amber-950 dark:text-amber-50">Tabel Perkembangan Tiap Siswa</h2>
            <p class="mt-0.5 text-sm text-amber-900/60 dark:text-muted-foreground">
              Nama dengan perbedaan huruf besar-kecil, spasi, atau maksimal 1–3 karakter akan digabung otomatis.
            </p>
          </div>
          <div
            v-if="!studentRows.length"
            class="py-12 text-center text-sm text-amber-900/60"
          >
            Belum ada siswa yang memiliki nilai.
          </div>
          <div v-else class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
              <thead class="border-b border-amber-200 bg-amber-50/80 text-xs uppercase tracking-wider text-amber-900 dark:border-border dark:bg-secondary">
                <tr>
                  <th class="px-5 py-3.5 font-bold">Nama Siswa</th>
                  <th
                    v-for="task in statistics.tasks"
                    :key="task.number"
                    class="px-4 py-3.5 text-center font-bold"
                  >
                    T{{ task.number }}
                  </th>
                  <th class="px-5 py-3.5 text-center font-bold">Rata-rata</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-amber-100 dark:divide-border">
                <tr
                  v-for="(student, idx) in studentRows"
                  :key="student.name"
                  class="transition hover:bg-amber-50/60 dark:hover:bg-accent"
                >
                  <td class="px-5 py-3.5 font-bold text-amber-950 dark:text-amber-50">
                    <span class="inline-flex items-center gap-2">
                      <span class="size-2.5 rounded-full" :style="{ backgroundColor: studentColor(idx) }" />
                      {{ student.name }}
                    </span>
                  </td>
                  <td
                    v-for="task in statistics.tasks"
                    :key="task.number"
                    class="px-4 py-3.5 text-center font-medium text-amber-900/80 dark:text-amber-200"
                  >
                    {{ student.scores[String(task.number)] ?? "-" }}
                  </td>
                  <td class="px-5 py-3.5 text-center">
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-900 dark:bg-secondary dark:text-amber-200">
                      {{ student.average ?? "-" }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </template>
    </div>
  </div>
</template>
