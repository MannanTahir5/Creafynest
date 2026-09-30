<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  LayoutGrid,
  LayoutTemplate,
  FileStack,
  Box,
  ShoppingCart,
  ArrowUpRight,
  Sparkles,
  Smartphone,
  TabletSmartphone,
  Share2,
  Bot,
  Mic,
  Cpu,
  Database,
  Search,
  Target,
  PenLine,
  LineChart,
} from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import { premiumServiceCategories } from '../config/premiumServicesHome.js'

const props = defineProps({
  categories: { type: Array, default: () => [] },
})

const resolvedCategories = computed(() =>
  Array.isArray(props.categories) && props.categories.length
    ? props.categories
    : premiumServiceCategories
)

const iconMap = {
  LayoutGrid,
  LayoutTemplate,
  FileStack,
  Box,
  ShoppingCart,
  Sparkles,
  Smartphone,
  TabletSmartphone,
  Share2,
  Bot,
  Mic,
  Cpu,
  Database,
  Search,
  Target,
  PenLine,
  LineChart,
}

function iconComponent(name) {
  return iconMap[name] || Box
}

function indexLabel(i) {
  return String(i + 1).padStart(2, '0')
}
</script>

<template>
  <section class="bg-white py-16 dark:bg-slate-950 sm:py-20 md:py-24">
    <Container>
      <div class="mx-auto max-w-4xl text-center">
        <div
          class="inline-flex items-center gap-2 rounded-full border border-violet-200/80 bg-violet-50/90 px-4 py-1.5 text-xs font-semibold text-indigo-900 shadow-sm dark:border-violet-500/30 dark:bg-violet-950/50 dark:text-violet-100"
        >
          <LayoutGrid class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400" stroke-width="2" />
          Services Category
        </div>

        <h2 class="font-sans mt-5 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.65rem] dark:text-white">
          Our premium
          <span
            class="bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-violet-400 dark:to-purple-400"
          >
            expertise
          </span>
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          Comprehensive digital solutions engineered for growth and scalability—organized by practice area.
        </p>
      </div>

      <div class="mx-auto mt-14 max-w-6xl space-y-12 sm:mt-16 md:space-y-14">
        <article
          v-for="(cat, ci) in resolvedCategories"
          :key="cat.slug"
          :id="cat.slug"
          class="scroll-mt-28 overflow-hidden rounded-[1.75rem] border border-violet-200/60 bg-gradient-to-br from-violet-100/95 via-violet-50/90 to-indigo-100/80 p-6 shadow-[0_20px_60px_-28px_rgba(91,33,182,0.18)] ring-1 ring-violet-200/40 dark:border-violet-500/20 dark:from-slate-900 dark:via-violet-950/50 dark:to-slate-950 dark:ring-violet-500/15 sm:rounded-[2rem] sm:p-8 lg:p-10"
        >
          <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-12 lg:items-stretch">
            <!-- Main service -->
            <div class="flex flex-col text-left">
              <div class="flex items-start justify-between gap-4">
                <div
                  class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 text-white shadow-md shadow-violet-500/25 sm:h-14 sm:w-14 sm:rounded-2xl"
                  aria-hidden="true"
                >
                  <component :is="iconComponent(cat.featureIcon)" class="h-6 w-6 sm:h-7 sm:w-7" stroke-width="2" />
                </div>
                <span
                  class="select-none text-5xl font-extralight tabular-nums leading-none text-slate-300/95 dark:text-slate-600 sm:text-6xl md:text-7xl"
                  aria-hidden="true"
                >
                  {{ indexLabel(ci) }}
                </span>
              </div>

              <h3 class="mt-6 font-sans text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                {{ cat.title }}
              </h3>
              <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
                {{ cat.subtitle }}
              </p>

              <img
                v-if="cat.image"
                :src="cat.image"
                :alt="cat.imageAlt || cat.title"
                class="mt-6 max-h-44 w-full max-w-md rounded-xl object-cover shadow-md ring-1 ring-violet-200/50 dark:ring-white/10"
                loading="lazy"
                decoding="async"
              >

              <div class="mt-auto pt-10">
                <Link
                  href="/contact"
                  class="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-700 transition hover:text-violet-900 dark:text-violet-300 dark:hover:text-violet-100"
                >
                  Explore {{ cat.title }}
                  <span aria-hidden="true">→</span>
                </Link>
              </div>
            </div>

            <!-- Sub-services grid -->
            <ul
              class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-4"
              role="list"
            >
              <li
                v-for="(item, ii) in cat.items"
                :key="item.id"
                class="group relative flex flex-col overflow-hidden rounded-2xl border bg-white shadow-[0_8px_30px_-12px_rgba(15,23,42,0.12)] transition hover:shadow-md dark:border-slate-700/80 dark:bg-slate-900/90 dark:shadow-none"
                :class="
                  ii === 0
                    ? 'border-violet-200/90 border-t-[3px] border-t-violet-600 dark:border-violet-500/40 dark:border-t-violet-500'
                    : 'border-slate-200/90 dark:border-slate-700/80'
                "
              >
                <component
                  :is="item.service_slug ? Link : 'div'"
                  :href="item.service_slug ? `/services/${item.service_slug}` : undefined"
                  class="flex flex-1 flex-col"
                  :class="
                    item.service_slug
                      ? 'cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-950'
                      : ''
                  "
                  :aria-label="item.service_slug ? `Open ${item.title} service page` : undefined"
                >
                  <div v-if="item.image" class="aspect-[16/10] w-full overflow-hidden bg-slate-50 dark:bg-slate-800">
                    <img
                      :src="item.image"
                      :alt="item.imageAlt || item.title"
                      class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                      loading="lazy"
                      decoding="async"
                    >
                  </div>
                  <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-2">
                      <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-violet-50 text-violet-700 ring-1 ring-violet-100 dark:bg-violet-950/60 dark:text-violet-200 dark:ring-violet-500/20"
                        aria-hidden="true"
                      >
                        <component :is="iconComponent(item.icon)" class="h-5 w-5" stroke-width="2" />
                      </div>
                      <span
                        class="rounded-md p-1 text-slate-300 opacity-70 transition dark:text-slate-500"
                        :class="
                          item.service_slug
                            ? 'group-hover:text-violet-600 dark:group-hover:text-violet-400'
                            : ''
                        "
                      >
                        <ArrowUpRight class="h-4 w-4 sm:h-[1.125rem] sm:w-[1.125rem]" stroke-width="2" aria-hidden="true" />
                      </span>
                    </div>
                    <h4
                      class="mt-4 text-[0.9375rem] font-bold leading-snug tracking-tight sm:text-base"
                      :class="ii === 0 ? 'text-violet-700 dark:text-violet-300' : 'text-slate-900 dark:text-white'"
                    >
                      {{ item.title }}
                    </h4>
                    <p class="mt-2 flex-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-[0.8125rem]">
                      {{ item.description }}
                    </p>
                  </div>
                </component>
              </li>
            </ul>
          </div>
        </article>
      </div>
    </Container>
  </section>
</template>
