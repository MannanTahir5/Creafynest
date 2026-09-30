<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import DeliveryItemsField from '../../../Components/Admin/DeliveryItemsField.vue'
import ImageUploadField from '../../../Components/Admin/ImageUploadField.vue'

const props = defineProps({
  category: { type: Object, required: true },
})

const initialItems = (props.category.items ?? []).map((i) => ({
  id: i.id ?? null,
  title: i.title,
  description: i.description,
  icon: i.icon,
  image_alt: i.image_alt ?? '',
  image_remove: false,
  image_url: i.image_url ?? null,
}))

const form = useForm({
  _method: 'put',
  slug: props.category.slug ?? '',
  title: props.category.title,
  subtitle: props.category.subtitle,
  feature_icon: props.category.feature_icon,
  image: null,
  image_alt: props.category.image_alt ?? '',
  image_remove: false,
  image_current_url: props.category.image_url ?? null,
  sort_order: props.category.sort_order ?? 0,
  is_active: !!props.category.is_active,
  items: initialItems,
  items_images: initialItems.map(() => null),
})

function submit() {
  form
    .transform((data) => {
      const { image_current_url, items, items_images, ...rest } = data
      return {
        ...rest,
        items: items.map(({ image_url, ...item }) => item),
        items_images,
      }
    })
    .post(`/admin/delivery-categories/${props.category.id}`, { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit service category</h1>
      <Link
        href="/admin/delivery-categories"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back
      </Link>
    </div>

    <form class="mt-8 max-w-3xl space-y-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <section class="space-y-4">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Category</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="edit-cat-title" class="block text-sm font-medium text-slate-700">Title</label>
            <input
              id="edit-cat-title"
              v-model="form.title"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.title }"
            >
            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.title }}</p>
          </div>
          <div>
            <label for="edit-cat-slug" class="block text-sm font-medium text-slate-700">
              Slug <span class="font-normal text-slate-400">(used for #anchor links)</span>
            </label>
            <input
              id="edit-cat-slug"
              v-model="form.slug"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.slug }"
            >
            <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
          </div>
        </div>

        <div>
          <label for="edit-cat-subtitle" class="block text-sm font-medium text-slate-700">Subtitle</label>
          <textarea
            id="edit-cat-subtitle"
            v-model="form.subtitle"
            rows="2"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!form.errors.subtitle }"
          />
          <p v-if="form.errors.subtitle" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.subtitle }}</p>
        </div>

        <ImageUploadField
          label="Category image"
          hint="Optional banner used on the home page card and dedicated pages."
          :file="form.image"
          :current-url="form.image_current_url"
          :remove-flag="form.image_remove"
          :error="form.errors.image"
          @update:file="(v) => (form.image = v)"
          @update:remove-flag="(v) => (form.image_remove = v)"
        />

        <div>
          <label for="edit-cat-image-alt" class="block text-sm font-medium text-slate-700">Category image alt text</label>
          <input
            id="edit-cat-image-alt"
            v-model="form.image_alt"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.image_alt }"
          >
          <p v-if="form.errors.image_alt" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.image_alt }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="sm:col-span-1">
            <label for="edit-cat-icon" class="block text-sm font-medium text-slate-700">Feature icon (Lucide)</label>
            <input
              id="edit-cat-icon"
              v-model="form.feature_icon"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.feature_icon }"
            >
            <p v-if="form.errors.feature_icon" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.feature_icon }}</p>
          </div>
          <div class="sm:col-span-1">
            <label for="edit-cat-sort" class="block text-sm font-medium text-slate-700">Sort order</label>
            <input
              id="edit-cat-sort"
              v-model.number="form.sort_order"
              type="number"
              min="0"
              max="1000"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.sort_order }"
            >
            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.sort_order }}</p>
          </div>
          <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 sm:col-span-1">
            <input v-model="form.is_active" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            <span class="text-sm text-slate-700">
              <span class="font-semibold text-slate-900">Visible</span>
              <span class="block text-xs text-slate-500">Show on the home page.</span>
            </span>
          </label>
        </div>
      </section>

      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Items</h2>
          <p class="text-xs text-slate-500">First item is highlighted on the public page.</p>
        </div>
        <DeliveryItemsField
          v-model="form.items"
          v-model:item-images="form.items_images"
          :errors="form.errors"
        />
      </section>

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
