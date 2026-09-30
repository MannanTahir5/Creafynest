<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({ section: { type: Object, required: true } })

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  eyebrow: props.section.eyebrow ?? '',
  heading: props.section.heading ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active !== false,
})

function submit() {
  form.put('/admin/home-sections/testimonials')
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />
    <p class="text-sm text-slate-600">Pick quotes under <strong>Overview → Featured testimonials</strong>. <Link href="/admin/testimonials" class="font-semibold text-indigo-600 hover:underline">Manage testimonials</Link>.</p>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
      <div><label class="block text-sm font-medium text-slate-700">Eyebrow</label><input v-model="form.eyebrow" type="text" class="form-input mt-1"></div>
      <div><label class="block text-sm font-medium text-slate-700">Heading</label><input v-model="form.heading" type="text" class="form-input mt-1"></div>
      <div><label class="block text-sm font-medium text-slate-700">Description</label><textarea v-model="form.description" rows="3" class="form-textarea mt-1" /></div>
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Visible on home</label>
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60" :disabled="form.processing">Save testimonials heading</button>
  </form>
</template>
