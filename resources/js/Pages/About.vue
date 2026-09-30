<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  ArrowRight,
  Brain,
  Check,
  Cpu,
  FileBarChart,
  Heart,
  Sparkles,
  UserRound,
  Box,
  LayoutGrid,
  Zap,
  Star,
  Target,
  PenLine,
  LineChart,
  ShieldCheck,
  MessageCircle,
  Smartphone,
} from 'lucide-vue-next'

import Container from '../Components/Ui/Container.vue'

const props = defineProps({
  aboutPage: { type: Object, default: null },
})

const lucideMap = {
  Brain,
  Heart,
  FileBarChart,
  Sparkles,
  Cpu,
  UserRound,
  Box,
  LayoutGrid,
  Zap,
  Star,
  Target,
  PenLine,
  LineChart,
  ShieldCheck,
  MessageCircle,
  Smartphone,
  Check,
}

function lucideIcon(name) {
  return lucideMap[name] || Sparkles
}

const defaultPillars = [
  {
    title: 'Full-stack delivery',
    description: 'From discovery and UI polish to APIs, data models, and deployment—we own the vertical slice so you are not juggling vendors.',
    icon_class: 'from-blue-500 via-indigo-500 to-violet-600',
  },
  {
    title: 'Built to last',
    description: 'Tests where they matter, observability hooks, and documentation that matches reality—so your codebase stays approachable as you grow.',
    icon_class: 'from-emerald-500 via-teal-500 to-cyan-600',
  },
  {
    title: 'Transparent partnership',
    description: 'Plain-language updates, visible trade-offs, and scope you can reason about. We would rather under-promise and over-deliver than hide risk.',
    icon_class: 'from-amber-500 via-orange-500 to-rose-600',
  },
  {
    title: 'Experience across domains',
    description: 'SaaS, marketplaces, content systems, and internal tools—patterns we have seen before, applied carefully to your context.',
    icon_class: 'from-violet-500 via-fuchsia-600 to-pink-600',
  },
]

const defaultValueItems = [
  {
    icon: 'Brain',
    title: 'Thoughtful AI',
    description: 'When AI fits your product, we integrate it with guardrails: retrieval, evaluation, and UX that sets expectations with users and stakeholders.',
  },
  {
    icon: 'Heart',
    title: 'Direct communication',
    description: 'No black-box phases. You get regular demos, written decisions, and a single thread of accountability from kickoff through handoff.',
  },
  {
    icon: 'FileBarChart',
    title: 'Measurable progress',
    description: 'Milestones tied to user-visible value, not vanity metrics—so you can steer budget and timeline with confidence.',
  },
]

function pick(value, fallback) {
  return (value ?? '').toString().trim() !== '' ? value : fallback
}

const heroEyebrow = computed(() => pick(props.aboutPage?.hero_eyebrow, 'About Creafynest'))
const heroLead = computed(() => pick(props.aboutPage?.hero_heading_lead, 'We build software'))
const heroAccent = computed(() => pick(props.aboutPage?.hero_heading_accent, 'you can grow into'))
const heroDescription = computed(() => pick(props.aboutPage?.hero_description, 'Creafynest is a product-minded engineering studio. We help teams ship reliable web and mobile software—clear UX, maintainable code, and delivery you can plan around.'))

const heroPrimaryLabel = computed(() => pick(props.aboutPage?.hero_primary_label, 'Get in touch'))
const heroPrimaryHref = computed(() => pick(props.aboutPage?.hero_primary_href, '/contact'))
const heroSecondaryLabel = computed(() => pick(props.aboutPage?.hero_secondary_label, 'See case studies'))
const heroSecondaryHref = computed(() => pick(props.aboutPage?.hero_secondary_href, '/portfolio'))
const heroTertiaryLabel = computed(() => props.aboutPage?.hero_tertiary_label ?? 'View services')
const heroTertiaryHref = computed(() => props.aboutPage?.hero_tertiary_href ?? '/services')

const panelEyebrow = computed(() => pick(props.aboutPage?.panel_eyebrow, 'How we work'))
const panelDescription = computed(() => pick(props.aboutPage?.panel_description, 'Strategy, engineering, and continuity in one studio—so launches stick and your team stays unblocked.'))
const panelNodeOne = computed(() => pick(props.aboutPage?.panel_node_one_label, 'Craft'))
const panelNodeTwo = computed(() => pick(props.aboutPage?.panel_node_two_label, 'Strategy'))
const panelNodeThree = computed(() => pick(props.aboutPage?.panel_node_three_label, 'Engineering'))
const panelCenter = computed(() => pick(props.aboutPage?.panel_center_label, 'Creafynest'))
const panelFooter = computed(() => pick(props.aboutPage?.panel_footer_label, 'One studio · long arcs'))

const whoHeading = computed(() => pick(props.aboutPage?.who_heading, 'Who we are'))
const whoDescription = computed(() => pick(props.aboutPage?.who_description, 'We combine Laravel, Vue, and modern cloud tooling with pragmatic product thinking. Whether you need a customer-facing app, an internal platform, or AI features that fit your roadmap, we focus on outcomes: performance, security, and teams that can own what we build.'))

const whyHeading = computed(() => pick(props.aboutPage?.why_heading, 'Why work with us'))
const whyDescription = computed(() => pick(props.aboutPage?.why_description, 'We care about the boring parts—security headers, caching, migrations, and release hygiene—because that is what keeps products stable in production.'))

const ctaActive = computed(() => props.aboutPage?.cta_is_active !== false)
const ctaHeading = computed(() => pick(props.aboutPage?.cta_heading, 'Ready to talk about your next build?'))
const ctaDescription = computed(() => pick(props.aboutPage?.cta_description, 'Tell us about timelines, constraints, and what success looks like—we will respond with honest next steps.'))
const ctaLabel = computed(() => pick(props.aboutPage?.cta_button_label, 'Contact Creafynest'))
const ctaHref = computed(() => pick(props.aboutPage?.cta_button_href, '/contact'))

const pillars = computed(() => {
  const list = props.aboutPage?.pillars ?? []
  return list.length ? list : defaultPillars
})

const valueItems = computed(() => {
  const list = props.aboutPage?.value_items ?? []
  return list.length ? list : defaultValueItems
})
</script>

<template>
  <div class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <!-- Hero -->
    <section
      class="relative overflow-hidden bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] py-16 text-white sm:py-20 md:py-24"
      aria-label="About hero"
    >
      <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_70%_55%_at_50%_-10%,rgba(99,102,241,0.35),transparent)]"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:linear-gradient(to_bottom,black,transparent)]"
        aria-hidden="true"
      />

      <Container class="relative z-10">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">
          <div class="max-w-xl">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-300 sm:text-xs">
              {{ heroEyebrow }}
            </p>
            <h1
              class="mt-4 font-sans text-3xl font-bold leading-[1.12] tracking-tight text-white sm:text-4xl md:text-5xl lg:text-[2.75rem]"
            >
              {{ heroLead }}
              <span
                v-if="heroAccent"
                class="bg-gradient-to-r from-sky-300 via-indigo-200 to-violet-200 bg-clip-text text-transparent"
              >
                {{ heroAccent }}
              </span>
            </h1>
            <p class="mt-6 text-sm leading-relaxed text-slate-200/95 sm:text-base">
              {{ heroDescription }}
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-3 sm:gap-4">
              <Link
                v-if="heroPrimaryLabel"
                :href="heroPrimaryHref || '/contact'"
                class="inline-flex min-h-[2.75rem] items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#0f2a52] shadow-lg transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2a52]"
              >
                {{ heroPrimaryLabel }}
                <ArrowRight class="h-4 w-4 shrink-0" stroke-width="2.25" aria-hidden="true" />
              </Link>
              <Link
                v-if="heroSecondaryLabel"
                :href="heroSecondaryHref || '/portfolio'"
                class="inline-flex min-h-[2.75rem] items-center justify-center rounded-xl border border-white/25 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:border-white/40 hover:bg-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2a52]"
              >
                {{ heroSecondaryLabel }}
              </Link>
              <Link
                v-if="heroTertiaryLabel"
                :href="heroTertiaryHref || '/services'"
                class="text-sm font-semibold text-slate-300 underline-offset-4 transition hover:text-white hover:underline"
              >
                {{ heroTertiaryLabel }}
              </Link>
            </div>
          </div>

          <div
            class="relative rounded-3xl border border-white/15 bg-white/10 p-6 shadow-2xl shadow-black/20 backdrop-blur-md sm:p-8"
          >
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">
              {{ panelEyebrow }}
            </p>
            <p class="mt-2 max-w-md text-sm leading-relaxed text-slate-200/90">
              {{ panelDescription }}
            </p>

            <div class="relative mx-auto mt-8 w-full max-w-sm pb-4">
              <svg
                class="absolute inset-0 h-full w-full text-white/25"
                viewBox="0 0 320 260"
                aria-hidden="true"
              >
                <path
                  d="M160 128 L160 52"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-dasharray="5 4"
                  stroke-linecap="round"
                />
                <path
                  d="M160 128 L52 188"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-dasharray="5 4"
                  stroke-linecap="round"
                />
                <path
                  d="M160 128 L268 188"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-dasharray="5 4"
                  stroke-linecap="round"
                />
              </svg>

              <div class="relative aspect-[320/260] w-full">
                <div
                  class="absolute left-1/2 top-[10%] flex -translate-x-1/2 -translate-y-1/2 flex-col items-center gap-1"
                >
                  <div
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/25 bg-white/15 text-white shadow-sm"
                  >
                    <Sparkles class="h-5 w-5" stroke-width="1.75" aria-hidden="true" />
                  </div>
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    {{ panelNodeOne }}
                  </span>
                </div>

                <div
                  class="absolute left-[9%] top-[68%] flex -translate-x-1/2 -translate-y-1/2 flex-col items-center gap-1"
                >
                  <div
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/25 bg-white/10 text-white shadow-sm"
                  >
                    <UserRound class="h-5 w-5" stroke-width="1.75" aria-hidden="true" />
                  </div>
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    {{ panelNodeTwo }}
                  </span>
                </div>

                <div
                  class="absolute left-[91%] top-[68%] flex -translate-x-1/2 -translate-y-1/2 flex-col items-center gap-1"
                >
                  <div
                    class="flex h-11 w-11 items-center justify-center rounded-lg border border-white/25 bg-white/10 text-white shadow-sm"
                  >
                    <Cpu class="h-5 w-5" stroke-width="1.75" aria-hidden="true" />
                  </div>
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    {{ panelNodeThree }}
                  </span>
                </div>

                <div
                  class="absolute left-1/2 top-1/2 flex h-[4.75rem] w-[4.75rem] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center gap-0.5 rounded-full bg-gradient-to-br from-sky-500 via-indigo-500 to-violet-600 text-white shadow-lg ring-4 ring-white/20"
                >
                  <Check class="h-6 w-6 shrink-0" stroke-width="2.25" aria-hidden="true" />
                  <span class="text-[8px] font-bold uppercase tracking-[0.12em]">{{ panelCenter }}</span>
                </div>
              </div>
            </div>

            <div class="mt-6 rounded-full border border-white/15 bg-white/5 px-4 py-2.5">
              <div class="flex items-center gap-3">
                <div class="h-1.5 min-w-0 flex-1 overflow-hidden rounded-full bg-white/15">
                  <div
                    class="h-full w-[62%] rounded-full bg-gradient-to-r from-sky-400 via-indigo-400 to-violet-400"
                  />
                </div>
                <span class="shrink-0 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-300">
                  {{ panelFooter }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>

    <!-- Who we are -->
    <section class="border-t border-slate-200/90 bg-white py-14 dark:border-slate-800 dark:bg-slate-900/40 sm:py-16 md:py-20">
      <Container>
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-14 xl:gap-16">
          <div class="lg:col-span-5">
            <h2 class="font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
              {{ whoHeading }}
            </h2>
            <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
              {{ whoDescription }}
            </p>
          </div>

          <div class="lg:col-span-7">
            <ul class="grid list-none grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5" role="list">
              <li
                v-for="(item, idx) in pillars"
                :key="item.title + idx"
                class="flex flex-col rounded-2xl border border-slate-200/90 bg-slate-50/80 p-6 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/70 dark:shadow-none sm:p-7"
              >
                <div
                  class="h-12 w-12 shrink-0 rounded-xl bg-gradient-to-br shadow-md ring-1 ring-black/5 dark:ring-white/10"
                  :class="item.icon_class || 'from-slate-400 to-slate-600'"
                  aria-hidden="true"
                />
                <h3 class="mt-5 text-base font-bold leading-snug tracking-tight text-slate-900 dark:text-white">
                  {{ item.title }}
                </h3>
                <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                  {{ item.description }}
                </p>
              </li>
            </ul>
          </div>
        </div>
      </Container>
    </section>

    <!-- Why work with us -->
    <section class="border-y border-slate-200/90 bg-slate-50 py-16 dark:border-slate-800 dark:bg-slate-950 sm:py-20 md:py-24">
      <Container>
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.35rem] dark:text-white">
            {{ whyHeading }}
          </h2>
          <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
            {{ whyDescription }}
          </p>
        </div>

        <ul
          class="mx-auto mt-12 grid max-w-5xl list-none grid-cols-1 gap-5 sm:mt-14 md:grid-cols-3 md:gap-6"
          role="list"
        >
          <li
            v-for="(item, idx) in valueItems"
            :key="item.title + idx"
            class="flex flex-col rounded-2xl border border-slate-200/90 bg-white p-7 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/80 dark:shadow-none sm:p-8"
          >
            <div
              class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#0f2a52]/15 bg-[#0f2a52]/5 text-[#0f2a52] dark:border-white/20 dark:bg-white/10 dark:text-white"
              aria-hidden="true"
            >
              <component :is="lucideIcon(item.icon)" class="h-5 w-5" stroke-width="1.75" />
            </div>
            <h3 class="mt-6 text-lg font-bold tracking-tight text-slate-900 dark:text-white">
              {{ item.title }}
            </h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
              {{ item.description }}
            </p>
          </li>
        </ul>
      </Container>
    </section>

    <!-- Closing CTA -->
    <section v-if="ctaActive" class="bg-white pb-16 pt-4 dark:bg-slate-900/40">
      <Container>
        <div
          class="relative mx-auto max-w-4xl overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] px-6 py-12 text-center text-white shadow-[0_24px_60px_-20px_rgba(15,42,82,0.45)] sm:px-12 sm:py-14"
        >
          <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_60%_50%_at_50%_-10%,rgba(99,102,241,0.22),transparent)]"
            aria-hidden="true"
          />
          <div class="relative">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
              {{ ctaHeading }}
            </h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-200/90 sm:text-base">
              {{ ctaDescription }}
            </p>
            <Link
              :href="ctaHref"
              class="mt-7 inline-flex items-center justify-center rounded-lg bg-[#e11d2a] px-7 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#c81825] focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2a52]"
            >
              {{ ctaLabel }}
            </Link>
          </div>
        </div>
      </Container>
    </section>
  </div>
</template>
