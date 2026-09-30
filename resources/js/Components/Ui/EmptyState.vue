<script setup>
defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  /** Use on dark section backgrounds */
  variant: { type: String, default: 'light' },
})
</script>

<template>
  <div
    class="rounded-2xl border border-dashed px-6 py-14 text-center"
    :class="
      variant === 'dark'
        ? 'border-white/20 bg-white/[0.04]'
        : 'border-slate-200 bg-slate-50/70'
    "
  >
    <p
      class="text-sm font-semibold"
      :class="variant === 'dark' ? 'text-white' : 'text-slate-900'"
    >
      {{ title }}
    </p>
    <p
      v-if="description"
      class="mt-2 text-sm leading-relaxed"
      :class="variant === 'dark' ? 'text-slate-400' : 'text-slate-600'"
    >
      {{ description }}
    </p>
    <div v-if="$slots.default" class="mt-6 flex flex-wrap justify-center gap-3">
      <slot />
    </div>
  </div>
</template>
