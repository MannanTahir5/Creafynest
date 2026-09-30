<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowRight, CheckCircle2, CircleSlash } from 'lucide-vue-next'

defineProps({
  sections: { type: Array, required: true },
})
</script>

<template>
  <div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Home sections</h1>
      <p class="mt-2 max-w-2xl text-sm text-slate-600">
        Edit the headings, copy, and items for each section of the public home page. Hero, Services Category, and Technologies have their own dedicated admin pages in the sidebar.
      </p>
    </div>

    <ul class="mt-8 grid grid-cols-1 gap-3 md:grid-cols-2">
      <li
        v-for="s in sections"
        :key="s.key"
        class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-slate-300 hover:shadow-sm"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="font-semibold text-slate-900">{{ s.name }}</p>
            <p class="mt-1 text-sm text-slate-600">{{ s.description }}</p>
            <p v-if="s.meta" class="mt-2 text-xs text-slate-500">{{ s.meta }}</p>
          </div>
          <span
            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold"
            :class="s.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-50 text-slate-500 ring-1 ring-slate-200'"
          >
            <CheckCircle2 v-if="s.is_active" class="h-3 w-3" />
            <CircleSlash v-else class="h-3 w-3" />
            {{ s.is_active ? 'Visible' : 'Hidden' }}
          </span>
        </div>
        <div class="mt-4 flex justify-end">
          <Link
            :href="s.edit_url"
            class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
          >
            Edit
            <ArrowRight class="h-3.5 w-3.5" />
          </Link>
        </div>
      </li>
    </ul>
  </div>
</template>
