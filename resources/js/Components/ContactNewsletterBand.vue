<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  heading: { type: String, default: 'Newsletter' },
  subtitle: { type: String, default: 'Get Global Updates. Subscribe Now.' },
  placeholder: { type: String, default: 'Enter your email' },
  buttonLabel: { type: String, default: 'Submit' },
})

const form = useForm({
  email: '',
})

function submit() {
  form.post('/newsletter', { preserveScroll: true })
}

const hasSuccess = computed(() => form.recentlySuccessful)
</script>

<template>
  <div
    class="relative isolate overflow-hidden border-b border-white/10 bg-[#141416] py-12 sm:py-14 lg:py-16"
    aria-labelledby="contact-newsletter-heading"
  >
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_90%_80%_at_50%_30%,rgba(255,255,255,0.04),transparent_55%)]"
      aria-hidden="true"
    />

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between lg:gap-16 xl:gap-24"
      >
        <div class="max-w-lg shrink-0 lg:max-w-xl">
          <h2
            id="contact-newsletter-heading"
            class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-[2.35rem] lg:leading-tight"
          >
            {{ heading }}
          </h2>
          <p class="mt-3 text-base font-normal leading-snug text-slate-400 sm:text-lg">
            {{ subtitle }}
          </p>
        </div>

        <div class="w-full min-w-0 lg:max-w-xl xl:max-w-2xl lg:flex-1">
          <form
            class="flex flex-col gap-2 rounded-[2rem] bg-white p-2 shadow-lg ring-1 ring-black/5 sm:flex-row sm:items-center sm:gap-0 sm:rounded-[999px] sm:p-1.5 sm:pl-5 sm:pr-1.5 sm:pt-1.5 sm:pb-1.5"
            @submit.prevent="submit"
          >
            <label class="sr-only" for="contact-newsletter-email">Email</label>
            <input
              id="contact-newsletter-email"
              v-model="form.email"
              type="email"
              name="email"
              autocomplete="email"
              required
              :placeholder="placeholder"
              class="min-h-[2.75rem] w-full flex-1 border-0 bg-transparent px-4 text-neutral-900 placeholder:text-[#A0A0A0] focus:outline-none focus:ring-0 sm:min-h-0 sm:px-2 sm:py-2"
              :class="{ 'ring-2 ring-rose-400 ring-offset-0 rounded-2xl sm:rounded-none sm:rounded-l-full': !!form.errors.email }"
            >
            <button
              type="submit"
              class="inline-flex min-h-[2.75rem] w-full shrink-0 items-center justify-center rounded-[999px] bg-[#141416] px-8 text-sm font-semibold text-white shadow-md ring-1 ring-white/10 transition hover:bg-neutral-900 hover:ring-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[#141416] disabled:opacity-60 sm:w-auto sm:min-h-[2.5rem]"
              :disabled="form.processing"
            >
              <span v-if="form.processing">…</span>
              <span v-else>{{ buttonLabel }}</span>
            </button>
          </form>
          <p v-if="form.errors.email" class="mt-2 text-sm text-rose-300" role="alert">
            {{ form.errors.email }}
          </p>
          <p v-if="hasSuccess" class="mt-2 text-sm font-medium text-emerald-400">
            Thanks — you are subscribed.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
