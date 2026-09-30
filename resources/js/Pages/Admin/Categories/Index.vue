<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  categories: { type: Object, required: true },
})

function destroyCategory(id) {
  if (!confirm('Delete this category?')) return
  router.delete(`/admin/categories/${id}`)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Categories</h1>
        <p class="mt-2 text-sm text-slate-600">Blog categories and slugs.</p>
      </div>
      <Link
        href="/admin/categories/create"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        New category
      </Link>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Slug</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!categories.data.length">
            <td colspan="3" class="px-4 py-12 text-center text-sm text-slate-600">
              No categories yet. Categories power blog post grouping on the public site.
            </td>
          </tr>
          <template v-else>
            <tr v-for="c in categories.data" :key="c.id">
              <td class="px-4 py-3 font-medium text-slate-900">{{ c.name }}</td>
              <td class="px-4 py-3 text-slate-600">{{ c.slug }}</td>
              <td class="px-4 py-3 text-right">
                <Link
                  :href="`/admin/categories/${c.id}/edit`"
                  class="font-semibold text-slate-900 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                >
                  Edit
                </Link>
                <span class="mx-2 text-slate-300">|</span>
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2"
                  @click="destroyCategory(c.id)"
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
      <Link v-if="categories.next_page_url" :href="categories.next_page_url" class="text-sm font-semibold text-slate-900 hover:underline">Next</Link>
    </div>
  </div>
</template>
