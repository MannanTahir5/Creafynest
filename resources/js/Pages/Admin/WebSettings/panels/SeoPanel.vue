<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { BarChart3 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  seo: { type: Object, required: true },
})

const pageKeys = computed(() => Object.keys(props.seo.definitions ?? {}))

const pagesInit = {}
for (const key of Object.keys(props.seo.pages ?? {})) {
  const p = props.seo.pages[key]
  pagesInit[key] = {
    title_stem: p.title_stem ?? '',
    meta_description: p.meta_description ?? '',
    meta_keywords: p.meta_keywords ?? '',
    noindex: !!p.noindex,
  }
}

const form = useForm({
  return_to: 'web-settings',
  site: {
    default_og_image_alt: props.seo.site?.default_og_image_alt ?? '',
    og_locale: props.seo.site?.og_locale ?? 'en_US',
    default_robots: props.seo.site?.default_robots ?? '',
    theme_color: props.seo.site?.theme_color ?? '',
    enable_blog_search_action: !!props.seo.site?.enable_blog_search_action,
  },
  pages: pagesInit,
  default_og_image: null,
  default_og_image_remove: false,
})

function save() {
  form.put('/admin/web-settings/seo', { forceFormData: true, preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <BarChart3 class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">SEO defaults</h2>
      </div>

      <div class="mt-6 grid max-w-3xl grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="form-label" for="locale">OG locale</label>
          <input id="locale" v-model="form.site.og_locale" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" for="robots">Default robots</label>
          <input id="robots" v-model="form.site.default_robots" type="text" class="form-input font-mono text-sm">
        </div>
        <div class="md:col-span-2">
          <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input v-model="form.site.enable_blog_search_action" type="checkbox" class="rounded border-slate-300">
            Enable blog search JSON-LD
          </label>
        </div>
        <div v-if="seo.site?.default_og_image_url" class="md:col-span-2">
          <img :src="seo.site.default_og_image_url" alt="" class="max-h-28 rounded-lg border border-slate-200">
        </div>
        <div class="md:col-span-2">
          <label class="form-label">Default OG image</label>
          <input type="file" accept="image/*" class="text-sm" @change="form.default_og_image = $event.target.files?.[0] ?? null">
        </div>
      </div>
    </div>

    <div v-for="key in pageKeys" :key="key" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-wrap items-baseline justify-between gap-2">
        <h3 class="text-base font-semibold text-slate-900">{{ seo.definitions[key].label }}</h3>
        <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ seo.definitions[key].path }}</code>
      </div>
      <div class="mt-4 grid gap-4">
        <div>
          <label class="form-label" :for="`title-${key}`">Title stem</label>
          <input :id="`title-${key}`" v-model="form.pages[key].title_stem" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" :for="`desc-${key}`">Meta description</label>
          <textarea :id="`desc-${key}`" v-model="form.pages[key].meta_description" rows="2" class="form-textarea" />
        </div>
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
          <input v-model="form.pages[key].noindex" type="checkbox" class="rounded border-slate-300">
          Noindex this page
        </label>
      </div>
    </div>
  </form>
</template>
