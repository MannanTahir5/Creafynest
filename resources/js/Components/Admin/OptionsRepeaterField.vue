<script setup>
import { Plus, Trash2, ArrowUp, ArrowDown } from 'lucide-vue-next'

const model = defineModel({ type: Array, default: () => [] })

function addRow() {
  model.value = [...model.value, { id: '', label: '' }]
}

function removeRow(index) {
  const next = [...model.value]
  next.splice(index, 1)
  model.value = next
}

function move(index, delta) {
  const target = index + delta
  if (target < 0 || target >= model.value.length) return
  const next = [...model.value]
  const [item] = next.splice(index, 1)
  next.splice(target, 0, item)
  model.value = next
}
</script>

<template>
  <div class="space-y-3">
    <div
      v-for="(row, index) in model"
      :key="index"
      class="flex flex-wrap items-end gap-2 rounded-xl border border-slate-100 bg-slate-50/70 p-3"
    >
      <div class="min-w-[7rem] flex-1">
        <label class="block text-xs font-medium text-slate-600">ID</label>
        <input v-model="row.id" type="text" class="form-input mt-1" placeholder="short">
      </div>
      <div class="min-w-[12rem] flex-[2]">
        <label class="block text-xs font-medium text-slate-600">Label</label>
        <input v-model="row.label" type="text" class="form-input mt-1" placeholder="Display text">
      </div>
      <div class="flex gap-1 pb-0.5">
        <button type="button" class="rounded p-1.5 text-slate-500 hover:bg-white" :disabled="index === 0" @click="move(index, -1)">
          <ArrowUp class="h-4 w-4" />
        </button>
        <button type="button" class="rounded p-1.5 text-slate-500 hover:bg-white" :disabled="index >= model.length - 1" @click="move(index, 1)">
          <ArrowDown class="h-4 w-4" />
        </button>
        <button type="button" class="rounded p-1.5 text-rose-600 hover:bg-rose-50" @click="removeRow(index)">
          <Trash2 class="h-4 w-4" />
        </button>
      </div>
    </div>
    <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="addRow">
      <Plus class="h-4 w-4" /> Add option
    </button>
  </div>
</template>
