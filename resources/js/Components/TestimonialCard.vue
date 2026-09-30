<script setup>
import Card from './Ui/Card.vue'

defineProps({
  name: { type: String, required: true },
  feedback: { type: String, required: true },
  role: { type: String, default: '' },
})
</script>

<template>
  <Card as="figure">
    <blockquote class="text-sm leading-relaxed text-slate-700">
      “{{ feedback }}”
    </blockquote>
    <figcaption class="mt-4">
      <p class="text-sm font-semibold text-slate-900">{{ name }}</p>
      <p v-if="role" class="text-xs text-slate-500">{{ role }}</p>
    </figcaption>
  </Card>
</template>

