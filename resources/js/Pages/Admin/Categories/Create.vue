<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const form = useForm({
  name: '',
  slug: '',
})

function submit() {
  form.post('/admin/categories')
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">New category</h1>
      <Link
        href="/admin/categories"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back
      </Link>
    </div>

    <form class="mt-8 max-w-xl space-y-4" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div>
        <label for="category-name" class="block text-sm font-medium text-slate-700">Name</label>
        <input
          id="category-name"
          v-model="form.name"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.name }"
        >
        <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.name }}</p>
      </div>
      <div>
        <label for="category-slug" class="block text-sm font-medium text-slate-700">Slug (optional)</label>
        <input
          id="category-slug"
          v-model="form.slug"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.slug }"
        >
        <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
      </div>
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
