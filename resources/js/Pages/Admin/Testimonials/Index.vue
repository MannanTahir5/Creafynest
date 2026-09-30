<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  testimonials: { type: Object, required: true },
})

function destroyTestimonial(id) {
  if (!confirm('Delete this testimonial?')) return
  router.delete(`/admin/testimonials/${id}`)
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Testimonials</h1>
        <p class="mt-2 text-sm text-slate-600">Short quotes with optional portrait image.</p>
      </div>
      <Link
        href="/admin/testimonials/create"
        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        New testimonial
      </Link>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
            <th class="px-4 py-3 text-left font-semibold text-slate-700">Feedback</th>
            <th class="px-4 py-3 text-right font-semibold text-slate-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          <tr v-if="!testimonials.data.length">
            <td colspan="3" class="px-4 py-12 text-center text-sm text-slate-600">
              No testimonials yet. Add quotes to feature on the home page.
            </td>
          </tr>
          <template v-else>
            <tr v-for="t in testimonials.data" :key="t.id">
              <td class="px-4 py-3 font-medium text-slate-900">{{ t.name }}</td>
              <td class="max-w-xl px-4 py-3 text-slate-600">{{ t.feedback }}</td>
              <td class="px-4 py-3 text-right">
                <Link
                  :href="`/admin/testimonials/${t.id}/edit`"
                  class="font-semibold text-slate-900 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
                >
                  Edit
                </Link>
                <span class="mx-2 text-slate-300">|</span>
                <button
                  type="button"
                  class="font-semibold text-rose-700 underline-offset-2 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 focus-visible:ring-offset-2"
                  @click="destroyTestimonial(t.id)"
                >
                  Delete
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div v-if="testimonials.last_page > 1" class="mt-6 flex justify-center">
      <Link v-if="testimonials.next_page_url" :href="testimonials.next_page_url" class="text-sm font-semibold text-slate-900 hover:underline">Next</Link>
    </div>
  </div>
</template>
