<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ChevronRight, ExternalLink, Save, X } from 'lucide-vue-next'
import BlogForm from '../../../Components/Admin/BlogForm.vue'

const props = defineProps({
  blog: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
})

const form = useForm({
  _method: 'put',
  title: props.blog.title,
  slug: props.blog.slug,
  content: props.blog.content,
  content_format: props.blog.content_format || 'html',
  category_id: String(props.blog.category_id),

  meta_title: props.blog.meta_title || '',
  meta_description: props.blog.meta_description || '',
  meta_keywords: props.blog.meta_keywords || '',
  og_title: props.blog.og_title || '',
  og_description: props.blog.og_description || '',
  og_image: null,
  og_image_remove: false,
  og_image_current_url: props.blog.og_image_url || null,
  noindex: !!props.blog.noindex,
  canonical_url: props.blog.canonical_url || '',
  schema_type: props.blog.schema_type || '',

  image: null,
  image_remove: false,
  image_current_url: props.blog.image_url || null,
})

const errorCount = computed(() => Object.keys(form.errors).length)
const publicUrl = computed(() => `/blog/${props.blog.slug}`)

function submit() {
  form
    .transform((data) => stripPreviewKeys(data))
    .post(`/admin/blogs/${props.blog.id}`, { forceFormData: true })
}

function stripPreviewKeys(data) {
  const { image_current_url, og_image_current_url, ...rest } = data
  return rest
}
</script>

<template>
  <div class="pb-32">
    <div class="border-b border-slate-200 pb-5">
      <nav class="flex items-center gap-1.5 text-xs text-slate-500" aria-label="Breadcrumb">
        <Link href="/admin" class="hover:text-slate-700">Admin</Link>
        <ChevronRight class="h-3 w-3" />
        <Link href="/admin/blogs" class="hover:text-slate-700">Blogs</Link>
        <ChevronRight class="h-3 w-3" />
        <span class="truncate text-slate-700">{{ props.blog.title }}</span>
      </nav>
      <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
          <div class="flex flex-wrap items-center gap-2">
            <h1 class="truncate text-2xl font-bold tracking-tight text-slate-900">
              {{ props.blog.title }}
            </h1>
            <span
              v-if="form.noindex"
              class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800"
            >
              No-index
            </span>
          </div>
          <p class="mt-1 text-sm text-slate-600">
            <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ publicUrl }}</code>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <a
            :href="publicUrl"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
          >
            <ExternalLink class="h-4 w-4" /> View post
          </a>
          <Link
            href="/admin/blogs"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
          >
            <X class="h-4 w-4" /> Close
          </Link>
        </div>
      </div>
    </div>

    <form class="mt-8" @submit.prevent="submit">
      <BlogForm :form="form" :categories="categories" is-edit />

      <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/90 px-4 py-3 shadow-[0_-10px_30px_-15px_rgba(15,23,42,0.18)] backdrop-blur sm:px-6">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
          <div class="min-w-0 text-sm">
            <p v-if="errorCount" class="font-semibold text-rose-700">
              {{ errorCount }} field{{ errorCount === 1 ? '' : 's' }} need{{ errorCount === 1 ? 's' : '' }} attention.
            </p>
            <p v-else class="text-slate-600">
              Editing <span class="font-semibold text-slate-900">{{ props.blog.title }}</span>
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link
              href="/admin/blogs"
              class="hidden rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:inline-flex"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-5 py-2 text-sm font-semibold text-white shadow-md shadow-violet-500/30 transition hover:bg-violet-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 disabled:opacity-60"
              :disabled="form.processing"
            >
              <Save class="h-4 w-4" />
              {{ form.processing ? 'Saving...' : 'Save changes' }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
