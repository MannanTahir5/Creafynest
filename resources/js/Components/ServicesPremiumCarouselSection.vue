<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
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
  ChevronLeft,
  ChevronRight,
} from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import { carouselBandForCategory } from '../config/serviceCategoryThemes.js'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  intro: { type: Object, default: null },
})

const resolvedCategories = computed(() =>
  Array.isArray(props.categories) && props.categories.length ? props.categories : [],
)

const introEyebrow = computed(() => props.intro?.eyebrow || 'Services Category')
const introHeadingLead = computed(() => props.intro?.heading_lead || 'Our premium')
const introHeadingHighlight = computed(() => props.intro?.heading_highlight || 'expertise')
const introDescription = computed(() =>
  props.intro?.description
    || 'Comprehensive digital solutions engineered for growth and scalability—browse by practice area.',
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

function bandClass(categorySlug) {
  return carouselBandForCategory(categorySlug)
}

/** Scroll containers keyed by category slug */
const stripRefs = ref(new Map())

function setStripRef(slug, el) {
  if (el) {
    stripRefs.value.set(slug, el)
  } else {
    stripRefs.value.delete(slug)
  }
}

function scrollStrip(slug, direction) {
  const el = stripRefs.value.get(slug)
  if (!el) return
  const delta = Math.round(Math.min(320, el.clientWidth * 0.72)) * direction
  el.scrollBy({ left: delta, behavior: 'smooth' })
}
</script>

<template>
  <section class="bg-slate-50 py-16 dark:bg-slate-950 sm:py-20 md:py-24">
    <Container>
      <div class="mx-auto max-w-4xl text-center">
        <div
          class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-xs font-semibold text-slate-800 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
        >
          <LayoutGrid class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" stroke-width="2" />
          {{ introEyebrow }}
        </div>

        <h2 class="mt-5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.65rem] dark:text-white">
          {{ introHeadingLead }}
          <span
            class="bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-violet-400 dark:to-purple-400"
          >
            {{ introHeadingHighlight }}
          </span>
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          {{ introDescription }}
        </p>
      </div>

      <div class="mt-14 space-y-16 sm:mt-16 md:space-y-20">
        <div v-for="cat in resolvedCategories" :key="cat.slug" class="scroll-mt-28" :id="cat.slug">
          <div class="mb-6 flex flex-col gap-1 px-1 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <h3 class="font-sans text-xl font-bold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                {{ cat.title }}
              </h3>
              <p v-if="cat.subtitle" class="mt-1 max-w-2xl text-sm text-slate-600 dark:text-slate-400">
                {{ cat.subtitle }}
              </p>
            </div>
            <Link
              href="/contact"
              class="mt-2 text-sm font-semibold text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 sm:mt-0"
            >
              Discuss {{ cat.title }} →
            </Link>
          </div>

          <div class="relative">
            <button
              type="button"
              class="absolute left-2 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/80 bg-white text-slate-700 shadow-lg transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 sm:left-0 sm:h-11 sm:w-11 sm:-translate-x-1/2"
              aria-label="Scroll cards left"
              @click="scrollStrip(cat.slug, -1)"
            >
              <ChevronLeft class="h-5 w-5" stroke-width="2" />
            </button>
            <button
              type="button"
              class="absolute right-2 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200/80 bg-white text-slate-700 shadow-lg transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 sm:right-0 sm:h-11 sm:w-11 sm:translate-x-1/2"
              aria-label="Scroll cards right"
              @click="scrollStrip(cat.slug, 1)"
            >
              <ChevronRight class="h-5 w-5" stroke-width="2" />
            </button>

            <div
              :ref="(el) => setStripRef(cat.slug, el)"
              class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2 pl-6 pr-6 pt-1 [-ms-overflow-style:none] [scrollbar-width:none] sm:gap-5 sm:pl-8 sm:pr-8 md:gap-6 [&::-webkit-scrollbar]:hidden"
              role="region"
              :aria-label="`${cat.title} services`"
            >
              <div
                v-for="(item, ii) in cat.items"
                :key="item.id"
                class="w-[min(78vw,15.5rem)] shrink-0 snap-start sm:w-[min(42vw,16rem)] md:w-[min(32vw,17rem)] lg:w-[min(24vw,17.5rem)]"
              >
                <component
                  :is="item.service_slug ? Link : 'div'"
                  :href="item.service_slug ? `/services/${item.service_slug}` : undefined"
                  class="flex h-full min-h-[19rem] flex-col overflow-hidden rounded-[0.65rem] border border-slate-200/90 bg-white text-left shadow-[0_12px_40px_-16px_rgba(15,23,42,0.2)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_50px_-18px_rgba(15,23,42,0.28)] dark:border-slate-700 dark:bg-slate-900 dark:shadow-none dark:hover:border-slate-600"
                  :class="
                    item.service_slug
                      ? 'cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950'
                      : ''
                "
                :aria-label="item.service_slug ? `Open ${item.title}` : undefined"
              >
                <div
                  class="flex min-h-[9.5rem] flex-1 items-center justify-center p-4 overflow-hidden"
                  :class="bandClass(cat.slug)"
                >
                  <img
                    v-if="item.image"
                    :src="item.image"
                    :alt="item.imageAlt || item.title"
                    class="h-24 w-auto max-w-full rounded-lg object-contain shadow-md"
                  />
                  <component
                    v-else
                    :is="iconComponent(item.icon)"
                    class="h-12 w-12 text-white sm:h-14 sm:w-14"
                    stroke-width="1.35"
                    aria-hidden="true"
                  />
                </div>
                <div class="flex flex-1 flex-col justify-center px-4 py-5 text-center sm:px-5">
                  <h4 class="text-[0.9375rem] font-bold leading-snug tracking-tight text-slate-900 sm:text-base dark:text-white">
                    {{ item.title }}
                  </h4>
                  <p class="mt-2 line-clamp-4 text-xs leading-relaxed text-slate-600 sm:text-[0.8125rem] dark:text-slate-400">
                    {{ item.description }}
                  </p>
                </div>
              </component>
            </div>
          </div>
        </div>
        </div>
      </div>
    </Container>
  </section>
</template>
