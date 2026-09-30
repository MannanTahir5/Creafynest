<script setup>
import { useForm } from '@inertiajs/vue3'
import { LineChart } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  tracking: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  ga4_measurement_id: props.tracking.ga4_measurement_id ?? '',
  gtm_container_id: props.tracking.gtm_container_id ?? '',
  meta_pixel_id: props.tracking.meta_pixel_id ?? '',
})

function save() {
  form.put('/admin/web-settings/tracking', { preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <LineChart class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Tracking &amp; analytics</h2>
      </div>
      <p class="mt-2 text-sm text-slate-600">Scripts are injected on every public page when IDs are set.</p>

      <div class="mt-6 max-w-xl space-y-4">
        <div>
          <label class="form-label" for="ga4">GA4 measurement ID</label>
          <input id="ga4" v-model="form.ga4_measurement_id" type="text" class="form-input" placeholder="G-XXXXXXXXXX">
          <p v-if="form.errors.ga4_measurement_id" class="mt-1 text-xs text-rose-600">{{ form.errors.ga4_measurement_id }}</p>
        </div>
        <div>
          <label class="form-label" for="gtm">Google Tag Manager container</label>
          <input id="gtm" v-model="form.gtm_container_id" type="text" class="form-input" placeholder="GTM-XXXXXXX">
          <p v-if="form.errors.gtm_container_id" class="mt-1 text-xs text-rose-600">{{ form.errors.gtm_container_id }}</p>
        </div>
        <div>
          <label class="form-label" for="pixel">Meta Pixel ID</label>
          <input id="pixel" v-model="form.meta_pixel_id" type="text" class="form-input" placeholder="1234567890">
          <p v-if="form.errors.meta_pixel_id" class="mt-1 text-xs text-rose-600">{{ form.errors.meta_pixel_id }}</p>
        </div>
      </div>
    </div>
  </form>
</template>
