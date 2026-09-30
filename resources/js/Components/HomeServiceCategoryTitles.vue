<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowUpRight, Box, LayoutGrid, Sparkles } from 'lucide-vue-next'
import Container from './Ui/Container.vue'

const props = defineProps({
  categories: { type: Array, default: () => [] },
})

const iconMap = { Box, LayoutGrid, Sparkles }

function iconComponent(name) {
  return iconMap[name] || Box
}
</script>

<template>
  <section
    v-if="props.categories.length"
    class="border-t border-slate-200/80 bg-white py-14 dark:border-slate-800 dark:bg-slate-950 sm:py-16 md:py-20"
    aria-labelledby="service-categories-heading"
  >
    <Container>
      <div class="mx-auto max-w-3xl text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-violet-700 dark:text-violet-300">Service categories</p>
        <h2 id="service-categories-heading" class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
          Explore our expertise
        </h2>
      </div>

      <div class="mx-auto mt-10 grid max-w-6xl gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <Link
          v-for="category in props.categories"
          :key="category.slug || category.title"
          :href="`/services#${category.slug}`"
          class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_12px_36px_-20px_rgba(15,23,42,0.3)] transition hover:-translate-y-1 hover:border-violet-300 hover:shadow-[0_20px_44px_-22px_rgba(109,40,217,0.35)] focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-violet-500"
        >
          <div v-if="category.image" class="aspect-[16/9] overflow-hidden bg-violet-50 dark:bg-slate-800">
            <img
              :src="category.image"
              :alt="category.imageAlt || category.title"
              class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
              loading="lazy"
              decoding="async"
            >
          </div>
          <div class="p-6">
            <div class="flex items-start justify-between gap-4">
              <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300">
                <component :is="iconComponent(category.featureIcon)" class="h-5 w-5" stroke-width="2" aria-hidden="true" />
              </span>
              <ArrowUpRight class="h-5 w-5 text-slate-400 transition group-hover:text-violet-600 dark:text-slate-500 dark:group-hover:text-violet-300" aria-hidden="true" />
            </div>
            <h3 class="mt-5 text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ category.title }}</h3>
            <p v-if="category.subtitle" class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ category.subtitle }}</p>
          </div>
        </Link>
      </div>

      <div class="mt-10 text-center sm:mt-12">
        <Link
          href="/services"
          class="inline-flex items-center gap-2 rounded-full bg-violet-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-700/20 transition hover:bg-violet-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:bg-violet-600 dark:hover:bg-violet-500 dark:focus-visible:ring-offset-slate-950"
        >
          Explore more services
          <ArrowUpRight class="h-4 w-4" aria-hidden="true" />
        </Link>
      </div>
    </Container>
  </section>
</template>
