<script setup>
import { computed } from 'vue'
import { AlertTriangle } from 'lucide-vue-next'

const props = defineProps({
  form: { type: Object, default: null },
  errors: { type: Object, default: null },
})

const errorList = computed(() => {
  const errs = props.form?.errors ?? props.errors ?? {}
  return Object.values(errs).flat().filter(Boolean)
})

const hasErrors = computed(() => errorList.value.length > 0)
</script>

<template>
  <div
    v-if="hasErrors"
    class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200/80 bg-rose-50 px-4 py-3.5 text-sm text-rose-900 shadow-sm"
    role="alert"
  >
    <AlertTriangle class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
    <div class="min-w-0">
      <p class="font-semibold">Please fix the following errors</p>
      <ul v-if="errorList.length" class="mt-2 list-inside list-disc space-y-0.5 text-rose-800/90">
        <li v-for="(msg, i) in errorList.slice(0, 6)" :key="i">{{ msg }}</li>
      </ul>
    </div>
  </div>
</template>
