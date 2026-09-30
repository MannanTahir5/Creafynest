<script setup>
import { Link } from '@inertiajs/vue3'
import ContentImage from './ContentImage.vue'

defineProps({
  indexLabel: { type: String, required: true },
  title: { type: String, required: true },
  slug: { type: String, required: true },
  excerpt: { type: String, default: '' },
  imageUrl: { type: String, default: null },
  imageWebpUrl: { type: String, default: null },
})
</script>

<template>
  <article
    class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900/40"
  >
    <div class="flex items-start justify-between gap-3 px-5 pt-5 sm:px-6 sm:pt-6">
      <span class="text-sm font-medium tabular-nums text-slate-500 dark:text-slate-400">
        {{ indexLabel }}
      </span>
    </div>

    <div class="mt-4 px-4 sm:px-5">
      <div
        v-if="imageUrl"
        class="overflow-hidden rounded-xl border border-slate-200 ring-1 ring-slate-900/[0.04] dark:border-slate-600 dark:ring-white/5"
      >
        <ContentImage
          :src="imageUrl"
          :webp-src="imageWebpUrl || ''"
          :alt="title"
          :high-priority="false"
          :lazy="true"
          img-class="aspect-[16/10] w-full object-cover"
        />
      </div>
      <div
        v-else
        class="flex aspect-[16/10] w-full items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 dark:border-slate-600 dark:bg-slate-900/60"
      >
        <span class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Preview</span>
      </div>
    </div>

    <div class="flex flex-1 flex-col px-5 pb-6 pt-5 sm:px-6">
      <h3 class="text-lg font-semibold leading-snug tracking-tight text-slate-900 dark:text-slate-100">
        {{ title }}
      </h3>
      <p v-if="excerpt" class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
        {{ excerpt }}
      </p>

      <Link
        :href="`/portfolio/${slug}`"
        class="mt-6 inline-flex w-fit items-center justify-center rounded-full border border-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-900 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:border-slate-300 dark:text-slate-100 dark:hover:bg-slate-100 dark:hover:text-slate-900 dark:focus-visible:ring-offset-slate-950"
      >
        View Full Case Study
      </Link>
    </div>
  </article>
</template>
