<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  badge: { type: String, default: '' },
})
</script>

<template>
  <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
      <div class="flex flex-wrap items-center gap-2.5">
        <h1 class="admin-page-title">{{ title }}</h1>
        <span v-if="badge" class="admin-badge-violet">{{ badge }}</span>
      </div>
      <p v-if="subtitle" class="admin-page-subtitle">{{ subtitle }}</p>
    </div>
    <div v-if="$slots.actions" class="flex shrink-0 flex-wrap items-center gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>
