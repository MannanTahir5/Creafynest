<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import AiServiceCardsField from '../../../../Components/Admin/AiServiceCardsField.vue'

const props = defineProps({ section: { type: Object, required: true } })

const initial = (props.section.cards ?? []).map((c) => ({
  id: c.id ?? null,
  title: c.title ?? '',
  description: c.description ?? '',
  href: c.href ?? '',
  icon: c.icon ?? 'Sparkles',
  color_theme: c.color_theme ?? 'amber',
  image_alt: c.image_alt ?? '',
  image_remove: false,
  image_url: c.image_url ?? null,
  is_active: c.is_active !== false,
}))

const form = useForm({
  _method: 'put',
  return_to: 'home-page',
  eyebrow: props.section.eyebrow ?? '',
  heading: props.section.heading ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active !== false,
  cards: initial,
  cards_images: initial.map(() => null),
})

function submit() {
  form.transform((data) => {
    const { cards, cards_images, ...rest } = data
    return { ...rest, cards: cards.map(({ image_url, ...c }) => c), cards_images }
  }).post('/admin/home-sections/ai-services', { forceFormData: true })
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
      <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded"> Visible on home</label>
      <AiServiceCardsField v-model="form.cards" v-model:item-images="form.cards_images" :errors="form.errors" />
    </section>
    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60" :disabled="form.processing">Save AI section</button>
  </form>
</template>
