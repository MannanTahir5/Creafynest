<script setup>
import Card from './Ui/Card.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  title: { type: String, required: true },
  excerpt: { type: String, default: '' },
  slug: { type: String, required: true },
  category: { type: String, default: '' },
  tech: { type: Array, default: () => [] },
})
</script>

<template>
  <Link
    :href="`/portfolio/${slug}`"
    class="block rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
  >
    <Card as="article">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p v-if="category" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
            {{ category }}
          </p>
          <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-100">
            {{ title }}
          </h3>
        </div>
        <span class="shrink-0 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200">
          View
        </span>
      </div>

      <p v-if="excerpt" class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
        {{ excerpt }}
      </p>

      <div v-if="tech?.length" class="mt-4 flex flex-wrap gap-2">
        <span
          v-for="t in tech"
          :key="t"
          class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200"
        >
          {{ t }}
        </span>
      </div>
    </Card>
  </Link>
</template>

