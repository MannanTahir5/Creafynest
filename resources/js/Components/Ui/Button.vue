<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: { type: String, default: null },
  variant: { type: String, default: 'primary' }, // primary | secondary | ghost
  as: { type: String, default: null }, // 'button' | 'a' (rare). If href is set, Link is used by default.
  type: { type: String, default: 'button' },
})

const classes = computed(() => {
  const base =
    'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2'

  switch (props.variant) {
    case 'secondary':
      return `${base} border border-slate-300 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800`
    case 'ghost':
      return `${base} text-slate-900 hover:bg-slate-50 dark:text-slate-100 dark:hover:bg-slate-800`
    case 'primary':
    default:
      return `${base} bg-slate-900 text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200`
  }
})
</script>

<template>
  <Link v-if="href" :href="href" :class="classes">
    <slot />
  </Link>
  <button v-else :type="type" :class="classes">
    <slot />
  </button>
</template>

