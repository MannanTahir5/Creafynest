<script setup>
import { ref } from 'vue'
import { ArrowDown, ArrowUp, ImageOff, Plus, Trash2, X } from 'lucide-vue-next'

const props = defineProps({
  modelValue: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
  itemImages: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue', 'update:itemImages'])

const previewUrls = ref(new Map())

function commit(next) { emit('update:modelValue', next) }
function commitImages(next) { emit('update:itemImages', next) }

function update(index, patch) {
  commit(props.modelValue.map((row, i) => (i === index ? { ...row, ...patch } : row)))
}

function add() {
  commit([
    ...props.modelValue,
    { id: null, name: '', image_alt: '', image_remove: false, image_url: null, is_active: true },
  ])
  commitImages([...props.itemImages, null])
}

function remove(index) {
  const old = previewUrls.value.get(index)
  if (old) URL.revokeObjectURL(old)
  previewUrls.value.delete(index)
  commit(props.modelValue.filter((_, i) => i !== index))
  commitImages(props.itemImages.filter((_, i) => i !== index))
}

function move(index, delta) {
  const target = index + delta
  if (target < 0 || target >= props.modelValue.length) return

  const items = props.modelValue.slice()
  const [m] = items.splice(index, 1)
  items.splice(target, 0, m)
  commit(items)

  const imgs = props.itemImages.slice()
  const [mi] = imgs.splice(index, 1)
  imgs.splice(target, 0, mi)
  commitImages(imgs)

  const map = new Map()
  previewUrls.value.forEach((url, oldIdx) => {
    if (oldIdx === index) map.set(target, url)
    else if (oldIdx === target) map.set(index, url)
    else map.set(oldIdx, url)
  })
  previewUrls.value = map
}

function onFileChange(index, event) {
  const file = event.target.files?.[0] ?? null
  const old = previewUrls.value.get(index)
  if (old) URL.revokeObjectURL(old)
  const map = new Map(previewUrls.value)
  if (file) map.set(index, URL.createObjectURL(file))
  else map.delete(index)
  previewUrls.value = map

  const next = props.itemImages.slice()
  while (next.length <= index) next.push(null)
  next[index] = file
  commitImages(next)
  update(index, { image_remove: false })
}

function clearStaged(index) {
  const old = previewUrls.value.get(index)
  if (old) URL.revokeObjectURL(old)
  const map = new Map(previewUrls.value)
  map.delete(index)
  previewUrls.value = map

  const next = props.itemImages.slice()
  while (next.length <= index) next.push(null)
  next[index] = null
  commitImages(next)
}

function markRemove(index) { clearStaged(index); update(index, { image_remove: true }) }
function undoRemove(index) { update(index, { image_remove: false }) }

function previewFor(index) {
  if (previewUrls.value.has(index)) return previewUrls.value.get(index)
  if (props.modelValue[index]?.image_remove) return null
  return props.modelValue[index]?.image_url ?? null
}

function errorFor(index, field) { return props.errors[`avatars.${index}.${field}`] }
</script>

<template>
  <div class="space-y-3">
    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
      No avatars yet. Add a few to replace the default gradient circles in the trust badge row.
    </div>

    <div
      v-for="(av, index) in modelValue"
      :key="av.id ?? `new-${index}`"
      class="rounded-lg border border-slate-200 bg-white p-3"
    >
      <div class="grid grid-cols-12 items-center gap-3">
        <div class="col-span-3 sm:col-span-1">
          <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50">
            <img v-if="previewFor(index)" :src="previewFor(index)" alt="" class="h-full w-full object-cover">
            <ImageOff v-else class="h-4 w-4 text-slate-400" />
          </div>
        </div>

        <div class="col-span-9 sm:col-span-4">
          <input
            type="text"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'name') }"
            placeholder="Name (optional)"
            :value="av.name"
            @input="update(index, { name: $event.target.value })"
          >
          <p v-if="errorFor(index, 'name')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'name') }}</p>
        </div>

        <div class="col-span-12 sm:col-span-4">
          <input
            type="file"
            accept="image/png,image/jpeg,image/webp,image/svg+xml"
            class="form-file text-xs"
            @change="(e) => onFileChange(index, e)"
          >
          <div class="mt-1 flex flex-wrap items-center gap-2">
            <button v-if="itemImages[index]" type="button" class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50" @click="clearStaged(index)">
              <X class="h-3 w-3" /> Clear staged
            </button>
            <button v-if="!itemImages[index] && av.image_url && !av.image_remove" type="button" class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100" @click="markRemove(index)">
              <X class="h-3 w-3" /> Remove current
            </button>
            <button v-if="av.image_remove" type="button" class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-100" @click="undoRemove(index)">
              Keep current
            </button>
          </div>
        </div>

        <div class="col-span-12 sm:col-span-3 flex items-center justify-end gap-1">
          <label class="mr-1 inline-flex items-center gap-1 text-xs text-slate-600">
            <input
              type="checkbox"
              class="h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
              :checked="av.is_active"
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
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove avatar" @click="remove(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>

        <div class="col-span-12">
          <input
            type="text"
            class="form-input text-xs"
            placeholder="Image alt text (optional)"
            :value="av.image_alt"
            @input="update(index, { image_alt: $event.target.value })"
          >
        </div>
      </div>
    </div>

    <button
      type="button"
      class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      @click="add"
    >
      <Plus class="h-4 w-4" /> Add avatar
    </button>
  </div>
</template>
