<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { BarChart3, BookOpen, Users } from "@lucide/vue";
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
        title: "Statistik",
      },
    ],
  },
});

const chartWidth = 760;
const chartHeight = 260;
const chartPadding = { top: 20, right: 20, bottom: 42, left: 42 };
const maxScore = 100;
const studentColors = ["#b45309", "#0369a1", "#15803d", "#be123c", "#7c3aed", "#0f766e"];
const selectedStudentNames = ref<string[]>(
  props.statistics.students.map((student) => student.name)
);

const averagePoints = computed(() => {
  if (!props.statistics.tasks.length) return "";
  const width = chartWidth - chartPadding.left - chartPadding.right;
  const height = chartHeight - chartPadding.top - chartPadding.bottom;

  return props.statistics.tasks
    .map((task, index) => {
      const x =
        chartPadding.left +
        (props.statistics.tasks.length === 1
          ? width / 2
          : (index / (props.statistics.tasks.length - 1)) * width);
      const y = chartPadding.top + height - (task.average / maxScore) * height;
      return `${x},${y}`;
    })
    .join(" ");
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
  <Head title="Statistik" />
  <div class="min-h-screen bg-amber-50/60 p-3 sm:p-4 md:p-8">
    <div class="mx-auto max-w-6xl space-y-6">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-widest text-amber-700">
            Analisis kelas
          </p>
          <h1 class="mt-2 text-2xl font-bold text-amber-950 sm:text-3xl">Statistik Nilai Siswa</h1>
          <p class="mt-2 text-sm text-amber-900/70">
            Pantau perkembangan nilai dari setiap tugas.
          </p>
        </div>
        <label class="flex w-full flex-col items-stretch gap-2 text-sm font-semibold text-amber-950 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
          <span>Pilih kelas</span>
          <select
            :value="selectedClassId ?? ''"
            class="w-full rounded-md border border-amber-200 bg-white px-3 py-2 shadow-sm outline-none focus:border-amber-600 sm:w-auto"
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
        class="rounded-xl border border-amber-200 bg-white p-12 text-center text-amber-900/70"
      >
        Belum ada kelas yang dapat dianalisis.
      </div>
      <template v-else>
        <div class="grid gap-4 sm:grid-cols-3">
          <div class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm">
            <Users class="size-6 text-amber-700" />
            <p class="mt-4 text-sm text-amber-900/70">Siswa teridentifikasi</p>
            <p class="mt-1 text-3xl font-bold text-amber-950">
              {{ statistics.summary.students }}
            </p>
          </div>
          <div class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm">
            <BookOpen class="size-6 text-amber-700" />
            <p class="mt-4 text-sm text-amber-900/70">Jawaban dinilai</p>
            <p class="mt-1 text-3xl font-bold text-amber-950">
              {{ statistics.summary.submissions }}
            </p>
          </div>
          <div class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm">
            <BarChart3 class="size-6 text-amber-700" />
            <p class="mt-4 text-sm text-amber-900/70">Rata-rata kelas</p>
            <p class="mt-1 text-3xl font-bold text-amber-950">
              {{
                statistics.summary.average === null
                  ? "Belum ada"
                  : statistics.summary.average
              }}
            </p>
          </div>
        </div>

        <section class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-lg font-bold text-amber-950">Rata-rata per tugas</h2>
              <p class="text-sm text-amber-900/60">
                Grafik naik turun nilai rata-rata kelas.
              </p>
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
              <line
                v-for="score in [0, 25, 50, 75, 100]"
                :key="score"
                :x1="chartPadding.left"
                :x2="chartWidth - chartPadding.right"
                :y1="pointY(score)"
                :y2="pointY(score)"
                stroke="#f1d9b5"
                stroke-width="1"
              />
              <text
                v-for="score in [0, 25, 50, 75, 100]"
                :key="`label-${score}`"
                :x="8"
                :y="pointY(score) + 4"
                fill="#92400e"
                font-size="12"
              >
                {{ score }}
              </text>
              <polyline
                :points="averagePoints"
                fill="none"
                stroke="#92400e"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="4"
              />
              <g v-for="(task, index) in statistics.tasks" :key="task.number">
                <circle
                  :cx="pointX(index)"
                  :cy="pointY(task.average)"
                  r="6"
                  fill="#d97706"
                  stroke="white"
                  stroke-width="3"
                />
                <text
                  :x="pointX(index)"
                  :y="chartHeight - 12"
                  text-anchor="middle"
                  fill="#78350f"
                  font-size="12"
                >
                  T{{ task.number }}
                </text>
              </g>
            </svg>
          </div>
        </section>

        <section class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
              <h2 class="text-lg font-bold text-amber-950">Tren nilai siswa</h2>
              <p class="text-sm text-amber-900/60">
                Semua siswa dalam kelas ditampilkan sebagai garis perkembangan.
              </p>
            </div>
            <div class="flex max-w-full flex-wrap justify-end gap-2 text-xs">
              <button
                type="button"
                class="rounded-md border border-amber-200 px-2 py-1 font-semibold text-amber-800 hover:bg-amber-50"
                @click="showAllStudents"
              >
                Semua
              </button>
              <button
                type="button"
                class="rounded-md border border-amber-200 px-2 py-1 font-semibold text-amber-800 hover:bg-amber-50"
                @click="hideAllStudents"
              >
                Kosongkan
              </button>
              <button
                v-for="(student, index) in statistics.students"
                :key="`legend-${student.name}`"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 transition"
                :class="
                  selectedStudentNames.includes(student.name)
                    ? 'border-amber-300 bg-amber-50 text-amber-950'
                    : 'border-gray-200 text-gray-400'
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
                stroke-width="1"
              />
              <text
                v-for="score in [0, 25, 50, 75, 100]"
                :key="`student-label-${score}`"
                :x="8"
                :y="pointY(score) + 4"
                fill="#92400e"
                font-size="12"
              >
                {{ score }}
              </text>
              <polyline
                v-for="entry in chartStudents"
                :key="`student-line-${entry.student.name}`"
                :points="studentPoints(entry.student)"
                fill="none"
                :stroke="studentColor(entry.index)"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                opacity="0.85"
              />
              <text
                v-for="(task, index) in statistics.tasks"
                :key="`student-task-label-${task.number}`"
                :x="pointX(index)"
                :y="chartHeight - 12"
                text-anchor="middle"
                fill="#78350f"
                font-size="12"
              >
                T{{ task.number }}
              </text>
            </svg>
          </div>
          <div v-else class="py-12 text-center text-sm text-amber-900/60">
            Pilih minimal satu nama siswa untuk menampilkan grafik.
          </div>
        </section>

        <section class="rounded-xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
          <h2 class="text-lg font-bold text-amber-950">Perkembangan tiap siswa</h2>
          <p class="text-sm text-amber-900/60">
            Nama dengan perbedaan huruf besar-kecil, spasi, atau maksimal 1-3 karakter
            akan dianggap siswa yang sama.
          </p>
          <div
            v-if="!studentRows.length"
            class="py-12 text-center text-sm text-amber-900/60"
          >
            Belum ada siswa yang memiliki nilai.
          </div>
          <div v-else class="mt-5 overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
              <thead class="bg-amber-50 text-amber-900">
                <tr>
                  <th class="px-4 py-3">Nama siswa</th>
                  <th
                    v-for="task in statistics.tasks"
                    :key="task.number"
                    class="px-4 py-3 text-center"
                  >
                    T{{ task.number }}
                  </th>
                  <th class="px-4 py-3 text-center">Rata-rata</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-amber-100">
                <tr
                  v-for="student in studentRows"
                  :key="student.name"
                  class="hover:bg-amber-50/60"
                >
                  <td class="px-4 py-3 font-semibold text-amber-950">
                    {{ student.name }}
                  </td>
                  <td
                    v-for="task in statistics.tasks"
                    :key="task.number"
                    class="px-4 py-3 text-center text-amber-900/80"
                  >
                    {{ student.scores[String(task.number)] ?? "-" }}
                  </td>
                  <td class="px-4 py-3 text-center font-bold text-amber-800">
                    {{ student.average ?? "-" }}
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
