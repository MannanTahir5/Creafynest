<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import IndustriesField from '../../../../Components/Admin/IndustriesField.vue'

const props = defineProps({ section: { type: Object, required: true } })

const initial = (props.section.industries ?? []).map((i) => ({
  id: i.id ?? null,
  title: i.title ?? '',
  icon: i.icon ?? '',
  image_alt: i.image_alt ?? '',
  image_remove: false,
  image_url: i.image_url ?? null,
  is_active: i.is_active !== false,
}))

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  eyebrow: props.section.eyebrow ?? '',
  heading_lead: props.section.heading_lead ?? '',
  heading_highlight: props.section.heading_highlight ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active !== false,
  industries: initial,
  industries_images: initial.map(() => null),
})

function submit() {
  form.transform((data) => {
    const { industries, industries_images, ...rest } = data
    return { ...rest, industries: industries.map(({ image_url, ...row }) => row), industries_images }
  }).post('/admin/home-sections/industries', { forceFormData: true })
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Eyebrow</label><input v-model="form.eyebrow" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading lead</label><input v-model="form.heading_lead" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading highlight</label><input v-model="form.heading_highlight" type="text" class="form-input mt-1"></div>
      </div>
      <div><label class="block text-sm font-medium text-slate-700">Description</label><textarea v-model="form.description" rows="3" class="form-textarea mt-1" /></div>
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Visible on home</label>
      <IndustriesField v-model="form.industries" v-model:item-images="form.industries_images" :errors="form.errors" />
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60" :disabled="form.processing">Save industries</button>
  </form>
</template>
