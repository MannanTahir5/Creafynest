<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import ImageUploadField from '../../../Components/Admin/ImageUploadField.vue'
import HeroAvatarsField from '../../../Components/Admin/HeroAvatarsField.vue'
import FeatureLinesField from '../../../Components/Admin/FeatureLinesField.vue'
import GradientPicker from '../../../Components/Admin/GradientPicker.vue'

const props = defineProps({
  hero: { type: Object, required: true },
  gradients: { type: Array, default: () => [] },
})

const initialAvatars = (props.hero.avatars ?? []).map((a) => ({
  id: a.id ?? null,
  name: a.name ?? '',
  image_alt: a.image_alt ?? '',
  image_remove: false,
  image_url: a.image_url ?? null,
  is_active: a.is_active === undefined ? true : !!a.is_active,
}))

const form = useForm({
  _method: 'put',
  eyebrow: props.hero.eyebrow ?? '',
  trust_count: props.hero.trust_count ?? '',
  trust_text: props.hero.trust_text ?? '',
  heading_line_one: props.hero.heading_line_one,
  heading_line_two: props.hero.heading_line_two,
  heading_gradient: props.hero.heading_gradient ?? 'cyan-violet-fuchsia',
  description: props.hero.description,
  feature_lines: Array.isArray(props.hero.feature_lines) ? props.hero.feature_lines.slice() : [],
  image: null,
  image_alt: props.hero.image_alt ?? '',
  image_remove: false,
  image_current_url: props.hero.image_url ?? null,
  primary_cta_label: props.hero.primary_cta_label,
  primary_cta_href: props.hero.primary_cta_href,
  secondary_cta_label: props.hero.secondary_cta_label ?? '',
  secondary_cta_href: props.hero.secondary_cta_href ?? '',
  is_active: !!props.hero.is_active,
  avatars: initialAvatars,
  avatars_images: initialAvatars.map(() => null),
})

function submit() {
  form
    .transform((data) => {
      const { image_current_url, avatars, avatars_images, ...rest } = data
      return {
        ...rest,
        avatars: avatars.map(({ image_url, ...a }) => a),
        avatars_images,
      }
    })
    .post(`/admin/heroes/${props.hero.id}`, { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit hero</h1>
      <Link
        href="/admin/heroes"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back
      </Link>
    </div>

    <form class="mt-8 max-w-3xl space-y-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Header</h2>

        <div>
          <label for="edit-hero-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow (small label above)</label>
          <input
            id="edit-hero-eyebrow"
            v-model="form.eyebrow"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.eyebrow }"
          >
          <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="edit-hero-heading-one" class="block text-sm font-medium text-slate-700">Heading line 1</label>
            <input
              id="edit-hero-heading-one"
              v-model="form.heading_line_one"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.heading_line_one }"
            >
            <p v-if="form.errors.heading_line_one" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_line_one }}</p>
          </div>
          <div>
            <label for="edit-hero-heading-two" class="block text-sm font-medium text-slate-700">Heading line 2 (gradient)</label>
            <input
              id="edit-hero-heading-two"
              v-model="form.heading_line_two"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.heading_line_two }"
            >
            <p v-if="form.errors.heading_line_two" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_line_two }}</p>
          </div>
        </div>

        <GradientPicker
          v-model="form.heading_gradient"
          :options="gradients"
          :error="form.errors.heading_gradient"
        />

        <div>
          <label for="edit-hero-description" class="block text-sm font-medium text-slate-700">Description</label>
          <textarea
            id="edit-hero-description"
            v-model="form.description"
            rows="4"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!form.errors.description }"
          />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
        </div>

        <div>
          <p class="block text-sm font-medium text-slate-700">Feature bullets <span class="font-normal text-slate-400">(optional, up to 8)</span></p>
          <p class="mt-1 text-xs text-slate-500">Short benefits shown above the CTA buttons. Leave empty to hide.</p>
          <div class="mt-2">
            <FeatureLinesField
              v-model="form.feature_lines"
              :errors="form.errors"
              placeholder="e.g. Production-grade engineering"
            />
          </div>
        </div>

        <ImageUploadField
          label="Hero image"
          hint="Optional illustration shown in the home hero (PNG/WebP/SVG up to 4 MB)."
          :file="form.image"
          :current-url="form.image_current_url"
          :remove-flag="form.image_remove"
          :error="form.errors.image"
          @update:file="(v) => (form.image = v)"
          @update:remove-flag="(v) => (form.image_remove = v)"
        />

        <div>
          <label for="edit-hero-image-alt" class="block text-sm font-medium text-slate-700">Hero image alt text</label>
          <input
            id="edit-hero-image-alt"
            v-model="form.image_alt"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.image_alt }"
          >
          <p v-if="form.errors.image_alt" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.image_alt }}</p>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Trust badge</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="sm:col-span-1">
            <label for="edit-hero-trust-count" class="block text-sm font-medium text-slate-700">Count</label>
            <input
              id="edit-hero-trust-count"
              v-model="form.trust_count"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.trust_count }"
            >
            <p v-if="form.errors.trust_count" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.trust_count }}</p>
          </div>
          <div class="sm:col-span-2">
            <label for="edit-hero-trust-text" class="block text-sm font-medium text-slate-700">Trust text</label>
            <input
              id="edit-hero-trust-text"
              v-model="form.trust_text"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.trust_text }"
            >
            <p v-if="form.errors.trust_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.trust_text }}</p>
          </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
          <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Trust avatars</p>
          <p class="mt-1 text-xs text-slate-500">
            Replace the default gradient circles with real avatar images. The trust count badge appears at the end of the row.
          </p>
          <div class="mt-3">
            <HeroAvatarsField
              v-model="form.avatars"
              v-model:item-images="form.avatars_images"
              :errors="form.errors"
            />
          </div>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Primary CTA</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="edit-hero-primary-label" class="block text-sm font-medium text-slate-700">Label</label>
            <input
              id="edit-hero-primary-label"
              v-model="form.primary_cta_label"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.primary_cta_label }"
            >
            <p v-if="form.errors.primary_cta_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.primary_cta_label }}</p>
          </div>
          <div>
            <label for="edit-hero-primary-href" class="block text-sm font-medium text-slate-700">Link</label>
            <input
              id="edit-hero-primary-href"
              v-model="form.primary_cta_href"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.primary_cta_href }"
            >
            <p v-if="form.errors.primary_cta_href" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.primary_cta_href }}</p>
          </div>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Secondary CTA <span class="font-normal text-slate-400">(optional)</span></h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="edit-hero-secondary-label" class="block text-sm font-medium text-slate-700">Label</label>
            <input
              id="edit-hero-secondary-label"
              v-model="form.secondary_cta_label"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.secondary_cta_label }"
            >
            <p v-if="form.errors.secondary_cta_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.secondary_cta_label }}</p>
          </div>
          <div>
            <label for="edit-hero-secondary-href" class="block text-sm font-medium text-slate-700">Link</label>
            <input
              id="edit-hero-secondary-href"
              v-model="form.secondary_cta_href"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.secondary_cta_href }"
            >
            <p v-if="form.errors.secondary_cta_href" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.secondary_cta_href }}</p>
          </div>
        </div>
      </section>

      <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
        <input v-model="form.is_active" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
        <span class="text-sm text-slate-700">
          <span class="font-semibold text-slate-900">Make this hero live</span>
          <span class="block text-xs text-slate-500">Activating this hero will replace the currently live one.</span>
        </span>
      </label>

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
