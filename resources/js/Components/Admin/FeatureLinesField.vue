<script setup>
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
  maxItems: { type: Number, default: 8 },
  placeholder: { type: String, default: 'Short feature line' },
})

const emit = defineEmits(['update:modelValue'])

function commit(next) { emit('update:modelValue', next) }

function update(index, value) {
  commit(props.modelValue.map((v, i) => (i === index ? value : v)))
}

function add() {
  if (props.modelValue.length >= props.maxItems) return
  commit([...props.modelValue, ''])
}

function remove(index) { commit(props.modelValue.filter((_, i) => i !== index)) }

function move(index, delta) {
  const target = index + delta
  if (target < 0 || target >= props.modelValue.length) return
  const next = props.modelValue.slice()
  const [m] = next.splice(index, 1)
  next.splice(target, 0, m)
  commit(next)
}

function errorFor(index) { return props.errors[`feature_lines.${index}`] }
</script>

<template>
  <div class="space-y-2">
    <div
      v-for="(line, index) in modelValue"
      :key="index"
      class="flex items-center gap-2"
    >
      <input
        type="text"
        class="form-input text-sm"
        :class="{ 'form-input-invalid': !!errorFor(index) }"
        :placeholder="placeholder"
        :value="line"
        @input="update(index, $event.target.value)"
      >
      <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="index === 0" aria-label="Move up" @click="move(index, -1)">
        <ArrowUp class="h-4 w-4" />
      </button>
      <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="index === modelValue.length - 1" aria-label="Move down" @click="move(index, 1)">
        <ArrowDown class="h-4 w-4" />
      </button>
      <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove line" @click="remove(index)">
        <Trash2 class="h-4 w-4" />
      </button>
    </div>

    <p v-if="modelValue.some((_, i) => errorFor(i))" class="text-xs text-rose-600" role="alert">
      <template v-for="(line, index) in modelValue" :key="index">
        <span v-if="errorFor(index)">{{ errorFor(index) }} </span>
      </template>
    </p>

    <button
      v-if="modelValue.length < maxItems"
      type="button"
      class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      @click="add"
    >
      <Plus class="h-4 w-4" /> Add line
    </button>
  </div>
</template>
