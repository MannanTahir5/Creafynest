<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({ section: { type: Object, required: true } })

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  heading_lead: props.section.heading_lead ?? '',
  heading_accent: props.section.heading_accent ?? '',
  view_all_label: props.section.view_all_label ?? '',
  view_all_href: props.section.view_all_href ?? '',
  is_active: props.section.is_active !== false,
})

function submit() {
  form.put('/admin/home-sections/case-studies')
}
</script>

<template>
  <form class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />
    <p class="text-sm text-slate-600">Project slides are chosen under <strong>Overview → Featured case studies</strong>. <Link href="/admin/projects" class="font-semibold text-indigo-600 hover:underline">Manage projects</Link>.</p>
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Heading lead</label><input v-model="form.heading_lead" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading accent</label><input v-model="form.heading_accent" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">View all label</label><input v-model="form.view_all_label" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">View all link</label><input v-model="form.view_all_href" type="text" class="form-input mt-1"></div>
      </div>
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Visible on home</label>
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60" :disabled="form.processing">Save case studies heading</button>
  </form>
</template>
