<script setup>
import { useForm } from '@inertiajs/vue3'
import { FileText } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  sitemap: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  sitemap_enabled: props.sitemap.sitemap_enabled !== false,
  robots_extra_disallow: props.sitemap.robots_extra_disallow ?? '',
})

function save() {
  form.put('/admin/web-settings/sitemap', { preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <FileText class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Sitemap &amp; robots</h2>
      </div>

      <div class="mt-4 flex flex-wrap gap-4 text-sm">
        <a :href="sitemap.sitemap_url" target="_blank" rel="noopener" class="font-semibold text-violet-700 hover:underline">{{ sitemap.sitemap_url }}</a>
        <a :href="sitemap.robots_url" target="_blank" rel="noopener" class="font-semibold text-violet-700 hover:underline">{{ sitemap.robots_url }}</a>
      </div>

      <div class="mt-6 max-w-2xl space-y-4">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
          <input v-model="form.sitemap_enabled" type="checkbox" class="rounded border-slate-300">
          Publish XML sitemap (auto-generated from live pages)
        </label>

        <div>
          <label class="form-label" for="disallow">Extra robots.txt disallow rules</label>
          <textarea
            id="disallow"
            v-model="form.robots_extra_disallow"
            rows="5"
            class="form-textarea font-mono text-sm"
            placeholder="/private&#10;/drafts"
          />
          <p class="mt-1 text-xs text-slate-500">One path per line. /admin and /login are always disallowed.</p>
        </div>
      </div>
    </div>
  </form>
</template>
