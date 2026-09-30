<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { ArrowDown, ArrowUp, GripVertical, Plus, Trash2 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  settings: { type: Object, required: true },
  sections: { type: Array, required: true },
  projectPicker: { type: Array, default: () => [] },
  testimonialPicker: { type: Array, default: () => [] },
  seo: { type: Object, default: () => ({}) },
  activeSection: { type: String, default: 'overview' },
})

const form = useForm({
  _method: 'put',
  active_section: props.activeSection,
  services_eyebrow: props.settings.services_eyebrow ?? '',
  services_heading_lead: props.settings.services_heading_lead ?? '',
  services_heading_highlight: props.settings.services_heading_highlight ?? '',
  services_description: props.settings.services_description ?? '',
  featured_project_slugs: [...(props.settings.featured_project_slugs ?? [])],
  featured_testimonial_ids: [...(props.settings.featured_testimonial_ids ?? [])],
  section_visibility: { ...(props.settings.section_visibility ?? {}) },
})

const sectionRows = computed(() =>
  props.sections.map((s) => ({
    ...s,
    visible: form.section_visibility[s.key] !== false,
  })),
)

function addFeaturedProject() {
  form.featured_project_slugs.push('')
}

function removeFeaturedProject(index) {
  form.featured_project_slugs.splice(index, 1)
}

function moveSlug(index, delta) {
  const target = index + delta
  if (target < 0 || target >= form.featured_project_slugs.length) return
  const [item] = form.featured_project_slugs.splice(index, 1)
  form.featured_project_slugs.splice(target, 0, item)
}

function toggleTestimonial(id) {
  const list = form.featured_testimonial_ids
  const idx = list.indexOf(id)
  if (idx === -1) {
    if (list.length < 6) list.push(id)
  } else {
    list.splice(idx, 1)
  }
}

function submit() {
  form.active_section = 'overview'
  form.put('/admin/home-page/settings')
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-base font-semibold text-slate-900">Section visibility</h2>
      <p class="mt-1 text-sm text-slate-600">Turn sections on or off for the public home page.</p>
      <ul class="mt-4 grid gap-2 sm:grid-cols-2">
        <li
          v-for="row in sectionRows"
          :key="row.key"
          class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-2.5"
        >
          <span class="text-sm font-medium text-slate-800">{{ row.label }}</span>
          <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-slate-600">
            <input
              v-model="form.section_visibility[row.key]"
              type="checkbox"
              class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            >
            {{ form.section_visibility[row.key] ? 'On' : 'Off' }}
          </label>
        </li>
      </ul>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-base font-semibold text-slate-900">Services carousel intro</h2>
      <p class="mt-1 text-sm text-slate-600">Badge, heading, and description above the category strips.</p>
      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Eyebrow</label>
          <input v-model="form.services_eyebrow" type="text" class="form-input mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Heading (lead)</label>
          <input v-model="form.services_heading_lead" type="text" class="form-input mt-1">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Heading (highlight)</label>
          <input v-model="form.services_heading_highlight" type="text" class="form-input mt-1">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Description</label>
          <textarea v-model="form.services_description" rows="3" class="form-textarea mt-1" />
        </div>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-semibold text-slate-900">Featured case studies</h2>
          <p class="mt-1 text-sm text-slate-600">Order of project slugs shown in the carousel (max 5 on the site).</p>
        </div>
        <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="addFeaturedProject">
          <Plus class="h-4 w-4" /> Add
        </button>
      </div>
      <ul class="mt-4 space-y-2">
        <li
          v-for="(slug, index) in form.featured_project_slugs"
          :key="index"
          class="flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50/60 px-2 py-2"
        >
          <GripVertical class="h-4 w-4 shrink-0 text-slate-400" aria-hidden="true" />
          <select v-model="form.featured_project_slugs[index]" class="form-input min-w-0 flex-1">
            <option value="">— Select project —</option>
            <option v-for="p in projectPicker" :key="p.id" :value="p.slug">{{ p.title }} ({{ p.slug }})</option>
          </select>
          <button type="button" class="rounded p-1 text-slate-500 hover:bg-white" :disabled="index === 0" @click="moveSlug(index, -1)">
            <ArrowUp class="h-4 w-4" />
          </button>
          <button type="button" class="rounded p-1 text-slate-500 hover:bg-white" :disabled="index >= form.featured_project_slugs.length - 1" @click="moveSlug(index, 1)">
            <ArrowDown class="h-4 w-4" />
          </button>
          <button type="button" class="rounded p-1 text-rose-600 hover:bg-rose-50" @click="removeFeaturedProject(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </li>
      </ul>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-base font-semibold text-slate-900">Featured testimonials</h2>
      <p class="mt-1 text-sm text-slate-600">Pick up to six quotes. Leave empty to show the three newest.</p>
      <ul class="mt-4 max-h-64 space-y-2 overflow-y-auto">
        <li
          v-for="t in testimonialPicker"
          :key="t.id"
          class="flex items-start gap-3 rounded-xl border px-3 py-2.5 transition"
          :class="form.featured_testimonial_ids.includes(t.id) ? 'border-indigo-200 bg-indigo-50/60' : 'border-slate-100 bg-slate-50/50'"
        >
          <input
            :id="`testimonial-${t.id}`"
            type="checkbox"
            class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600"
            :checked="form.featured_testimonial_ids.includes(t.id)"
            @change="toggleTestimonial(t.id)"
          >
          <label :for="`testimonial-${t.id}`" class="min-w-0 flex-1 cursor-pointer">
            <span class="font-medium text-slate-900">{{ t.name }}</span>
            <span v-if="t.role" class="text-slate-500"> · {{ t.role }}</span>
            <p class="mt-0.5 truncate text-xs text-slate-500">{{ t.excerpt }}</p>
          </label>
        </li>
      </ul>
    </section>

    <section v-if="seo?.edit_url" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 p-5">
      <p class="text-sm text-slate-600">
        Homepage SEO title:
        <span class="font-semibold text-slate-900">{{ seo.meta_title }}</span>
      </p>
      <a :href="seo.edit_url" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:underline">Edit SEO settings →</a>
    </section>

    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60" :disabled="form.processing">
      Save overview settings
    </button>
  </form>
</template>
