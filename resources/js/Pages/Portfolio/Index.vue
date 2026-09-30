<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import CaseStudiesHero from '../../Components/CaseStudiesHero.vue'
import CaseStudyGridCard from '../../Components/CaseStudyGridCard.vue'
import Container from '../../Components/Ui/Container.vue'
import EmptyState from '../../Components/Ui/EmptyState.vue'
import { Star } from 'lucide-vue-next'

const props = defineProps({
  filters: { type: Object, default: () => ({ categories: [] }) },
  categories: { type: Array, default: () => [] },
  projects: { type: Object, required: true },
})

const selectedCategories = computed(() => {
  const c = props.filters?.categories
  return Array.isArray(c) ? c : []
})

const isEmpty = computed(() => props.projects.total === 0)
const hasCategoryFilter = computed(() => selectedCategories.value.length > 0)
const wrongPage = computed(
  () => props.projects.total > 0 && props.projects.data.length === 0,
)

const firstPortfolioPageUrl = computed(
  () => props.projects.first_page_url || '/portfolio',
)

const perPage = computed(() => props.projects.per_page ?? 9)
const currentPage = computed(() => props.projects.current_page ?? 1)

function indexLabel(listIndex) {
  const n = (currentPage.value - 1) * perPage.value + listIndex + 1
  return String(n).padStart(3, '0')
}

function applyCategories(categories) {
  const next = categories.length
    ? { categories: categories.join(',') }
    : {}
  router.get('/portfolio', next, { preserveState: true, preserveScroll: true, replace: true })
}

function toggleCategory(category) {
  const cur = [...selectedCategories.value]
  const i = cur.indexOf(category)
  if (i >= 0) {
    cur.splice(i, 1)
  } else {
    cur.push(category)
  }
  applyCategories(cur)
}

function clearCategories() {
  applyCategories([])
}

function filterChipClass(category) {
  const active = category === '' ? !hasCategoryFilter.value : selectedCategories.value.includes(category)
  return active
    ? 'border border-transparent bg-blue-600 text-white shadow-md shadow-blue-600/25'
    : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'
}
</script>

<template>
  <div>
    <CaseStudiesHero />

    <section class="pb-20 pt-12 sm:pb-24 sm:pt-16">
      <Container>
        <div class="text-center">
          <div
            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200"
          >
            <Star class="h-4 w-4 shrink-0 text-violet-600 dark:text-violet-400" aria-hidden="true" stroke-width="2" />
            <span>Case studies</span>
          </div>
          <h2
            class="mt-6 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.4rem] md:leading-tight dark:text-slate-100"
          >
            Our top AI &amp; ML projects
          </h2>
          <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
            Explore shipped products—from intelligent automation and data pipelines to full-stack web apps. Filter by
            focus area to find work similar to what you want to build.
          </p>
        </div>

        <div class="mt-10 flex flex-wrap justify-center gap-2 sm:mt-12">
          <button
            type="button"
            class="rounded-full px-4 py-2 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 sm:text-sm"
            :class="filterChipClass('')"
            @click="clearCategories"
          >
            All
          </button>

          <button
            v-for="c in categories"
            :key="c"
            type="button"
            class="rounded-full px-4 py-2 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 sm:text-sm"
            :class="filterChipClass(c)"
            @click="toggleCategory(c)"
          >
            {{ c }}
          </button>
        </div>

        <div v-if="wrongPage" class="mt-12">
          <EmptyState
            title="No projects on this page"
            description="Try returning to the first page of results."
          >
            <Link
              :href="firstPortfolioPageUrl"
              class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            >
              Back to portfolio
            </Link>
          </EmptyState>
        </div>

        <div v-else-if="isEmpty && hasCategoryFilter" class="mt-12">
          <EmptyState
            title="No projects in these categories"
            description="Try different filters or view all projects."
          >
            <button
              type="button"
              class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
              @click="clearCategories"
            >
              Clear filters
            </button>
            <Link
              href="/contact"
              class="inline-flex items-center justify-center rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-blue-500"
            >
              Get in touch
            </Link>
          </EmptyState>
        </div>

        <div v-else-if="isEmpty" class="mt-12">
          <EmptyState
            title="No projects to show yet"
            description="New case studies will appear here once they are published."
          >
            <Link
              href="/contact"
              class="inline-flex items-center justify-center rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-blue-500"
            >
              Start a project
            </Link>
          </EmptyState>
        </div>

        <div v-else class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
          <CaseStudyGridCard
            v-for="(p, i) in projects.data"
            :key="p.slug"
            :index-label="indexLabel(i)"
            :title="p.title"
            :slug="p.slug"
            :excerpt="p.excerpt"
            :image-url="p.image_url ?? p.imageUrl"
            :image-webp-url="p.image_webp_url ?? p.imageWebpUrl"
          />
        </div>

        <div
          v-if="projects.last_page > 1"
          class="mt-12 flex flex-wrap items-center justify-center gap-3"
        >
          <Link
            v-if="projects.prev_page_url"
            :href="projects.prev_page_url"
            class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
          >
            Previous
          </Link>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            Page <span class="font-semibold text-slate-900 dark:text-slate-100">{{ projects.current_page }}</span>
            of
            <span class="font-semibold text-slate-900 dark:text-slate-100">{{ projects.last_page }}</span>
          </p>
          <Link
            v-if="projects.next_page_url"
            :href="projects.next_page_url"
            class="rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
          >
            Next
          </Link>
        </div>

        <div
          class="mt-14 rounded-2xl border border-slate-200 bg-white px-6 py-8 text-center sm:px-10 dark:border-slate-700 dark:bg-slate-900/40"
        >
          <p class="text-sm text-slate-600 dark:text-slate-400">
            Want something similar for your product?
          </p>
          <div class="mt-4 flex justify-center">
            <Link
              href="/contact"
              class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
            >
              Work with me
            </Link>
          </div>
        </div>
      </Container>
    </section>
  </div>
</template>
