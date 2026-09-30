<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ChevronRight, ExternalLink, Save, Trash2, X } from 'lucide-vue-next'
import ServiceForm from '../../../Components/Admin/ServiceForm.vue'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  service: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
})

// JSON round-trip: Inertia props are reactive proxies; structuredClone() throws on Proxies and prevents mount (blank page).
function cloneFormTree(value) {
  return JSON.parse(JSON.stringify(value))
}

const form = useForm({
  _method: 'put',
  delivery_category_id: props.service.delivery_category_id ?? '',
  slug: props.service.slug ?? '',
  title: props.service.title,
  icon: props.service.icon ?? 'Sparkles',
  sort_order: props.service.sort_order ?? 0,
  is_active: !!props.service.is_active,
  meta_title: props.service.meta_title ?? '',
  meta_description: props.service.meta_description ?? '',
  meta_keywords: props.service.meta_keywords ?? '',
  og_title: props.service.og_title ?? '',
  og_description: props.service.og_description ?? '',
  og_image: null,
  og_image_remove: false,
  og_image_current_url: props.service.og_image_url ?? null,
  noindex: !!props.service.noindex,
  canonical_url: props.service.canonical_url ?? '',
  schema_type: props.service.schema_type ?? '',
  hero_image: null,
  hero_image_remove: false,
  hero_image_current_url: props.service.hero_image_url,
  tech_image: null,
  tech_image_remove: false,
  tech_image_current_url: props.service.tech_image_url,
  outcomes_image: null,
  outcomes_image_remove: false,
  outcomes_image_current_url: props.service.outcomes_image_url,
  snapshot_image: null,
  snapshot_image_remove: false,
  snapshot_image_current_url: props.service.snapshot_image_url,
  content: cloneFormTree(props.service.content ?? {}),
})

const errorCount = computed(() => Object.keys(form.errors).length)

/** Multipart FormData can lose or mis-merge deeply nested `content[*]` keys; use JSON unless a file is uploaded. */
function hasUploadedFile(payload) {
  const keys = ['hero_image', 'tech_image', 'outcomes_image', 'snapshot_image', 'og_image']

  return keys.some((k) => typeof File !== 'undefined' && payload[k] instanceof File)
}

function submit() {
  form
    .transform((data) => stripPreviewKeys(data))
    .post(`/admin/services/${props.service.id}`, { forceFormData: hasUploadedFile(form.data()) })
}

function stripPreviewKeys(data) {
  const {
    hero_image_current_url,
    tech_image_current_url,
    outcomes_image_current_url,
    snapshot_image_current_url,
    og_image_current_url,
    ...rest
  } = data
  return rest
}
</script>

<template>
  <div class="pb-32">
    <!-- Header / breadcrumb -->
    <div class="border-b border-slate-200 pb-5">
      <nav class="flex items-center gap-1.5 text-xs text-slate-500" aria-label="Breadcrumb">
        <Link href="/admin" class="hover:text-slate-700">Admin</Link>
        <ChevronRight class="h-3 w-3" />
        <Link href="/admin/services" class="hover:text-slate-700">Services</Link>
        <ChevronRight class="h-3 w-3" />
        <span class="truncate text-slate-700">{{ service.title }}</span>
      </nav>
      <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0">
          <h1 class="truncate text-2xl font-bold tracking-tight text-slate-900">
            {{ service.title }}
            <span
              v-if="service.is_active"
              class="ml-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 align-middle text-[10px] font-bold uppercase tracking-wider text-emerald-700 ring-1 ring-inset ring-emerald-200"
            >
              <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" /> Live
            </span>
            <span
              v-else
              class="ml-2 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 align-middle text-[10px] font-bold uppercase tracking-wider text-slate-600 ring-1 ring-inset ring-slate-200"
            >
              Hidden
            </span>
          </h1>
          <p v-if="service.slug" class="mt-1 text-sm text-slate-600">
            Public URL: <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">/services/{{ service.slug }}</code>
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <a
            v-if="service.is_active"
            :href="`/services/${service.slug}`"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
          >
            <ExternalLink class="h-4 w-4" /> View page
          </a>
          <Link
            href="/admin/services"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50"
          >
            <X class="h-4 w-4" /> Close
          </Link>
        </div>
      </div>
    </div>

    <form class="mt-8" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />
      <ServiceForm :form="form" :categories="categories" :is-edit="true" />

      <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/90 px-4 py-3 shadow-[0_-10px_30px_-15px_rgba(15,23,42,0.18)] backdrop-blur sm:px-6">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
          <div class="min-w-0 text-sm">
            <p v-if="errorCount" class="font-semibold text-rose-700">
              {{ errorCount }} field{{ errorCount === 1 ? '' : 's' }} need{{ errorCount === 1 ? 's' : '' }} attention.
            </p>
            <p v-else class="text-slate-600">
              Editing — changes will be saved to
              <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">/services/{{ form.slug }}</code>
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Link
              href="/admin/services"
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
