<script setup>
import { Link } from '@inertiajs/vue3'
import { ExternalLink, Plus } from 'lucide-vue-next'

defineProps({
  deliveryCategories: { type: Array, default: () => [] },
})
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-semibold text-slate-900">Service categories & cards</h2>
          <p class="mt-1 text-sm text-slate-600">Each category is a horizontal strip on the home carousel. Edit intro copy in the Overview tab.</p>
        </div>
        <Link href="/admin/delivery-categories/create" class="inline-flex items-center gap-1 rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
          <Plus class="h-4 w-4" /> New category
        </Link>
      </div>
      <ul class="mt-6 divide-y divide-slate-100">
        <li v-for="cat in deliveryCategories" :key="cat.id" class="flex flex-wrap items-center justify-between gap-3 py-4 first:pt-0">
          <div>
            <p class="font-semibold text-slate-900">{{ cat.title }}</p>
            <p class="text-xs text-slate-500">{{ cat.slug }} · {{ cat.items_count }} items · {{ cat.is_active ? 'Visible' : 'Hidden' }}</p>
          </div>
          <Link :href="cat.edit_url" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:underline">
            Edit <ExternalLink class="h-3.5 w-3.5" />
          </Link>
        </li>
      </ul>
    </section>
  </div>
</template>
