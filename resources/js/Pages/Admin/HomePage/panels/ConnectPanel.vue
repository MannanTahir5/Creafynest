<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import OptionsRepeaterField from '../../../../Components/Admin/OptionsRepeaterField.vue'

const props = defineProps({ section: { type: Object, required: true } })

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  eyebrow: props.section.eyebrow ?? '',
  heading: props.section.heading ?? '',
  description: props.section.description ?? '',
  submit_label: props.section.submit_label ?? '',
  how_found_label: props.section.how_found_label ?? '',
  idea_label: props.section.idea_label ?? '',
  timeline_options: [...(props.section.timeline_options ?? [])],
  service_options: [...(props.section.service_options ?? [])],
  is_active: props.section.is_active !== false,
})

function submit() {
  form.put('/admin/home-sections/connect')
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Eyebrow</label><input v-model="form.eyebrow" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading</label><input v-model="form.heading" type="text" class="form-input mt-1"></div>
      </div>
      <div><label class="block text-sm font-medium text-slate-700">Description</label><textarea v-model="form.description" rows="3" class="form-textarea mt-1" /></div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Submit button</label><input v-model="form.submit_label" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">“How did you find us” label</label><input v-model="form.how_found_label" type="text" class="form-input mt-1" placeholder="How did you find us?"></div>
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700">Idea field label</label><input v-model="form.idea_label" type="text" class="form-input mt-1" placeholder="Tell us about your idea"></div>
      </div>
      <div>
        <p class="text-sm font-medium text-slate-700">Timeline options</p>
        <OptionsRepeaterField v-model="form.timeline_options" class="mt-2" />
      </div>
      <div>
        <p class="text-sm font-medium text-slate-700">Service type options</p>
        <OptionsRepeaterField v-model="form.service_options" class="mt-2" />
      </div>
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Visible on home</label>
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60" :disabled="form.processing">Save connect section</button>
  </form>
</template>
