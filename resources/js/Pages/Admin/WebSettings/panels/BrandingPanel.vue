<script setup>
import { useForm } from '@inertiajs/vue3'
import { Image } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import ImageUploadField from '../../../../Components/Admin/ImageUploadField.vue'

const props = defineProps({
  branding: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  app_name: props.branding.app_name ?? '',
  header_logo_alt: props.branding.header_logo_alt ?? '',
  footer_logo_alt: props.branding.footer_logo_alt ?? '',
  theme_color: props.branding.theme_color ?? '',
  header_logo: null,
  header_logo_remove: false,
  footer_logo: null,
  footer_logo_remove: false,
  favicon: null,
  favicon_remove: false,
})

function save() {
  form.post('/admin/web-settings/branding', { forceFormData: true, preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <Image class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Branding &amp; media</h2>
      </div>

      <div class="mt-6 space-y-8">
        <ImageUploadField
          label="Header logo (shown in nav)"
          hint="PNG, JPG, WebP, GIF up to 5MB"
          :current-url="branding.header_logo_url"
          :file="form.header_logo"
          :remove-flag="form.header_logo_remove"
          :error="form.errors.header_logo"
          aspect="16 / 5"
          :max-bytes="5 * 1024 * 1024"
          @update:file="form.header_logo = $event"
          @update:remove-flag="form.header_logo_remove = $event"
        />

        <div>
          <label class="form-label" for="header-alt">Header logo alt text</label>
          <input id="header-alt" v-model="form.header_logo_alt" type="text" class="form-input max-w-xl">
        </div>

        <ImageUploadField
          label="Footer logo"
          hint="Used in the site footer"
          :current-url="branding.footer_logo_url"
          :file="form.footer_logo"
          :remove-flag="form.footer_logo_remove"
          :error="form.errors.footer_logo"
          aspect="16 / 5"
          :max-bytes="5 * 1024 * 1024"
          @update:file="form.footer_logo = $event"
          @update:remove-flag="form.footer_logo_remove = $event"
        />

        <div>
          <label class="form-label" for="footer-alt">Footer logo alt text</label>
          <input id="footer-alt" v-model="form.footer_logo_alt" type="text" class="form-input max-w-xl">
        </div>

        <ImageUploadField
          label="Favicon"
          hint="Square icon for browser tabs"
          :current-url="branding.favicon_url"
          :file="form.favicon"
          :remove-flag="form.favicon_remove"
          :error="form.errors.favicon"
          aspect="1 / 1"
          :max-bytes="2 * 1024 * 1024"
          @update:file="form.favicon = $event"
          @update:remove-flag="form.favicon_remove = $event"
        />

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="form-label" for="app-name">Site name</label>
            <input id="app-name" v-model="form.app_name" type="text" class="form-input">
          </div>
          <div>
            <label class="form-label" for="theme">Theme color</label>
            <input id="theme" v-model="form.theme_color" type="text" class="form-input" placeholder="#0f172a">
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
