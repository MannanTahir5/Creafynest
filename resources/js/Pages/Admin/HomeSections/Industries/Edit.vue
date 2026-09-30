<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import IndustriesField from '../../../../Components/Admin/IndustriesField.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

const initial = (props.section.industries ?? []).map((i) => ({
  id: i.id ?? null,
  title: i.title ?? '',
  icon: i.icon ?? '',
  image_alt: i.image_alt ?? '',
  image_remove: false,
  image_url: i.image_url ?? null,
  is_active: i.is_active === undefined ? true : !!i.is_active,
}))

const form = useForm({
  _method: 'put',
  eyebrow: props.section.eyebrow ?? '',
  heading_lead: props.section.heading_lead ?? '',
  heading_highlight: props.section.heading_highlight ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
  industries: initial,
  industries_images: initial.map(() => null),
})

function submit() {
  form
    .transform((data) => {
      const { industries, industries_images, ...rest } = data
      return { ...rest, industries: industries.map(({ image_url, ...i }) => i), industries_images }
    })
    .post('/admin/home-sections/industries', { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Industries section</h1>
      <Link href="/admin/home-sections" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>

    <form class="mt-8 max-w-4xl space-y-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Section heading</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label for="ind-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow</label>
            <input id="ind-eyebrow" v-model="form.eyebrow" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.eyebrow }" placeholder="Who we serve">
            <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
          </div>
          <div>
            <label for="ind-lead" class="block text-sm font-medium text-slate-700">Heading lead</label>
            <input id="ind-lead" v-model="form.heading_lead" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading_lead }" placeholder="Industries">
            <p v-if="form.errors.heading_lead" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_lead }}</p>
          </div>
          <div>
            <label for="ind-highlight" class="block text-sm font-medium text-slate-700">Heading highlight</label>
            <input id="ind-highlight" v-model="form.heading_highlight" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading_highlight }" placeholder="We Transform">
            <p v-if="form.errors.heading_highlight" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_highlight }}</p>
          </div>
        </div>

        <div>
          <label for="ind-desc" class="block text-sm font-medium text-slate-700">Description</label>
          <textarea id="ind-desc" v-model="form.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors.description }" />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
        </div>

        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3">
          <input v-model="form.is_active" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
          <span class="text-sm text-slate-700">
            <span class="font-semibold text-slate-900">Visible</span>
            <span class="block text-xs text-slate-500">Show this section on the home page.</span>
          </span>
        </label>
      </section>

      <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Industries</h2>
          <p class="text-xs text-slate-500">Use any <a href="https://lucide.dev/icons" target="_blank" rel="noopener" class="font-semibold text-slate-700 underline">Lucide</a> icon name (PascalCase).</p>
        </div>
        <IndustriesField v-model="form.industries" :errors="form.errors" />
      </section>

      <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60" :disabled="form.processing">
        Save
      </button>
    </form>
  </div>
</template>
