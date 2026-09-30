<script setup>
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:modelValue'])

function commit(next) { emit('update:modelValue', next) }

function update(index, patch) {
  commit(props.modelValue.map((item, i) => (i === index ? { ...item, ...patch } : item)))
}

function add() {
  commit([...props.modelValue, { id: null, title: '', icon: 'Building2', is_active: true }])
}

function remove(index) {
  commit(props.modelValue.filter((_, i) => i !== index))
}

function move(index, delta) {
  const target = index + delta
  if (target < 0 || target >= props.modelValue.length) return
  const next = props.modelValue.slice()
  const [m] = next.splice(index, 1)
  next.splice(target, 0, m)
  commit(next)
}

function errorFor(index, field) { return props.errors[`industries.${index}.${field}`] }
</script>

<template>
  <div class="space-y-2">
    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
      No industries yet — add one below.
    </div>

    <div
      v-for="(item, index) in modelValue"
      :key="item.id ?? `new-${index}`"
      class="rounded-lg border border-slate-200 bg-white p-3"
    >
      <div class="grid grid-cols-12 items-center gap-2">
        <div class="col-span-12 sm:col-span-5">
          <input
            type="text"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'title') }"
            placeholder="Industry name"
            :value="item.title"
            @input="update(index, { title: $event.target.value })"
          >
          <p v-if="errorFor(index, 'title')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'title') }}</p>
        </div>
        <div class="col-span-7 sm:col-span-4">
          <input
            type="text"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'icon') }"
            placeholder="Lucide icon (e.g. HeartPulse)"
            :value="item.icon"
            @input="update(index, { icon: $event.target.value })"
          >
          <p v-if="errorFor(index, 'icon')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'icon') }}</p>
        </div>
        <div class="col-span-5 sm:col-span-3 flex items-center justify-end gap-1">
          <label class="mr-1 inline-flex items-center gap-1 text-xs text-slate-600">
            <input
              type="checkbox"
              class="h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
              :checked="item.is_active"
              @change="update(index, { is_active: $event.target.checked })"
            >
            On
          </label>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="index === 0" aria-label="Move up" @click="move(index, -1)">
            <ArrowUp class="h-4 w-4" />
          </button>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="index === modelValue.length - 1" aria-label="Move down" @click="move(index, 1)">
            <ArrowDown class="h-4 w-4" />
          </button>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove industry" @click="remove(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2" @click="add">
      <Plus class="h-4 w-4" /> Add industry
    </button>
  </div>
</template>
