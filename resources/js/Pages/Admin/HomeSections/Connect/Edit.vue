<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

const form = useForm({
  _method: 'put',
  eyebrow: props.section.eyebrow ?? '',
  heading: props.section.heading ?? '',
  description: props.section.description ?? '',
  submit_label: props.section.submit_label ?? '',
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
})

function submit() {
  form.put('/admin/home-sections/connect')
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Let’s connect section</h1>
      <Link href="/admin/home-sections" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>
    <p class="mt-1 max-w-2xl text-sm text-slate-500">
      Edit the heading and CTA copy for the contact-form section. Form fields and pill options remain controlled by the application.
    </p>

    <form class="mt-8 max-w-2xl space-y-6 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div>
        <label for="lc-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow pill</label>
        <input id="lc-eyebrow" v-model="form.eyebrow" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.eyebrow }" placeholder="Get in touch">
        <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
      </div>

      <div>
        <label for="lc-heading" class="block text-sm font-medium text-slate-700">Heading</label>
        <input id="lc-heading" v-model="form.heading" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading }" placeholder="Let's connect">
        <p v-if="form.errors.heading" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading }}</p>
      </div>

      <div>
        <label for="lc-desc" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea id="lc-desc" v-model="form.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors.description }" />
        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
      </div>

      <div>
        <label for="lc-submit" class="block text-sm font-medium text-slate-700">Submit button label</label>
        <input id="lc-submit" v-model="form.submit_label" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.submit_label }" placeholder="Let's work together">
        <p v-if="form.errors.submit_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.submit_label }}</p>
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
