<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'
import AiServiceCardsField from '../../../../Components/Admin/AiServiceCardsField.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

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
  is_active: c.is_active === undefined ? true : !!c.is_active,
}))

const form = useForm({
  _method: 'put',
  eyebrow: props.section.eyebrow ?? '',
  heading: props.section.heading ?? '',
  description: props.section.description ?? '',
  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,
  cards: initial,
  cards_images: initial.map(() => null),
})

function submit() {
  form
    .transform((data) => {
      const { cards, cards_images, ...rest } = data
      return { ...rest, cards: cards.map(({ image_url, ...c }) => c), cards_images }
    })
    .post('/admin/home-sections/ai-services', { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">AI services section</h1>
      <Link href="/admin/home-sections" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>

    <form class="mt-8 max-w-4xl space-y-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Section heading</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="ai-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow pill</label>
            <input id="ai-eyebrow" v-model="form.eyebrow" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.eyebrow }" placeholder="Our top services">
            <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
          </div>
          <div>
            <label for="ai-heading" class="block text-sm font-medium text-slate-700">Heading</label>
            <input id="ai-heading" v-model="form.heading" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.heading }" placeholder="What We Do With AI">
            <p v-if="form.errors.heading" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading }}</p>
          </div>
        </div>

        <div>
          <label for="ai-desc" class="block text-sm font-medium text-slate-700">Description</label>
          <textarea id="ai-desc" v-model="form.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors.description }" />
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
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Cards</h2>
        <AiServiceCardsField
          v-model="form.cards"
          v-model:item-images="form.cards_images"
          :errors="form.errors"
        />
      </section>

      <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60" :disabled="form.processing">
        Save
      </button>
    </form>
  </div>
</template>
