<script setup>
import { computed } from 'vue'
import { AlertCircle, CheckCircle2, X } from 'lucide-vue-next'

const props = defineProps({
  type: { type: String, default: 'success' },
  message: { type: String, default: '' },
})

const emit = defineEmits(['dismiss'])

const isSuccess = computed(() => props.type === 'success')

const classes = computed(() =>
  isSuccess.value
    ? 'border-emerald-200/80 bg-emerald-50 text-emerald-900'
    : 'border-rose-200/80 bg-rose-50 text-rose-900',
)
</script>

<template>
  <div
    v-if="message"
    class="flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm shadow-sm"
    :class="classes"
    role="alert"
  >
    <CheckCircle2 v-if="isSuccess" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
    <AlertCircle v-else class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
    <p class="min-w-0 flex-1 font-medium leading-relaxed">{{ message }}</p>
    <button
      type="button"
      class="shrink-0 rounded-lg p-1 opacity-70 transition hover:bg-black/5 hover:opacity-100"
      aria-label="Dismiss"
      @click="emit('dismiss')"
    >
      <X class="h-4 w-4" />
    </button>
  </div>
</template>
