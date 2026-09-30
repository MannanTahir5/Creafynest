<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import TechnologiesField from '../../../Components/Admin/TechnologiesField.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

const initialTech = (props.section.technologies ?? []).map((t) => ({
  id: t.id ?? null,
  name: t.name ?? '',
  icon_slug: t.icon_slug ?? '',
  image_alt: t.image_alt ?? '',
  image_remove: false,
  image_url: t.image_url ?? null,
  row_index: Number(t.row_index ?? 1),
  is_active: t.is_active === undefined ? true : !!t.is_active,
}))

const form = useForm({
  _method: 'put',
  eyebrow: props.section.eyebrow ?? '',
  heading_lead: props.section.heading_lead ?? '',
  heading_highlight: props.section.heading_highlight ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
  technologies: initialTech,
  technologies_images: initialTech.map(() => null),
})

function submit() {
  form
    .transform((data) => {
      const { technologies, technologies_images, ...rest } = data
      return {
        ...rest,
        technologies: technologies.map(({ image_url, ...t }) => t),
        technologies_images,
      }
    })
    .post('/admin/technology-section', { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Technologies section</h1>
      <Link
        href="/admin"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back to dashboard
      </Link>
    </div>
    <p class="mt-1 max-w-2xl text-sm text-slate-500">
      Edit the heading, description, and the two scrolling technology marquee rows shown on the home page.
    </p>

    <form class="mt-8 max-w-4xl space-y-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Section heading</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label for="ts-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow pill</label>
            <input
              id="ts-eyebrow"
              v-model="form.eyebrow"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.eyebrow }"
              placeholder="Latest technologies"
            >
            <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
          </div>
          <div>
            <label for="ts-lead" class="block text-sm font-medium text-slate-700">Heading lead</label>
            <input
              id="ts-lead"
              v-model="form.heading_lead"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.heading_lead }"
              placeholder="Our core"
            >
            <p v-if="form.errors.heading_lead" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_lead }}</p>
          </div>
          <div>
            <label for="ts-highlight" class="block text-sm font-medium text-slate-700">Heading highlight</label>
            <input
              id="ts-highlight"
              v-model="form.heading_highlight"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.heading_highlight }"
              placeholder="technologies"
            >
            <p v-if="form.errors.heading_highlight" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_highlight }}</p>
          </div>
        </div>

        <div>
          <label for="ts-desc" class="block text-sm font-medium text-slate-700">Description</label>
          <textarea
            id="ts-desc"
            v-model="form.description"
            rows="3"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!form.errors.description }"
          />
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
        <div class="flex flex-wrap items-center justify-between gap-2">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Technologies</h2>
          <p class="text-xs text-slate-500">
            Use the
            <a href="https://simpleicons.org/" target="_blank" rel="noopener" class="font-semibold text-slate-700 underline">Simple Icons</a>
            slug for built-in logos. Upload a custom icon to override.
          </p>
        </div>
        <TechnologiesField
          v-model="form.technologies"
          v-model:item-images="form.technologies_images"
          :errors="form.errors"
        />
      </section>

      <button
        type="submit"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60"
        :disabled="form.processing"
      >
        Save
      </button>
    </form>
  </div>
</template>
