<script setup>
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: { type: Array, required: true },
  label: { type: String, default: 'Item' },
  fields: { type: Array, required: true },
  newRow: { type: Function, required: true },
  errors: { type: Object, default: () => ({}) },
  errorPrefix: { type: String, required: true },
  max: { type: Number, default: 24 },
  emptyText: { type: String, default: 'No entries yet.' },
})

const emit = defineEmits(['update:modelValue'])

function commit(next) {
  emit('update:modelValue', next)
}

function update(index, key, value) {
  commit(props.modelValue.map((row, i) => (i === index ? { ...row, [key]: value } : row)))
}

function add() {
  if (props.modelValue.length >= props.max) return
  commit([...props.modelValue, props.newRow()])
}

function remove(index) {
  commit(props.modelValue.filter((_, i) => i !== index))
}

function move(index, delta) {
  const target = index + delta
  if (target < 0 || target >= props.modelValue.length) return
  const next = props.modelValue.slice()
  const [moved] = next.splice(index, 1)
  next.splice(target, 0, moved)
  commit(next)
}

function errorFor(index, key) {
  return props.errors[`${props.errorPrefix}.${index}.${key}`]
}
</script>

<template>
  <div class="space-y-3">
    <div
      v-if="!modelValue.length"
      class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500"
    >
      {{ emptyText }}
    </div>

    <div
      v-for="(row, index) in modelValue"
      :key="index"
      class="rounded-lg border border-slate-200 bg-white p-3"
    >
      <div class="flex items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
          {{ label }} {{ index + 1 }}
        </p>
        <div class="flex items-center gap-1">
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="index === 0"
            aria-label="Move up"
            @click="move(index, -1)"
          >
            <ArrowUp class="h-3.5 w-3.5" />
          </button>
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="index === modelValue.length - 1"
            aria-label="Move down"
            @click="move(index, 1)"
          >
            <ArrowDown class="h-3.5 w-3.5" />
          </button>
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50"
            aria-label="Remove"
            @click="remove(index)"
          >
            <Trash2 class="h-3.5 w-3.5" />
          </button>
        </div>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div
          v-for="field in fields"
          :key="field.key"
          :class="field.span === 'full' ? 'sm:col-span-2' : ''"
        >
          <label
            :for="`${errorPrefix}-${index}-${field.key}`"
            class="block text-xs font-medium text-slate-700"
          >
            {{ field.label }}
          </label>
          <textarea
            v-if="field.type === 'textarea'"
            :id="`${errorPrefix}-${index}-${field.key}`"
            rows="3"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!errorFor(index, field.key) }"
            :value="row[field.key] ?? ''"
            :placeholder="field.placeholder"
            @input="update(index, field.key, $event.target.value)"
          />
          <input
            v-else
            :id="`${errorPrefix}-${index}-${field.key}`"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!errorFor(index, field.key) }"
            :value="row[field.key] ?? ''"
            :placeholder="field.placeholder"
            @input="update(index, field.key, $event.target.value)"
          >
          <p v-if="errorFor(index, field.key)" class="mt-1 text-xs text-rose-600" role="alert">
            {{ errorFor(index, field.key) }}
          </p>
        </div>
      </div>
    </div>

    <button
      v-if="modelValue.length < max"
      type="button"
      class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
      @click="add"
    >
      <Plus class="h-3.5 w-3.5" />
      Add {{ label.toLowerCase() }}
    </button>
  </div>
</template>
