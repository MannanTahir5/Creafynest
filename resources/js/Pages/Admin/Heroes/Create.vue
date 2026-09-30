<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import ImageUploadField from '../../../Components/Admin/ImageUploadField.vue'
import HeroAvatarsField from '../../../Components/Admin/HeroAvatarsField.vue'
import FeatureLinesField from '../../../Components/Admin/FeatureLinesField.vue'
import GradientPicker from '../../../Components/Admin/GradientPicker.vue'

defineProps({
  gradients: { type: Array, default: () => [] },
})

const form = useForm({
  eyebrow: 'Premium digital solutions',
  trust_count: '200+',
  trust_text: 'Trusted by teams shipping real products',
  heading_line_one: 'Elevate your corporate',
  heading_line_two: 'digital presence',
  heading_gradient: 'cyan-violet-fuchsia',
  description:
    'We deliver premium web, mobile, and AI solutions tailored for the modern enterprise. Innovation meets elegance.',
  feature_lines: [],
  image: null,
  image_alt: '',
  image_remove: false,
  image_current_url: null,
  primary_cta_label: 'Our services',
  primary_cta_href: '/services',
  secondary_cta_label: 'Contact us',
  secondary_cta_href: '/contact',
  is_active: true,
  avatars: [],
  avatars_images: [],
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
    .post('/admin/heroes', { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">New hero</h1>
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
          <label for="hero-eyebrow" class="block text-sm font-medium text-slate-700">Eyebrow (small label above)</label>
          <input
            id="hero-eyebrow"
            v-model="form.eyebrow"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.eyebrow }"
            placeholder="Premium digital solutions"
          >
          <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.eyebrow }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-heading-one" class="block text-sm font-medium text-slate-700">Heading line 1</label>
            <input
              id="hero-heading-one"
              v-model="form.heading_line_one"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.heading_line_one }"
            >
            <p v-if="form.errors.heading_line_one" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.heading_line_one }}</p>
          </div>
          <div>
            <label for="hero-heading-two" class="block text-sm font-medium text-slate-700">Heading line 2 (gradient)</label>
            <input
              id="hero-heading-two"
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
          <label for="hero-description" class="block text-sm font-medium text-slate-700">Description</label>
          <textarea
            id="hero-description"
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
          <label for="hero-image-alt" class="block text-sm font-medium text-slate-700">Hero image alt text</label>
          <input
            id="hero-image-alt"
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
            <label for="hero-trust-count" class="block text-sm font-medium text-slate-700">Count</label>
            <input
              id="hero-trust-count"
              v-model="form.trust_count"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.trust_count }"
              placeholder="200+"
            >
            <p v-if="form.errors.trust_count" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.trust_count }}</p>
          </div>
          <div class="sm:col-span-2">
            <label for="hero-trust-text" class="block text-sm font-medium text-slate-700">Trust text</label>
            <input
              id="hero-trust-text"
              v-model="form.trust_text"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.trust_text }"
              placeholder="Trusted by teams shipping real products"
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
            <label for="hero-primary-label" class="block text-sm font-medium text-slate-700">Label</label>
            <input
              id="hero-primary-label"
              v-model="form.primary_cta_label"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.primary_cta_label }"
            >
            <p v-if="form.errors.primary_cta_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.primary_cta_label }}</p>
          </div>
          <div>
            <label for="hero-primary-href" class="block text-sm font-medium text-slate-700">Link</label>
            <input
              id="hero-primary-href"
              v-model="form.primary_cta_href"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.primary_cta_href }"
              placeholder="/services"
            >
            <p v-if="form.errors.primary_cta_href" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.primary_cta_href }}</p>
          </div>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Secondary CTA <span class="font-normal text-slate-400">(optional)</span></h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-secondary-label" class="block text-sm font-medium text-slate-700">Label</label>
            <input
              id="hero-secondary-label"
              v-model="form.secondary_cta_label"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.secondary_cta_label }"
            >
            <p v-if="form.errors.secondary_cta_label" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.secondary_cta_label }}</p>
          </div>
          <div>
            <label for="hero-secondary-href" class="block text-sm font-medium text-slate-700">Link</label>
            <input
              id="hero-secondary-href"
              v-model="form.secondary_cta_href"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.secondary_cta_href }"
              placeholder="/contact"
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
        Create
      </button>
    </form>
  </div>
</template>
