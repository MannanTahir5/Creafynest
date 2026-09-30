<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

const form = useForm({
  _method: 'put',
  heading_lead: props.section.heading_lead ?? '',
  heading_accent: props.section.heading_accent ?? '',
  view_all_label: props.section.view_all_label ?? '',
  view_all_href: props.section.view_all_href ?? '',
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
})

function submit() {
  form.put('/admin/home-sections/case-studies')
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Case studies heading</h1>
      <Link href="/admin/home-sections" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>

    <form class="mt-8 max-w-2xl space-y-6 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="cs-lead" class="block text-sm font-medium text-slate-700">Heading lead</label>
          <input id="cs-lead" v-model="form.heading_lead" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading_lead }" placeholder="Success stories &">
          <p v-if="form.errors.heading_lead" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_lead }}</p>
        </div>
        <div>
          <label for="cs-accent" class="block text-sm font-medium text-slate-700">Heading accent (violet)</label>
          <input id="cs-accent" v-model="form.heading_accent" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading_accent }" placeholder="case studies">
          <p v-if="form.errors.heading_accent" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_accent }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="cs-cta-label" class="block text-sm font-medium text-slate-700">View-all CTA label</label>
          <input id="cs-cta-label" v-model="form.view_all_label" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.view_all_label }" placeholder="View all case studies">
          <p v-if="form.errors.view_all_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.view_all_label }}</p>
        </div>
        <div>
          <label for="cs-cta-href" class="block text-sm font-medium text-slate-700">View-all CTA URL</label>
          <input id="cs-cta-href" v-model="form.view_all_href" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.view_all_href }" placeholder="/portfolio">
          <p v-if="form.errors.view_all_href" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.view_all_href }}</p>
        </div>
      </div>

      <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3">
        <input v-model="form.is_active" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
        <span class="text-sm text-slate-700">
          <span class="font-semibold text-slate-900">Visible</span>
          <span class="block text-xs text-slate-500">Show this section on the home page.</span>
        </span>
      </label>

      <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60" :disabled="form.processing">
        Save
      </button>
    </form>
  </div>
</template>
