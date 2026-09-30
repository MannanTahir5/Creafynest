<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowUpRight, Clock } from 'lucide-vue-next'
import ContentImage from './ContentImage.vue'

defineProps({
  title: { type: String, required: true },
  slug: { type: String, required: true },
  excerpt: { type: String, default: '' },
  category: { type: String, default: '' },
  date: { type: String, default: '' },
  imageUrl: { type: String, default: '' },
  imageWebpUrl: { type: String, default: '' },
  readingMinutes: { type: Number, default: 0 },
})
</script>

<template>
  <Link
    :href="`/blog/${slug}`"
    class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-slate-600"
  >
    <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
      <ContentImage
        v-if="imageUrl"
        :src="imageUrl"
        :webp-src="imageWebpUrl"
        :alt="title"
        img-class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
      />
      <div
        v-else
        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] text-xs font-semibold uppercase tracking-[0.2em] text-white/70"
      >
        Creafynest blog
      </div>
      <span
        v-if="category"
        class="absolute left-3 top-3 inline-flex items-center rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#0f2a52] shadow-sm ring-1 ring-black/5 backdrop-blur-sm"
      >
        {{ category }}
      </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
      <div class="flex items-center gap-3 text-xs font-medium text-slate-500 dark:text-slate-400">
        <span v-if="date">{{ date }}</span>
        <span v-if="date && readingMinutes" aria-hidden="true">·</span>
        <span v-if="readingMinutes" class="inline-flex items-center gap-1">
          <Clock class="h-3.5 w-3.5" stroke-width="2" aria-hidden="true" />
          {{ readingMinutes }} min read
        </span>
      </div>

      <h3
        class="mt-3 line-clamp-2 text-lg font-bold leading-snug tracking-tight text-slate-900 transition group-hover:text-[#0f2a52] dark:text-white dark:group-hover:text-white"
      >
        {{ title }}
      </h3>

      <p
        v-if="excerpt"
        class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400"
      >
        {{ excerpt }}
      </p>

      <div class="mt-auto flex items-center justify-between pt-5">
        <span class="text-sm font-semibold text-[#0f2a52] dark:text-slate-200">
          Read article
        </span>
        <span
          class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-[#0f2a52] transition group-hover:bg-[#0f2a52] group-hover:text-white dark:bg-slate-800 dark:text-slate-200"
          aria-hidden="true"
        >
          <ArrowUpRight class="h-4 w-4" stroke-width="2" />
        </span>
      </div>
    </div>
  </Link>
</template>
