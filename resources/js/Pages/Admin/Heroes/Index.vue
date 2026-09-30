<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  heroes: { type: Object, required: true },
})

function destroyHero(id) {
  if (!confirm('Delete this hero variant?')) return
  router.delete(`/admin/heroes/${id}`)
}

function activateHero(id) {
  router.post(`/admin/heroes/${id}/activate`)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Hero section</h1>
        <p class="mt-2 text-sm text-slate-600">
          Manage the home page hero. Only one variant can be live at a time — activate the one you want to show.
        </p>
      </div>
      <Link
        href="/admin/heroes/create"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        New hero
      </Link>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Heading</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Eyebrow</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!heroes.data.length">
            <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-600">
              No hero variants yet. Create one to control the home page hero.
            </td>
          </tr>
          <template v-else>
            <tr v-for="h in heroes.data" :key="h.id">
              <td class="px-4 py-3 font-medium text-slate-900">
                {{ h.heading_line_one }}
                <span class="text-slate-500">/</span>
                {{ h.heading_line_two }}
              </td>
              <td class="px-4 py-3 text-slate-600">{{ h.eyebrow || '—' }}</td>
              <td class="px-4 py-3">
                <span
                  v-if="h.is_active"
                  class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200"
                >
                  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                  Live
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200"
                >
                  Draft
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <button
                  v-if="!h.is_active"
                  type="button"
                  class="font-semibold text-emerald-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-2"
                  @click="activateHero(h.id)"
                >
                  Activate
                </button>
                <span v-if="!h.is_active" class="mx-2 text-slate-300">|</span>
                <Link
                  :href="`/admin/heroes/${h.id}/edit`"
                  class="font-semibold text-slate-900 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                >
                  Edit
                </Link>
                <span class="mx-2 text-slate-300">|</span>
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2"
                  @click="destroyHero(h.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="heroes.last_page > 1" class="mt-6 flex justify-center">
      <Link
        v-if="heroes.next_page_url"
        :href="heroes.next_page_url"
        class="text-sm font-semibold text-slate-900 hover:underline"
      >
        Next
      </Link>
    </div>
  </div>
</template>
