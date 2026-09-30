<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  categories: { type: Object, required: true },
})

function destroyCategory(id, title) {
  if (!confirm(`Delete category “${title}” and all its items? This cannot be undone.`)) return
  router.delete(`/admin/delivery-categories/${id}`)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Services Category</h1>
        <p class="mt-2 text-sm text-slate-600">
          Manage the home page “Services Category” cards and their line items. Order is controlled by sort order (lowest first).
        </p>
      </div>
      <Link
        href="/admin/delivery-categories/create"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        New category
      </Link>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Title</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Slug</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Items</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Order</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!categories.data.length">
            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-600">
              No categories yet. Add one to populate the home page.
            </td>
          </tr>
          <template v-else>
            <tr v-for="c in categories.data" :key="c.id">
              <td class="px-4 py-3">
                <p class="font-medium text-slate-900">{{ c.title }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ c.subtitle }}</p>
              </td>
              <td class="px-4 py-3 text-slate-600">
                <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ c.slug }}</code>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ c.items_count ?? 0 }}</td>
              <td class="px-4 py-3 tabular-nums text-slate-600">{{ c.sort_order }}</td>
              <td class="px-4 py-3">
                <span
                  v-if="c.is_active"
                  class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200"
                >
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                  Live
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200"
                >
                  Hidden
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <Link
                  :href="`/admin/delivery-categories/${c.id}/edit`"
                  class="font-semibold text-slate-900 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                >
                  Edit
                </Link>
                <span class="mx-2 text-slate-300">|</span>
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2"
                  @click="destroyCategory(c.id, c.title)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="categories.last_page > 1" class="mt-6 flex justify-center">
      <Link
        v-if="categories.next_page_url"
        :href="categories.next_page_url"
        class="text-sm font-semibold text-slate-900 hover:underline"
      >
        Next
      </Link>
    </div>
  </div>
</template>
