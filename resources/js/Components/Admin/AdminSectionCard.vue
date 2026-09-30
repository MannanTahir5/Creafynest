<script setup>
defineProps({
  title: { type: String, default: '' },
  description: { type: String, default: '' },
  compact: { type: Boolean, default: false },
})
</script>

<template>
  <section class="admin-section-card" :class="compact ? 'p-4 sm:p-5' : ''">
    <div v-if="title || $slots.header" class="mb-4 flex flex-wrap items-start justify-between gap-3">
      <div class="min-w-0">
        <slot name="header">
          <h2 class="text-base font-semibold text-slate-900">{{ title }}</h2>
          <p v-if="description" class="mt-1 text-sm text-slate-600">{{ description }}</p>
        </slot>
      </div>
      <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
    <slot />
  </section>
</template>
