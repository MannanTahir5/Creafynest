<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  testimonial: { type: Object, required: true },
})

const form = useForm({
  _method: 'put',
  name: props.testimonial.name,
  feedback: props.testimonial.feedback,
  image: null,
})

function submit() {
  form.post(`/admin/testimonials/${props.testimonial.id}`, { forceFormData: true })
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit testimonial</h1>
      <Link
        href="/admin/testimonials"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back
      </Link>
    </div>

    <form class="mt-8 max-w-2xl space-y-4" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div>
        <label for="edit-testimonial-name" class="block text-sm font-medium text-slate-700">Name</label>
        <input
          id="edit-testimonial-name"
          v-model="form.name"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.name }"
        >
        <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.name }}</p>
      </div>
      <div>
        <label for="edit-testimonial-feedback" class="block text-sm font-medium text-slate-700">Feedback</label>
        <textarea
          id="edit-testimonial-feedback"
          v-model="form.feedback"
          rows="5"
          class="form-textarea"
          :class="{ 'form-input-invalid': !!form.errors.feedback }"
        />
        <p v-if="form.errors.feedback" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.feedback }}</p>
      </div>
      <div>
        <label for="edit-testimonial-image" class="block text-sm font-medium text-slate-700">Replace image</label>
        <input
          id="edit-testimonial-image"
          type="file"
          accept="image/*"
          class="form-file"
          @change="form.image = $event.target.files[0]"
        >
        <p v-if="form.errors.image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.image }}</p>
      </div>
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
