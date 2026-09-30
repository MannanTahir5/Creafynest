<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  filters: { type: Object, default: () => ({ q: '' }) },
  subscribers: { type: Object, required: true },
})

const q = ref(props.filters.q ?? '')

function search() {
  router.get('/admin/newsletter', { q: q.value || undefined }, { preserveState: true, replace: true })
}

function destroySubscriber(id) {
  if (!confirm('Remove this subscriber?')) return
  router.delete(`/admin/newsletter/${id}`)
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Newsletter</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Subscribers who opted in from the site footer.</p>
      </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
      <input
        v-model="q"
        type="search"
        class="form-input !mt-0 sm:max-w-md"
        placeholder="Search by email…"
        @keyup.enter="search"
      >
      <button
        type="button"
        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
        @click="search"
      >
        Search
      </button>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/40">
      <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-900/80">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300">Email</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300">Confirmed</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-slate-300">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <tr v-if="!subscribers.data.length">
            <td colspan="3" class="px-4 py-12 text-center text-sm text-slate-600 dark:text-slate-400">
              No subscribers yet.
            </td>
          </tr>
          <template v-else>
            <tr v-for="s in subscribers.data" :key="s.id">
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">{{ s.email }}</td>
              <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                {{ s.confirmed_at ? new Date(s.confirmed_at).toLocaleString() : '—' }}
              </td>
              <td class="px-4 py-3 text-right">
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2 dark:text-rose-400"
                  @click="destroySubscriber(s.id)"
                >
                  Remove
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="subscribers.last_page > 1" class="mt-6 flex items-center justify-center gap-3">
      <Link
        v-if="subscribers.prev_page_url"
        :href="subscribers.prev_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
      >
        Previous
      </Link>
      <p class="text-sm text-slate-600 dark:text-slate-400">Page {{ subscribers.current_page }} / {{ subscribers.last_page }}</p>
      <Link
        v-if="subscribers.next_page_url"
        :href="subscribers.next_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
      >
        Next
      </Link>
    </div>
  </div>
</template>
