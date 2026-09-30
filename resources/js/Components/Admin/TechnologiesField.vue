<script setup>
import { computed, ref } from 'vue'
import { ArrowDown, ArrowUp, ImageOff, Plus, Trash2, X } from 'lucide-vue-next'
import { simpleIconSvgUrl } from '../../utils/simpleIconCdn.js'

const props = defineProps({
  modelValue: { type: Array, required: true },
  errors: { type: Object, default: () => ({}) },
  itemImages: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue', 'update:itemImages'])

const previewUrls = ref(new Map())
const filterRow = ref('all')

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

function addItem(rowIndex) {
  commit([
    ...props.modelValue,
    {
      id: null,
      name: '',
      icon_slug: '',
      image_alt: '',
      image_remove: false,
      image_url: null,
      row_index: rowIndex,
      is_active: true,
    },
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
  const [moved] = nextItems.splice(index, 1)
  nextItems.splice(target, 0, moved)
  commit(nextItems)

  const nextImages = props.itemImages.slice()
  const [movedImg] = nextImages.splice(index, 1)
  nextImages.splice(target, 0, movedImg)
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
  if (props.modelValue[index]?.image_url) return props.modelValue[index].image_url
  const slug = props.modelValue[index]?.icon_slug
  if (slug) return simpleIconSvgUrl(slug)
  return null
}

function errorFor(index, field) {
  return props.errors[`technologies.${index}.${field}`]
}

const visibleIndexes = computed(() => {
  return props.modelValue
    .map((tech, idx) => ({ tech, idx }))
    .filter(({ tech }) => filterRow.value === 'all' || tech.row_index === Number(filterRow.value))
    .map(({ idx }) => idx)
})

const counts = computed(() => ({
  all: props.modelValue.length,
  row1: props.modelValue.filter((t) => t.row_index === 1).length,
  row2: props.modelValue.filter((t) => t.row_index === 2).length,
}))
</script>

<template>
  <div class="space-y-3">
    <div class="flex flex-wrap items-center gap-2">
      <button
        type="button"
        class="rounded-md border px-2.5 py-1 text-xs font-semibold transition"
        :class="filterRow === 'all' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
        @click="filterRow = 'all'"
      >
        All ({{ counts.all }})
      </button>
      <button
        type="button"
        class="rounded-md border px-2.5 py-1 text-xs font-semibold transition"
        :class="filterRow === '1' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
        @click="filterRow = '1'"
      >
        Row 1 ({{ counts.row1 }})
      </button>
      <button
        type="button"
        class="rounded-md border px-2.5 py-1 text-xs font-semibold transition"
        :class="filterRow === '2' ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
        @click="filterRow = '2'"
      >
        Row 2 ({{ counts.row2 }})
      </button>
      <span class="text-xs text-slate-500">Use the row selector on each tech to assign it to row 1 or row 2 of the marquee.</span>
    </div>

    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
      No technologies yet — add some below.
    </div>

    <div
      v-for="index in visibleIndexes"
      :key="modelValue[index].id ?? `new-${index}`"
      class="rounded-lg border border-slate-200 bg-white p-3"
    >
      <div class="grid grid-cols-12 items-start gap-3">
        <!-- preview -->
        <div class="col-span-2 flex items-center justify-center sm:col-span-1">
          <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-50">
            <img v-if="previewFor(index)" :src="previewFor(index)" alt="" class="h-9 w-9 object-contain">
            <ImageOff v-else class="h-4 w-4 text-slate-400" />
          </div>
        </div>

        <!-- name -->
        <div class="col-span-10 sm:col-span-3">
          <label :for="`tech-name-${index}`" class="sr-only">Name</label>
          <input
            :id="`tech-name-${index}`"
            type="text"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'name') }"
            placeholder="Tech name"
            :value="modelValue[index].name"
            @input="update(index, { name: $event.target.value })"
          >
          <p v-if="errorFor(index, 'name')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'name') }}</p>
        </div>

        <!-- icon slug -->
        <div class="col-span-7 sm:col-span-3">
          <label :for="`tech-slug-${index}`" class="sr-only">Simple Icons slug</label>
          <input
            :id="`tech-slug-${index}`"
            type="text"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'icon_slug') }"
            placeholder="simpleicons slug, e.g. react"
            :value="modelValue[index].icon_slug"
            @input="update(index, { icon_slug: $event.target.value })"
          >
          <p v-if="errorFor(index, 'icon_slug')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'icon_slug') }}</p>
        </div>

        <!-- row -->
        <div class="col-span-3 sm:col-span-2">
          <label :for="`tech-row-${index}`" class="sr-only">Row</label>
          <select
            :id="`tech-row-${index}`"
            class="form-input text-sm"
            :class="{ 'form-input-invalid': !!errorFor(index, 'row_index') }"
            :value="modelValue[index].row_index"
            @change="update(index, { row_index: Number($event.target.value) })"
          >
            <option :value="1">Row 1</option>
            <option :value="2">Row 2</option>
          </select>
          <p v-if="errorFor(index, 'row_index')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'row_index') }}</p>
        </div>

        <!-- active + actions -->
        <div class="col-span-2 sm:col-span-3 flex items-center justify-end gap-1">
          <label class="mr-1 inline-flex items-center gap-1 text-xs text-slate-600" :title="modelValue[index].is_active ? 'Visible' : 'Hidden'">
            <input
              type="checkbox"
              class="h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
              :checked="modelValue[index].is_active"
              @change="update(index, { is_active: $event.target.checked })"
            >
            <span class="hidden sm:inline">On</span>
          </label>
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
            aria-label="Remove technology"
            @click="removeItem(index)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>

      <!-- collapsible image row -->
      <details class="mt-2 rounded-md border border-slate-200 bg-slate-50/50 px-2 py-1">
        <summary class="cursor-pointer text-xs font-semibold text-slate-600">Custom icon image (overrides the slug)</summary>
        <div class="mt-2 flex flex-wrap items-center gap-2 px-1 pb-2">
          <input
            type="file"
            accept="image/png,image/jpeg,image/webp,image/svg+xml"
            class="form-file text-xs"
            @change="(e) => onFileChange(index, e)"
          >
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
            v-if="!itemImages[index] && modelValue[index].image_url && !modelValue[index].image_remove"
            type="button"
            class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100"
            @click="markRemove(index)"
          >
            <X class="h-3 w-3" />
            Remove current
          </button>
          <button
            v-if="modelValue[index].image_remove"
            type="button"
            class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-100"
            @click="undoRemove(index)"
          >
            Keep current
          </button>
          <input
            type="text"
            class="form-input min-w-0 flex-1 text-xs"
            placeholder="Image alt text (optional)"
            :value="modelValue[index].image_alt"
            @input="update(index, { image_alt: $event.target.value })"
          >
        </div>
      </details>
    </div>

    <div class="flex flex-wrap gap-2">
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        @click="addItem(1)"
      >
        <Plus class="h-4 w-4" />
        Add to row 1
      </button>
      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        @click="addItem(2)"
      >
        <Plus class="h-4 w-4" />
        Add to row 2
      </button>
    </div>
  </div>
</template>
