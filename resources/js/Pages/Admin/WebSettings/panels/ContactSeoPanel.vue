<script setup>
import { useForm } from '@inertiajs/vue3'
import { MapPinned } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  contactSeo: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  title_stem: props.contactSeo.title_stem ?? '',
  meta_description: props.contactSeo.meta_description ?? '',
  meta_keywords: props.contactSeo.meta_keywords ?? '',
  og_title: props.contactSeo.og_title ?? '',
  og_description: props.contactSeo.og_description ?? '',
  og_image_alt: props.contactSeo.og_image_alt ?? '',
  canonical_url: props.contactSeo.canonical_url ?? '',
  noindex: !!props.contactSeo.noindex,
  robots: props.contactSeo.robots ?? '',
  og_image: null,
  og_image_remove: false,
})

function save() {
  form.put('/admin/web-settings/contact-seo', { forceFormData: true, preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <MapPinned class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Contact page SEO</h2>
      </div>
      <p class="mt-2 text-sm text-slate-600">Meta tags for <code class="rounded bg-slate-100 px-1 text-xs">/contact</code>.</p>

      <div class="mt-6 max-w-2xl space-y-4">
        <div>
          <label class="form-label" for="title">Title stem</label>
          <input id="title" v-model="form.title_stem" type="text" class="form-input" placeholder="Contact us">
        </div>
        <div>
          <label class="form-label" for="desc">Meta description</label>
          <textarea id="desc" v-model="form.meta_description" rows="3" class="form-textarea" />
        </div>
        <div>
          <label class="form-label" for="kw">Meta keywords</label>
          <input id="kw" v-model="form.meta_keywords" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" for="og-title">Open Graph title</label>
          <input id="og-title" v-model="form.og_title" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" for="og-desc">Open Graph description</label>
          <textarea id="og-desc" v-model="form.og_description" rows="2" class="form-textarea" />
        </div>
        <div v-if="contactSeo.og_image_url" class="mt-2">
          <img :src="contactSeo.og_image_url" alt="" class="max-h-28 rounded-lg border border-slate-200">
        </div>
        <div>
          <label class="form-label">OG image</label>
          <input type="file" accept="image/*" class="text-sm" @change="form.og_image = $event.target.files?.[0] ?? null">
          <label v-if="contactSeo.og_image_url" class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600">
            <input v-model="form.og_image_remove" type="checkbox" class="rounded border-slate-300">
            Remove current image
          </label>
        </div>
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
          <input v-model="form.noindex" type="checkbox" class="rounded border-slate-300">
          Hide contact page from search engines (noindex)
        </label>
      </div>
    </div>
  </form>
</template>
