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
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
})

function submit() {
  form.put('/admin/home-sections/testimonials')
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Testimonials heading</h1>
      <Link href="/admin/home-sections" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>
    <p class="mt-1 max-w-2xl text-sm text-slate-500">
      Manage individual testimonials from the dedicated <code class="rounded bg-slate-100 px-1.5 py-0.5">Testimonials</code> sidebar entry.
    </p>

    <form class="mt-8 max-w-2xl space-y-6 rounded-xl border border-slate-200 bg-white p-5" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="ts-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow pill</label>
          <input id="ts-eyebrow" v-model="form.eyebrow" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.eyebrow }" placeholder="Clients reviews">
          <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
        </div>
        <div>
          <label for="ts-heading" class="block text-sm font-medium text-slate-700">Heading</label>
          <input id="ts-heading" v-model="form.heading" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading }" placeholder="Testimonials of our valued customers">
          <p v-if="form.errors.heading" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading }}</p>
        </div>
      </div>

      <div>
        <label for="ts-desc" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea id="ts-desc" v-model="form.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors.description }" />
        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
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
