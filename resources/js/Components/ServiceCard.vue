<script setup>
import Card from './Ui/Card.vue'

defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  icon: { type: Object, default: null },
})
</script>

<template>
  <Card as="article">
    <div class="flex items-start gap-3">
      <div class="mt-0.5 rounded-lg border border-slate-200 bg-slate-50 p-2 text-slate-900">
        <component :is="icon" v-if="icon" class="h-5 w-5" />
        <span v-else class="block h-5 w-5" />
      </div>

      <div>
        <h3 class="text-base font-semibold tracking-tight text-slate-900">
          {{ title }}
        </h3>
        <p v-if="description" class="mt-2 text-sm leading-relaxed text-slate-600">
          {{ description }}
        </p>
      </div>
    </div>
  </Card>
</template>

