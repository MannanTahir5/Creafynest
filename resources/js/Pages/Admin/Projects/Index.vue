<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  filters: { type: Object, default: () => ({ q: '' }) },
  projects: { type: Object, required: true },
})

const q = ref(props.filters.q ?? '')

function search() {
  router.get('/admin/projects', { q: q.value || undefined }, { preserveState: true, replace: true })
}

function destroyProject(id) {
  if (!confirm('Delete this project?')) return
  router.delete(`/admin/projects/${id}`)
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Projects</h1>
        <p class="mt-2 text-sm text-slate-600">Manage portfolio projects and media.</p>
      </div>
      <Link
        href="/admin/projects/create"
        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        New project
      </Link>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
      <input
        v-model="q"
        type="search"
        placeholder="Search…"
        class="form-input !mt-0 sm:max-w-md"
        @keyup.enter="search"
      >
      <button
        type="button"
        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        @click="search"
      >
        Search
      </button>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Title</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Slug</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Category</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!projects.data.length">
            <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-600">
              No projects yet. Create one to get started.
            </td>
          </tr>
          <template v-else>
            <tr v-for="p in projects.data" :key="p.id">
              <td class="px-4 py-3 font-medium text-slate-900">{{ p.title }}</td>
              <td class="px-4 py-3 text-slate-600">{{ p.slug }}</td>
              <td class="px-4 py-3 text-slate-600">{{ p.category }}</td>
              <td class="px-4 py-3 text-right">
                <Link
                  :href="`/admin/projects/${p.id}/edit`"
                  class="font-semibold text-slate-900 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                >
                  Edit
                </Link>
                <span class="mx-2 text-slate-300">|</span>
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2"
                  @click="destroyProject(p.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="projects.last_page > 1" class="mt-6 flex items-center justify-center gap-3">
      <Link
        v-if="projects.prev_page_url"
        :href="projects.prev_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Previous
      </Link>
      <p class="text-sm text-slate-600">
        Page {{ projects.current_page }} / {{ projects.last_page }}
      </p>
      <Link
        v-if="projects.next_page_url"
        :href="projects.next_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Next
      </Link>
    </div>
  </div>
</template>
