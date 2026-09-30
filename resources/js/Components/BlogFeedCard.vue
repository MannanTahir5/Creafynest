<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Tag } from 'lucide-vue-next'
import ContentImage from './ContentImage.vue'

const props = defineProps({
  title: { type: String, required: true },
  slug: { type: String, required: true },
  excerpt: { type: String, default: '' },
  date: { type: String, default: '' },
  imageUrl: { type: String, default: '' },
  imageWebpUrl: { type: String, default: '' },
  byline: { type: String, default: 'By Creafynest' },
})

const overlayTitle = computed(() => props.title.toUpperCase())
</script>

<template>
  <Link
    :href="`/blog/${slug}`"
    class="group flex h-full flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm transition hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900"
  >
    <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-900">
      <div
        v-if="imageUrl"
        class="absolute inset-0"
      >
        <ContentImage
          :src="imageUrl"
          :webp-src="imageWebpUrl"
          :alt="title"
          img-class="h-full w-full object-cover opacity-90 transition duration-500 group-hover:scale-[1.03] group-hover:opacity-100"
        />
      </div>
      <div
        v-else
        class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-black"
      />

      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/45 to-black/25" aria-hidden="true" />

      <div class="absolute left-3 top-3 z-10">
        <img
          :src="'/images/creafynest-logo.png'"
          alt=""
          class="h-8 w-auto max-w-[7rem] object-contain drop-shadow-md"
          width="120"
          height="32"
          loading="lazy"
        >
      </div>

      <div class="absolute inset-x-0 bottom-0 z-10 p-4 pt-12 sm:p-5 sm:pt-14">
        <p
          class="line-clamp-3 text-center text-[11px] font-bold leading-snug tracking-wide text-white drop-shadow-sm sm:text-xs sm:leading-tight"
        >
          {{ overlayTitle }}
        </p>
      </div>
    </div>

    <div class="flex flex-1 flex-col px-4 pb-4 pt-4 sm:px-5 sm:pb-5 sm:pt-5">
      <h3 class="text-lg font-bold leading-snug tracking-tight text-slate-900 dark:text-white sm:text-xl">
        {{ title }}
      </h3>

      <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-1 text-xs sm:text-sm">
        <span class="font-bold uppercase tracking-wide text-orange-500">{{ byline }}</span>
        <span v-if="date" class="font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ date }}</span>
      </div>

      <p
        v-if="excerpt"
        class="mt-3 line-clamp-4 flex-1 font-serif text-sm leading-relaxed text-slate-600 dark:text-slate-400"
      >
        {{ excerpt }}
      </p>

      <div class="mt-4 border-t border-slate-200 pt-3 dark:border-slate-700">
        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.15em] text-orange-500">
          <Tag class="h-3.5 w-3.5" stroke-width="2" aria-hidden="true" />
          Blog
        </span>
      </div>
    </div>
  </Link>
</template>
