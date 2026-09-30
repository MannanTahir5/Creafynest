<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import ImageUploadField from '../../../../Components/Admin/ImageUploadField.vue'
import HeroAvatarsField from '../../../../Components/Admin/HeroAvatarsField.vue'
import FeatureLinesField from '../../../../Components/Admin/FeatureLinesField.vue'
import GradientPicker from '../../../../Components/Admin/GradientPicker.vue'

const props = defineProps({
  hero: { type: Object, default: null },
  gradients: { type: Array, default: () => [] },
})

const initialAvatars = (props.hero?.avatars ?? []).map((a) => ({
  id: a.id ?? null,
  name: a.name ?? '',
  image_alt: a.image_alt ?? '',
  image_remove: false,
  image_url: a.image_url ?? null,
  is_active: a.is_active === undefined ? true : !!a.is_active,
}))

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  eyebrow: props.hero?.eyebrow ?? '',
  trust_count: props.hero?.trust_count ?? '',
  trust_text: props.hero?.trust_text ?? '',
  heading_line_one: props.hero?.heading_line_one ?? '',
  heading_line_two: props.hero?.heading_line_two ?? '',
  heading_gradient: props.hero?.heading_gradient ?? 'cyan-violet-fuchsia',
  description: props.hero?.description ?? '',
  feature_lines: Array.isArray(props.hero?.feature_lines) ? props.hero.feature_lines.slice() : [],
  image: null,
  image_alt: props.hero?.image_alt ?? '',
  image_remove: false,
  image_current_url: props.hero?.image_url ?? null,
  primary_cta_label: props.hero?.primary_cta_label ?? '',
  primary_cta_href: props.hero?.primary_cta_href ?? '',
  secondary_cta_label: props.hero?.secondary_cta_label ?? '',
  secondary_cta_href: props.hero?.secondary_cta_href ?? '',
  is_active: props.hero?.is_active !== false,
  avatars: initialAvatars,
  avatars_images: initialAvatars.map(() => null),
})

function submit() {
  if (!props.hero?.id) return
  form
    .transform((data) => {
      const { image_current_url, avatars, avatars_images, ...rest } = data
      return { ...rest, avatars: avatars.map(({ image_url, ...a }) => a), avatars_images }
    })
    .post(`/admin/heroes/${props.hero.id}`, { forceFormData: true })
}
</script>

<template>
  <div v-if="!hero" class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
    No hero found. <a href="/admin/heroes/create" class="font-semibold underline">Create a hero</a> first.
  </div>
  <form v-else class="space-y-6" @submit.prevent="submit">
    <AdminFormErrorBanner :form="form" />
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
      <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Hero copy</h2>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Eyebrow</label><input v-model="form.eyebrow" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Trust count</label><input v-model="form.trust_count" type="text" class="form-input mt-1"></div>
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700">Trust text</label><input v-model="form.trust_text" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading line 1</label><input v-model="form.heading_line_one" type="text" class="form-input mt-1"></div>
        <div><label class="block text-sm font-medium text-slate-700">Heading line 2</label><input v-model="form.heading_line_two" type="text" class="form-input mt-1"></div>
      </div>
      <GradientPicker v-model="form.heading_gradient" :gradients="gradients" />
      <div><label class="block text-sm font-medium text-slate-700">Description</label><textarea v-model="form.description" rows="3" class="form-textarea mt-1" /></div>
      <FeatureLinesField v-model="form.feature_lines" :errors="form.errors" />
      <ImageUploadField v-model:file="form.image" :current-url="form.image_current_url" v-model:remove-flag="form.image_remove" label="Hero image" />
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-slate-700">Primary CTA</label><input v-model="form.primary_cta_label" type="text" class="form-input mt-1"><input v-model="form.primary_cta_href" type="text" class="form-input mt-1" placeholder="/contact"></div>
        <div><label class="block text-sm font-medium text-slate-700">Secondary CTA</label><input v-model="form.secondary_cta_label" type="text" class="form-input mt-1"><input v-model="form.secondary_cta_href" type="text" class="form-input mt-1"></div>
      </div>
      <HeroAvatarsField v-model="form.avatars" v-model:item-images="form.avatars_images" :errors="form.errors" />
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Active hero on home page</label>
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60" :disabled="form.processing">Save hero</button>
  </form>
</template>
