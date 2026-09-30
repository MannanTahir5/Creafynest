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

function commit(next) {
  emit('update:modelValue', next)
}

function commitImages(next) {
  emit('update:itemImages', next)
}

function update(index, patch) {
  const next = props.modelValue.map((item, i) => (i === index ? { ...item, ...patch } : item))
  commit(next)
}

function addItem() {
  commit([
    ...props.modelValue,
    { id: null, title: '', description: '', icon: 'Box', image_alt: '', image_remove: false, image_url: null },
  ])
  commitImages([...props.itemImages, null])
}

function removeItem(index) {
  const local = previewUrls.value.get(index)
  if (local) URL.revokeObjectURL(local)
  previewUrls.value.delete(index)
  commit(props.modelValue.filter((_, i) => i !== index))
  commitImages(props.itemImages.filter((_, i) => i !== index))
}

function moveItem(index, delta) {
  const target = index + delta
  if (target < 0 || target >= props.modelValue.length) return

  const nextItems = props.modelValue.slice()
  const [movedItem] = nextItems.splice(index, 1)
  nextItems.splice(target, 0, movedItem)
  commit(nextItems)

  const nextImages = props.itemImages.slice()
  const [movedImage] = nextImages.splice(index, 1)
  nextImages.splice(target, 0, movedImage)
  commitImages(nextImages)

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
  if (file) {
    const url = URL.createObjectURL(file)
    const next = new Map(previewUrls.value)
    next.set(index, url)
    previewUrls.value = next
  } else {
    const next = new Map(previewUrls.value)
    next.delete(index)
    previewUrls.value = next
  }

  const nextImages = props.itemImages.slice()
  while (nextImages.length <= index) nextImages.push(null)
  nextImages[index] = file
  commitImages(nextImages)

  update(index, { image_remove: false })
}

function clearStaged(index) {
  const old = previewUrls.value.get(index)
  if (old) URL.revokeObjectURL(old)
  const map = new Map(previewUrls.value)
  map.delete(index)
  previewUrls.value = map

  const nextImages = props.itemImages.slice()
  while (nextImages.length <= index) nextImages.push(null)
  nextImages[index] = null
  commitImages(nextImages)
}

function markRemove(index) {
  clearStaged(index)
  update(index, { image_remove: true })
}

function undoRemove(index) {
  update(index, { image_remove: false })
}

function previewFor(index) {
  if (previewUrls.value.has(index)) return previewUrls.value.get(index)
  if (props.modelValue[index]?.image_remove) return null
  return props.modelValue[index]?.image_url ?? null
}

function errorFor(index, field) {
  return props.errors[`items.${index}.${field}`]
}
</script>

<template>
  <div class="space-y-4">
    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
      No items yet. Add at least one to make this category meaningful on the home page.
    </div>

    <div
      v-for="(item, index) in modelValue"
      :key="item.id ?? `new-${index}`"
      class="rounded-xl border border-slate-200 bg-white p-4"
    >
      <div class="flex items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
          Item {{ index + 1 }}
        </p>
        <div class="flex items-center gap-1">
          <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="index === 0"
            aria-label="Move up"
            @click="moveItem(index, -1)"
          >
            <ArrowUp class="h-4 w-4" />
          </button>
          <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="index === modelValue.length - 1"
            aria-label="Move down"
            @click="moveItem(index, 1)"
          >
            <ArrowDown class="h-4 w-4" />
          </button>
          <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50"
            aria-label="Remove item"
            @click="removeItem(index)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
          <label :for="`item-title-${index}`" class="block text-sm font-medium text-slate-700">Title</label>
          <input
            :id="`item-title-${index}`"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!errorFor(index, 'title') }"
            :value="item.title"
            @input="update(index, { title: $event.target.value })"
          >
          <p v-if="errorFor(index, 'title')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'title') }}</p>
        </div>
        <div>
          <label :for="`item-icon-${index}`" class="block text-sm font-medium text-slate-700">Lucide icon</label>
          <input
            :id="`item-icon-${index}`"
            type="text"
            class="form-input"
            :class="{ 'form-input-invalid': !!errorFor(index, 'icon') }"
            placeholder="Box"
            :value="item.icon"
            @input="update(index, { icon: $event.target.value })"
          >
          <p v-if="errorFor(index, 'icon')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'icon') }}</p>
        </div>
      </div>

      <div class="mt-3">
        <label :for="`item-desc-${index}`" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea
          :id="`item-desc-${index}`"
          rows="3"
          class="form-textarea"
          :class="{ 'form-input-invalid': !!errorFor(index, 'description') }"
          :value="item.description"
          @input="update(index, { description: $event.target.value })"
        />
        <p v-if="errorFor(index, 'description')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'description') }}</p>
      </div>

      <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
        <p class="text-xs font-semibold text-slate-700">Item image</p>
        <div class="mt-2 flex items-start gap-3">
          <div class="flex h-20 w-28 shrink-0 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-white">
            <img v-if="previewFor(index)" :src="previewFor(index)" alt="" class="h-full w-full object-contain">
            <div v-else class="flex flex-col items-center gap-0.5 text-slate-400">
              <ImageOff class="h-4 w-4" />
              <span class="text-[9px] uppercase tracking-wide">No image</span>
            </div>
          </div>
          <div class="min-w-0 flex-1 space-y-2">
            <input
              type="file"
              accept="image/png,image/jpeg,image/webp,image/svg+xml"
              class="form-file text-xs"
              @change="(e) => onFileChange(index, e)"
            >
            <div class="flex flex-wrap items-center gap-2">
              <button
                v-if="itemImages[index]"
                type="button"
                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50"
                @click="clearStaged(index)"
              >
                <X class="h-3 w-3" />
                Clear staged
              </button>
              <button
                v-if="!itemImages[index] && item.image_url && !item.image_remove"
                type="button"
                class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100"
                @click="markRemove(index)"
              >
                <X class="h-3 w-3" />
                Remove current
              </button>
              <button
                v-if="item.image_remove"
                type="button"
                class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-100"
                @click="undoRemove(index)"
              >
                Keep current
              </button>
            </div>
            <input
              type="text"
              class="form-input text-xs"
              placeholder="Image alt text (optional)"
              :value="item.image_alt"
              @input="update(index, { image_alt: $event.target.value })"
            >
          </div>
        </div>
      </div>
    </div>

    <button
      type="button"
      class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      @click="addItem"
    >
      <Plus class="h-4 w-4" />
      Add item
    </button>
  </div>
</template>
