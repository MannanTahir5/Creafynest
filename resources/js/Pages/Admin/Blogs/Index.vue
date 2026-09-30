<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { ExternalLink, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  filters: { type: Object, default: () => ({ q: '', category: null }) },
  blogs: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
})

const q = ref(props.filters.q ?? '')
const categoryId = ref(props.filters.category ? String(props.filters.category) : '')

let searchTimer = null
watch(q, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => apply(), 320)
})
watch(categoryId, () => apply())

function apply() {
  router.get(
    '/admin/blogs',
    {
      q: q.value || undefined,
      category: categoryId.value ? Number(categoryId.value) : undefined,
    },
    { preserveState: true, replace: true },
  )
}

function destroyBlog(id) {
  if (!confirm('Delete this blog post? This cannot be undone.')) return
  router.delete(`/admin/blogs/${id}`)
}

const totalLabel = computed(() => {
  const total = props.blogs.total ?? 0
  return `${total} post${total === 1 ? '' : 's'}`
})

function formatDate(iso) {
  if (!iso) return ''
  try {
    return new Date(iso).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return ''
  }
}
</script>

<template>
  <div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">
          Blogs
          <span class="ml-2 text-sm font-medium text-slate-500">{{ totalLabel }}</span>
        </h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Manage posts, SEO fields, and cover images.</p>
      </div>
      <Link
        href="/admin/blogs/create"
        class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-violet-500/30 transition hover:bg-violet-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2"
      >
        <Plus class="h-4 w-4" /> New post
      </Link>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative w-full sm:max-w-md">
        <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <input
          v-model="q"
          type="search"
          class="form-input !mt-0 pl-9"
          placeholder="Search by title or slug…"
        >
      </div>
      <select v-model="categoryId" class="form-select !mt-0 sm:w-56">
        <option value="">All categories</option>
        <option v-for="c in categories" :key="c.id" :value="String(c.id)">{{ c.name }}</option>
      </select>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Post</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Category</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">SEO</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Created</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!blogs.data.length">
            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-600">
              No blog posts found. Try adjusting your search or
              <Link href="/admin/blogs/create" class="font-semibold text-violet-700 hover:underline">create one</Link>.
            </td>
          </tr>
          <template v-else>
            <tr v-for="b in blogs.data" :key="b.id" class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="h-12 w-16 shrink-0 overflow-hidden rounded-md bg-gradient-to-br from-violet-100 via-slate-100 to-indigo-100 ring-1 ring-slate-200">
                    <img v-if="b.image_url" :src="b.image_url" :alt="b.title" class="h-full w-full object-cover">
                  </div>
                  <div class="min-w-0">
                    <p class="truncate font-semibold text-slate-900">{{ b.title }}</p>
                    <p class="mt-0.5 flex flex-wrap items-center gap-1.5">
                      <code class="truncate rounded bg-slate-100 px-1.5 py-0.5 text-[11px] text-slate-600">/blog/{{ b.slug }}</code>
                      <span
                        v-if="b.noindex"
                        class="inline-flex items-center rounded-full bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800"
                      >No-index</span>
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ b.category?.name || '—' }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap items-center gap-1">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                      b.has_meta_title ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500',
                    ]"
                    title="SEO title"
                  >Title</span>
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                      b.has_meta_description ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500',
                    ]"
                    title="Meta description"
                  >Desc</span>
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                      b.has_og_image ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500',
                    ]"
                    title="Open Graph image"
                  >OG</span>
                </div>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ formatDate(b.created_at) }}</td>
              <td class="px-4 py-3 text-right">
                <div class="inline-flex items-center gap-1">
                  <a
                    :href="`/blog/${b.slug}`"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                    title="View post"
                  >
                    <ExternalLink class="h-4 w-4" />
                  </a>
                  <Link
                    :href="`/admin/blogs/${b.id}/edit`"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                    title="Edit"
                  >
                    <Pencil class="h-4 w-4" />
                  </Link>
                  <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50"
                    title="Delete"
                    @click="destroyBlog(b.id)"
                  >
                    <Trash2 class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="blogs.last_page > 1" class="mt-6 flex items-center justify-center gap-3">
      <Link
        v-if="blogs.prev_page_url"
        :href="blogs.prev_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Prev
      </Link>
      <p class="text-sm text-slate-600">Page {{ blogs.current_page }} / {{ blogs.last_page }}</p>
      <Link
        v-if="blogs.next_page_url"
        :href="blogs.next_page_url"
        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Next
      </Link>
    </div>
  </div>
</template>
