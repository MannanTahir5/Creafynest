<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  /** Dark charcoal footer variant. */
  variant: { type: String, default: 'default' },
})

const form = useForm({
  email: '',
})

function submit() {
  form.post('/newsletter', { preserveScroll: true })
}

const wrapClass = computed(() =>
  props.variant === 'footer'
    ? 'rounded-xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur-sm'
    : 'rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/50',
)

const titleClass = computed(() =>
  props.variant === 'footer' ? 'text-sm font-semibold text-white' : 'text-sm font-semibold text-slate-900 dark:text-slate-100',
)

const descClass = computed(() =>
  props.variant === 'footer' ? 'mt-1 text-xs text-slate-400' : 'mt-1 text-xs text-slate-600 dark:text-slate-400',
)

const inputClass = computed(() =>
  props.variant === 'footer'
    ? 'form-input !mt-0 border-white/15 bg-white/5 text-white placeholder:text-slate-500 focus-visible:ring-violet-400/80 sm:min-w-[220px] sm:flex-1'
    : 'form-input !mt-0 sm:min-w-[220px] sm:flex-1',
)

const btnClass = computed(() =>
  props.variant === 'footer'
    ? 'inline-flex shrink-0 items-center justify-center rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-900/30 transition hover:from-violet-500 hover:to-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#141416] disabled:opacity-60'
    : 'inline-flex shrink-0 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200',
)
</script>

<template>
  <div :class="wrapClass">
    <p :class="titleClass">Newsletter</p>
    <p :class="descClass">
      Occasional updates on new posts and projects. Unsubscribe any time from the link in emails (when enabled).
    </p>
    <form class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-stretch" @submit.prevent="submit">
      <label class="sr-only" for="newsletter-email">Email</label>
      <input
        id="newsletter-email"
        v-model="form.email"
        type="email"
        name="email"
        autocomplete="email"
        required
        placeholder="you@example.com"
        :class="[inputClass, { 'form-input-invalid': !!form.errors.email }]"
      >
      <button type="submit" :class="btnClass" :disabled="form.processing">
        Subscribe
      </button>
    </form>
    <p v-if="form.errors.email" class="mt-2 text-xs text-rose-400" role="alert">{{ form.errors.email }}</p>
    <p v-if="form.recentlySuccessful" class="mt-2 text-xs font-medium text-emerald-400">
      Thanks — you are on the list.
    </p>
  </div>
</template>
