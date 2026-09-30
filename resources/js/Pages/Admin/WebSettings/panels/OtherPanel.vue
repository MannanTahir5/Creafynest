<script setup>
import { useForm } from '@inertiajs/vue3'
import { Settings2 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  other: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  app_name: props.other.app_name ?? '',
  app_url: props.other.app_url ?? '',
  timezone: props.other.timezone ?? '',
  locale: props.other.locale ?? '',
})

function save() {
  form.put('/admin/web-settings/other', { preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <Settings2 class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Application</h2>
      </div>
      <p class="mt-2 text-sm text-slate-600">Leave empty to use environment defaults from <code class="rounded bg-slate-100 px-1 text-xs">.env</code>.</p>

      <div class="mt-6 grid max-w-2xl grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <label class="form-label" for="name">Application name</label>
          <input id="name" v-model="form.app_name" type="text" class="form-input">
          <p class="mt-1 text-xs text-slate-500">Effective: {{ other.effective.app_name }}</p>
        </div>
        <div class="md:col-span-2">
          <label class="form-label" for="url">Application URL</label>
          <input id="url" v-model="form.app_url" type="url" class="form-input" placeholder="https://example.com">
          <p class="mt-1 text-xs text-slate-500">Effective: {{ other.effective.app_url }}</p>
        </div>
        <div>
          <label class="form-label" for="tz">Timezone</label>
          <input id="tz" v-model="form.timezone" type="text" class="form-input" placeholder="UTC">
          <p class="mt-1 text-xs text-slate-500">Effective: {{ other.effective.timezone }}</p>
        </div>
        <div>
          <label class="form-label" for="locale">Locale</label>
          <input id="locale" v-model="form.locale" type="text" class="form-input" placeholder="en">
          <p class="mt-1 text-xs text-slate-500">Effective: {{ other.effective.locale }}</p>
        </div>
      </div>
    </div>
  </form>
</template>
