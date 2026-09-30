<script setup>
import { computed, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight, ExternalLink, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next'
import AdminPageHeader from '../../../Components/Admin/AdminPageHeader.vue'

const props = defineProps({
  services: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({ category: null, q: '' }) },
})

const search = ref(props.filters.q ?? '')
const categoryFilter = ref(props.filters.category ?? '')

let debounceId = null
watch(search, (next) => {
  if (debounceId) clearTimeout(debounceId)
  debounceId = setTimeout(() => {
    router.get(
      '/admin/services',
      {
        ...(categoryFilter.value ? { category: categoryFilter.value } : {}),
        ...(next ? { q: next } : {}),
      },
      { preserveScroll: true, preserveState: true, replace: true },
    )
  }, 250)
})

function changeCategory(event) {
  categoryFilter.value = event.target.value
  router.get(
    '/admin/services',
    {
      ...(categoryFilter.value ? { category: categoryFilter.value } : {}),
      ...(search.value ? { q: search.value } : {}),
    },
    { preserveScroll: true, preserveState: true },
  )
}

function destroyService(id, title) {
  if (!confirm(`Delete service “${title}” and its uploaded images? This cannot be undone.`)) return
  router.delete(`/admin/services/${id}`, { preserveScroll: true })
}

const totalLabel = computed(() => {
  const meta = props.services
  const total = meta.total ?? meta.data?.length ?? 0
  return `${total} service${total === 1 ? '' : 's'}`
})
</script>

<template>
  <div class="space-y-6">
    <AdminPageHeader
      :title="'Services'"
      :badge="totalLabel"
      subtitle="Each service belongs to a category and renders a public page at /services/slug."
    >
      <template #actions>
        <Link href="/admin/services/create" class="admin-btn-primary">
          <Plus class="h-4 w-4" /> New service
        </Link>
      </template>
    </AdminPageHeader>

    <div class="flex flex-wrap items-center gap-3">
      <div class="relative min-w-[16rem] flex-1 max-w-md">
        <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <input
          v-model="search"
          type="search"
          class="form-input pl-9"
          placeholder="Search by title or slug..."
        >
      </div>
      <div>
        <label for="filter-category" class="sr-only">Filter by category</label>
        <select
          id="filter-category"
          class="form-select"
          :value="categoryFilter"
          @change="changeCategory"
        >
          <option value="">All categories</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.title }}</option>
        </select>
      </div>
    </div>

    <div class="admin-table-wrap overflow-x-auto">
      <table class="admin-table min-w-[56rem]">
        <thead>
          <tr>
            <th>Service</th>
            <th>Category</th>
            <th>Slug</th>
            <th>Status</th>
            <th>SEO</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!services.data.length">
            <td colspan="6" class="px-4 py-16 text-center text-sm text-slate-600">
              <p class="font-semibold text-slate-900">No services match your filters.</p>
              <p class="mt-1 text-xs text-slate-500">Try clearing the search or switching categories.</p>
            </td>
          </tr>
          <tr v-for="s in services.data" v-else :key="s.id" class="transition hover:bg-slate-50/50">
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="flex h-12 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                  <img v-if="s.hero_image_url" :src="s.hero_image_url" alt="" class="h-full w-full object-cover">
                  <span v-else class="text-[10px] uppercase tracking-wide text-slate-400">no img</span>
                </div>
                <div class="min-w-0">
                  <p class="truncate font-semibold text-slate-900">{{ s.title }}</p>
                  <p class="text-[11px] text-slate-500">Order #{{ s.sort_order }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-slate-600">{{ s.category?.title || '—' }}</td>
            <td class="px-4 py-3 text-slate-600">
              <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ s.slug }}</code>
            </td>
            <td class="px-4 py-3">
              <span
                v-if="s.is_active"
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200"
              >
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" /> Live
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-200"
              >
                Hidden
              </span>
              <span
                v-if="s.noindex"
                class="ml-1 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 ring-1 ring-inset ring-amber-200"
                title="noindex"
              >
                noindex
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap items-center gap-1">
                <span
                  v-if="s.has_meta_title"
                  class="inline-flex h-5 items-center rounded-md bg-emerald-50 px-1.5 text-[10px] font-bold uppercase text-emerald-700 ring-1 ring-emerald-200"
                  title="Has SEO title"
                >Title</span>
                <span
                  v-if="s.has_meta_description"
                  class="inline-flex h-5 items-center rounded-md bg-emerald-50 px-1.5 text-[10px] font-bold uppercase text-emerald-700 ring-1 ring-emerald-200"
                  title="Has meta description"
                >Desc</span>
                <span
                  v-if="s.has_og_image"
                  class="inline-flex h-5 items-center rounded-md bg-violet-50 px-1.5 text-[10px] font-bold uppercase text-violet-700 ring-1 ring-violet-200"
                  title="Has OG image"
                >OG</span>
                <span
                  v-if="!s.has_meta_title && !s.has_meta_description && !s.has_og_image"
                  class="text-[11px] text-slate-400"
                >—</span>
              </div>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-1">
                <a
                  v-if="s.is_active"
                  :href="`/services/${s.slug}`"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                  title="View public page"
                >
                  <ExternalLink class="h-4 w-4" />
                </a>
                <Link
                  :href="`/admin/services/${s.id}/edit`"
                  class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-violet-50 hover:text-violet-700"
                  title="Edit"
                >
                  <Pencil class="h-4 w-4" />
                </Link>
                <button
                  type="button"
                  class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-500 hover:bg-rose-50 hover:text-rose-700"
                  title="Delete"
                  @click="destroyService(s.id, s.title)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="services.last_page > 1" class="flex items-center justify-between">
      <p class="text-xs text-slate-500">
        Page {{ services.current_page }} of {{ services.last_page }}
      </p>
      <div class="flex items-center gap-2">
        <Link
          v-if="services.prev_page_url"
          :href="services.prev_page_url"
          class="admin-btn-secondary px-3 py-1.5 text-xs"
        >
          <ChevronLeft class="h-3.5 w-3.5" /> Prev
        </Link>
        <Link
          v-if="services.next_page_url"
          :href="services.next_page_url"
          class="admin-btn-secondary px-3 py-1.5 text-xs"
        >
          Next <ChevronRight class="h-3.5 w-3.5" />
        </Link>
      </div>
    </div>
  </div>
</template>
