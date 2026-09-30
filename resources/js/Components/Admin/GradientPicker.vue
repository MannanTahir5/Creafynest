<script setup>
import { HERO_GRADIENTS } from '../../config/heroGradients.js'

const props = defineProps({
  modelValue: { type: String, default: 'cyan-violet-fuchsia' },
  options: { type: Array, default: () => [] },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const themes = HERO_GRADIENTS.filter((g) =>
  !props.options || !props.options.length || props.options.includes(g.id)
)

function pick(id) {
  emit('update:modelValue', id)
}
</script>

<template>
  <div>
    <p class="block text-sm font-medium text-slate-700">Heading gradient</p>
    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
      <button
        v-for="g in themes"
        :key="g.id"
        type="button"
        class="group flex items-center gap-2 rounded-lg border bg-white p-2 text-left transition hover:border-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        :class="modelValue === g.id ? 'border-slate-900 ring-2 ring-slate-900/20' : 'border-slate-200'"
        :aria-pressed="modelValue === g.id"
        @click="pick(g.id)"
      >
        <span :class="['h-7 w-7 shrink-0 rounded-full', g.swatch]" aria-hidden="true" />
        <span class="min-w-0 truncate text-xs font-semibold text-slate-700 group-hover:text-slate-900">{{ g.label }}</span>
      </button>
    </div>
    <p v-if="error" class="mt-1 text-xs text-rose-600" role="alert">{{ error }}</p>
  </div>
</template>
