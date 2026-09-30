<script setup>
defineProps({
  as: { type: String, default: 'div' },
})
</script>

<template>
  <component
    :is="as"
    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-900/60 dark:shadow-none dark:hover:shadow-md"
  >
    <slot />
  </component>
</template>

