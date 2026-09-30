<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import Button from './Ui/Button.vue'
import EmptyState from './Ui/EmptyState.vue'

const props = defineProps({
  projects: { type: Array, default: () => [] },
  /** First line of the section heading (includes trailing “&” where used). */
  headingLead: { type: String, default: 'Success stories &' },
  /** Bold violet line in the heading. */
  headingAccent: { type: String, default: 'case studies' },
  /** Optional dynamic CMS section overrides headingLead/headingAccent and the View-all CTA. */
  section: { type: Object, default: null },
})

const resolvedHeadingLead = computed(() => props.section?.heading_lead || props.headingLead)
const resolvedHeadingAccent = computed(() => props.section?.heading_accent || props.headingAccent)
const viewAllLabel = computed(() => props.section?.view_all_label || 'View all case studies')
const viewAllHref = computed(() => props.section?.view_all_href || '/portfolio')

const current = ref(0)

const total = computed(() => props.projects.length)

const active = computed(() => {
  if (!total.value) return null
  return props.projects[Math.min(current.value, total.value - 1)]
})

/** Same artwork as the portfolio card: project upload, or public/images/case-studies/{slug}.jpg */
const heroImageUrl = computed(() => active.value?.image_url ?? null)
const heroImageWebp = computed(() => active.value?.image_webp_url ?? null)
const heroImageAlt = computed(() => {
  const t = active.value?.title
  return t ? `Preview image for ${t}` : 'Case study preview'
})

function prev() {
  if (total.value < 1) return
  current.value = (current.value - 1 + total.value) % total.value
}

function next() {
  if (total.value < 1) return
  current.value = (current.value + 1) % total.value
}

const techLine = computed(() => {
  const t = active.value?.tech
  if (!t?.length) return ''
  return t.join(', ')
})

function wordmark(title) {
  return (title || 'Project').toUpperCase()
}
</script>

<template>
  <section class="bg-white py-20 dark:bg-slate-950 sm:py-24 lg:py-28">
    <Container>
      <template v-if="total">
        <!-- Header: line runs from "case studies" across to dark link; chevrons far right (reference layout) -->
        <header class="w-full">
          <p
            class="font-sans text-[1.65rem] font-normal leading-[1.15] tracking-tight text-slate-700 sm:text-3xl md:text-4xl lg:text-[2.45rem] dark:text-slate-300"
          >
            {{ resolvedHeadingLead }}
          </p>

          <div class="mt-1 flex w-full min-w-0 flex-wrap items-center gap-x-3 gap-y-3 sm:flex-nowrap sm:gap-x-4">
            <span
              class="font-sans shrink-0 text-[1.65rem] font-bold leading-[1.15] tracking-tight text-violet-600 sm:text-3xl md:text-4xl lg:text-[2.45rem] dark:text-violet-400"
            >
              {{ resolvedHeadingAccent }}
            </span>

            <span
              class="hidden h-px min-w-0 flex-1 bg-violet-500/80 dark:bg-violet-500/70 sm:block"
              aria-hidden="true"
            />

            <Link
              :href="viewAllHref"
              class="group hidden shrink-0 items-center gap-1.5 text-sm font-medium text-slate-800 sm:inline-flex dark:text-slate-200"
            >
              {{ viewAllLabel }}
              <span class="text-violet-600 transition group-hover:translate-x-0.5 dark:text-violet-400" aria-hidden="true">→</span>
            </Link>

            <div class="ml-auto flex shrink-0 items-center gap-0 sm:ml-0 sm:pl-2">
              <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full text-slate-400 transition hover:bg-violet-50 hover:text-violet-700 disabled:cursor-not-allowed disabled:opacity-35 disabled:hover:bg-transparent dark:hover:bg-slate-800 dark:hover:text-violet-300"
                :disabled="total <= 1"
                aria-label="Previous case study"
                @click="prev"
              >
                <ChevronLeft class="h-7 w-7" stroke-width="1.25" />
              </button>
              <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full text-slate-400 transition hover:bg-violet-50 hover:text-violet-700 disabled:cursor-not-allowed disabled:opacity-35 disabled:hover:bg-transparent dark:hover:bg-slate-800 dark:hover:text-violet-300"
                :disabled="total <= 1"
                aria-label="Next case study"
                @click="next"
              >
                <ChevronRight class="h-7 w-7" stroke-width="1.25" />
              </button>
            </div>
          </div>

          <Link
            :href="viewAllHref"
            class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-800 sm:hidden dark:text-slate-200"
          >
            {{ viewAllLabel }}
            <span class="text-violet-600 dark:text-violet-400" aria-hidden="true">→</span>
          </Link>
        </header>

        <div
          v-if="active"
          :key="active.slug"
          class="mt-16 grid grid-cols-1 items-center gap-14 lg:mt-24 lg:grid-cols-2 lg:gap-x-20 lg:gap-y-12"
        >
          <!-- Left: logo row (swirl + uppercase) → title → body → tech → CTA -->
          <div class="order-2 max-w-lg lg:order-1">
            <div class="flex items-center gap-3.5">
              <span
                class="relative flex h-[3.25rem] w-[3.25rem] shrink-0 items-center justify-center overflow-hidden rounded-full shadow-md ring-2 ring-white dark:ring-slate-900"
                aria-hidden="true"
              >
                <span
                  class="absolute inset-0 rounded-full bg-[conic-gradient(at_50%_50%,#7c3aed,#4f46e5,#22d3ee,#6366f1,#7c3aed)]"
                />
                <span class="absolute inset-[3px] rounded-full bg-gradient-to-br from-violet-400/35 to-cyan-400/35 mix-blend-overlay" />
              </span>
              <span class="text-[0.8125rem] font-bold uppercase tracking-[0.2em] text-slate-800 dark:text-slate-100">
                {{ wordmark(active.title) }}
              </span>
            </div>

            <h3 class="mt-7 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-[1.75rem]">
              {{ active.title }}
            </h3>

            <p class="mt-4 text-[0.9375rem] leading-[1.7] text-slate-600 dark:text-slate-400 sm:text-base">
              {{ active.excerpt }}
            </p>

            <p
              v-if="techLine"
              class="mt-6 text-base font-normal leading-snug text-slate-400 dark:text-slate-500 sm:text-lg"
            >
              {{ techLine }}
            </p>

            <Link
              :href="`/portfolio/${active.slug}`"
              class="mt-9 inline-flex min-h-[2.75rem] items-center justify-center rounded-full bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 px-9 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950"
            >
              View Details
            </Link>
          </div>

          <!-- Right: MacBook-style + phone overlap -->
          <div class="relative order-1 flex justify-center lg:order-2 lg:justify-end">
            <div class="relative w-full max-w-[36rem] lg:max-w-none">
              <!-- Laptop shell -->
              <div
                class="relative mx-auto rounded-[1.15rem] bg-gradient-to-b from-[#e8e8ea] via-[#d1d1d6] to-[#b8b8c0] p-[0.55rem] pb-3 shadow-[0_25px_50px_-12px_rgba(15,23,42,0.28)] dark:from-slate-600 dark:via-slate-700 dark:to-slate-800 dark:shadow-black/40"
              >
                <div class="overflow-hidden rounded-lg bg-slate-950 ring-1 ring-black/5">
                  <div class="flex h-5 items-center justify-center gap-1.5 bg-slate-900/95">
                    <span class="h-2 w-2 rounded-full bg-slate-700" />
                    <span class="h-2 w-2 rounded-full bg-slate-700" />
                    <span class="h-2 w-2 rounded-full bg-slate-700" />
                  </div>
                  <div class="aspect-[16/10] bg-slate-900">
                    <picture v-if="heroImageUrl" class="block h-full w-full">
                      <source v-if="heroImageWebp" :srcset="heroImageWebp" type="image/webp">
                      <img
                        :src="heroImageUrl"
                        :alt="heroImageAlt"
                        class="h-full w-full object-cover object-top"
                        width="1400"
                        height="875"
                        loading="lazy"
                        decoding="async"
                      >
                    </picture>
                    <div
                      v-else
                      class="flex h-full min-h-[12rem] w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-800 via-slate-900 to-violet-950 px-6 text-center"
                      role="img"
                      :aria-label="heroImageAlt"
                    >
                      <p class="text-sm font-medium text-slate-300">No preview image</p>
                      <p class="max-w-xs text-xs leading-relaxed text-slate-500">
                        Add a project image in Admin → Projects (or place a JPEG at
                        <code class="rounded bg-slate-800 px-1 py-0.5 text-[10px] text-slate-400">public/images/case-studies/{{ active.slug }}.jpg</code>).
                      </p>
                    </div>
                  </div>
                </div>
                <!-- Hinge -->
                <div class="mx-auto mt-2 flex justify-center">
                  <div class="h-1.5 w-[38%] max-w-[11rem] rounded-full bg-[#a8a8ae]/90 dark:bg-slate-600" />
                </div>
              </div>

              <!-- Phone -->
              <div
                class="absolute -bottom-1 right-0 z-10 w-[30%] max-w-[10.5rem] translate-x-1 translate-y-1 sm:max-w-[11.5rem] lg:right-2 lg:translate-x-4 lg:translate-y-2"
              >
                <div
                  class="overflow-hidden rounded-[1.65rem] border-[10px] border-slate-900 bg-slate-900 shadow-[0_20px_40px_-8px_rgba(0,0,0,0.45)] dark:border-slate-950"
                >
                  <div class="aspect-[9/19] bg-slate-900">
                    <picture v-if="heroImageUrl" class="block h-full w-full">
                      <source v-if="heroImageWebp" :srcset="heroImageWebp" type="image/webp">
                      <img
                        :src="heroImageUrl"
                        :alt="heroImageAlt"
                        class="h-full w-full object-cover object-center"
                        width="700"
                        height="1400"
                        loading="lazy"
                        decoding="async"
                      >
                    </picture>
                    <div
                      v-else
                      class="flex h-full w-full items-center justify-center bg-gradient-to-b from-slate-800 to-slate-950"
                      role="img"
                      :aria-label="heroImageAlt"
                    >
                      <span class="text-[10px] font-medium uppercase tracking-wider text-slate-500">Preview</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- All projects strip -->
        <ul class="mt-10 divide-y divide-slate-100 dark:divide-slate-800 border-t border-slate-100 dark:border-slate-800">
          <li
            v-for="(project, index) in projects"
            :key="project.slug"
            class="flex items-center justify-between gap-4 py-3.5 cursor-pointer transition-colors hover:bg-slate-50 dark:hover:bg-slate-900 px-1 rounded-lg"
            :class="index === current ? 'opacity-100' : 'opacity-60 hover:opacity-100'"
            @click="current = index"
          >
            <div class="flex items-center gap-3 min-w-0">
              <span
                class="shrink-0 h-2 w-2 rounded-full transition-colors"
                :class="index === current ? 'bg-violet-600 dark:bg-violet-400' : 'bg-slate-300 dark:bg-slate-600'"
              />
              <span
                class="truncate text-sm font-semibold text-slate-900 dark:text-white transition-colors"
                :class="index === current ? 'text-violet-700 dark:text-violet-300' : ''"
              >{{ project.title }}</span>
              <span v-if="project.category" class="hidden sm:inline text-xs text-slate-400 dark:text-slate-500 shrink-0">— {{ project.category }}</span>
            </div>
            <Link
              :href="`/portfolio/${project.slug}`"
              class="shrink-0 text-xs font-medium text-violet-600 dark:text-violet-400 hover:underline"
              @click.stop
            >
              View →
            </Link>
          </li>
        </ul>
      </template>

      <template v-else>
        <header class="w-full">
          <p
            class="font-sans text-[1.65rem] font-normal leading-[1.15] tracking-tight text-slate-700 sm:text-3xl md:text-4xl lg:text-[2.45rem] dark:text-slate-300"
          >
            {{ resolvedHeadingLead }}
          </p>
          <div class="mt-1 flex w-full min-w-0 flex-wrap items-center gap-x-3 gap-y-3 sm:flex-nowrap sm:gap-x-4">
            <span
              class="font-sans shrink-0 text-[1.65rem] font-bold leading-[1.15] tracking-tight text-violet-600 sm:text-3xl md:text-4xl lg:text-[2.45rem] dark:text-violet-400"
            >
              {{ resolvedHeadingAccent }}
            </span>
            <span class="hidden h-px min-w-0 flex-1 bg-violet-500/80 dark:bg-violet-500/70 sm:block" aria-hidden="true" />
            <Link
              :href="viewAllHref"
              class="group hidden shrink-0 items-center gap-1.5 text-sm font-medium text-slate-800 sm:inline-flex dark:text-slate-200"
            >
              {{ viewAllLabel }}
              <span class="text-violet-600 transition group-hover:translate-x-0.5 dark:text-violet-400" aria-hidden="true">→</span>
            </Link>
          </div>
          <Link
            :href="viewAllHref"
            class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-800 sm:hidden dark:text-slate-200"
          >
            {{ viewAllLabel }}
            <span class="text-violet-600 dark:text-violet-400" aria-hidden="true">→</span>
          </Link>
        </header>
        <div class="mt-14 lg:mt-20">
          <EmptyState
            title="No case studies yet"
            description="Publish projects from the admin area to feature them here."
          >
            <Button href="/portfolio" variant="secondary">Browse projects</Button>
          </EmptyState>
        </div>
      </template>
    </Container>
  </section>
</template>
