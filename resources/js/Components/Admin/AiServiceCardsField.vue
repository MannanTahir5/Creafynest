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

const colorThemes = [
  { id: 'amber', label: 'Amber' },
  { id: 'emerald', label: 'Emerald' },
  { id: 'orange', label: 'Orange' },
  { id: 'violet', label: 'Violet' },
  { id: 'sky', label: 'Sky' },
  { id: 'rose', label: 'Rose' },
  { id: 'slate', label: 'Slate' },
]

function commit(next) { emit('update:modelValue', next) }
function commitImages(next) { emit('update:itemImages', next) }

function update(index, patch) {
  commit(props.modelValue.map((card, i) => (i === index ? { ...card, ...patch } : card)))
}

function addCard() {
  commit([
    ...props.modelValue,
    {
      id: null, title: '', description: '', href: '/services',
      icon: 'Sparkles', color_theme: 'amber',
      image_alt: '', image_remove: false, image_url: null, is_active: true,
    },
  ])
  commitImages([...props.itemImages, null])
}

function removeCard(index) {
  const old = previewUrls.value.get(index)
  if (old) URL.revokeObjectURL(old)
  previewUrls.value.delete(index)
  commit(props.modelValue.filter((_, i) => i !== index))
  commitImages(props.itemImages.filter((_, i) => i !== index))
}

function moveCard(index, delta) {
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

function errorFor(index, field) { return props.errors[`cards.${index}.${field}`] }
</script>

<template>
  <div class="space-y-4">
    <div v-if="modelValue.length === 0" class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
      No cards yet — click “Add card” below.
    </div>

    <div
      v-for="(card, index) in modelValue"
      :key="card.id ?? `new-${index}`"
      class="rounded-xl border border-slate-200 bg-white p-4"
    >
      <div class="flex items-center justify-between gap-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Card {{ index + 1 }}</p>
        <div class="flex items-center gap-1">
          <label class="mr-1 inline-flex items-center gap-1 text-xs text-slate-600">
            <input
              type="checkbox"
              class="h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
              :checked="card.is_active"
              @change="update(index, { is_active: $event.target.checked })"
            >
            On
          </label>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" :disabled="index === 0" aria-label="Move up" @click="moveCard(index, -1)">
            <ArrowUp class="h-4 w-4" />
          </button>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40" :disabled="index === modelValue.length - 1" aria-label="Move down" @click="moveCard(index, 1)">
            <ArrowDown class="h-4 w-4" />
          </button>
          <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove card" @click="removeCard(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
          <label :for="`ai-title-${index}`" class="block text-sm font-medium text-slate-700">Title</label>
          <input :id="`ai-title-${index}`" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorFor(index, 'title') }" :value="card.title" @input="update(index, { title: $event.target.value })">
          <p v-if="errorFor(index, 'title')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'title') }}</p>
        </div>
        <div>
          <label :for="`ai-icon-${index}`" class="block text-sm font-medium text-slate-700">Lucide icon</label>
          <input :id="`ai-icon-${index}`" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorFor(index, 'icon') }" placeholder="Sparkles" :value="card.icon" @input="update(index, { icon: $event.target.value })">
          <p v-if="errorFor(index, 'icon')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'icon') }}</p>
        </div>
      </div>

      <div class="mt-3">
        <label :for="`ai-desc-${index}`" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea :id="`ai-desc-${index}`" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!errorFor(index, 'description') }" :value="card.description" @input="update(index, { description: $event.target.value })" />
        <p v-if="errorFor(index, 'description')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'description') }}</p>
      </div>

      <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="sm:col-span-2">
          <label :for="`ai-href-${index}`" class="block text-sm font-medium text-slate-700">Link href</label>
          <input :id="`ai-href-${index}`" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorFor(index, 'href') }" placeholder="/services" :value="card.href" @input="update(index, { href: $event.target.value })">
          <p v-if="errorFor(index, 'href')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'href') }}</p>
        </div>
        <div>
          <label :for="`ai-color-${index}`" class="block text-sm font-medium text-slate-700">Color</label>
          <select :id="`ai-color-${index}`" class="form-select" :class="{ 'form-input-invalid': !!errorFor(index, 'color_theme') }" :value="card.color_theme" @change="update(index, { color_theme: $event.target.value })">
            <option v-for="c in colorThemes" :key="c.id" :value="c.id">{{ c.label }}</option>
          </select>
          <p v-if="errorFor(index, 'color_theme')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorFor(index, 'color_theme') }}</p>
        </div>
      </div>

      <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
        <p class="text-xs font-semibold text-slate-700">Card image (optional)</p>
        <div class="mt-2 flex items-start gap-3">
          <div class="flex h-20 w-28 shrink-0 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-white">
            <img v-if="previewFor(index)" :src="previewFor(index)" alt="" class="h-full w-full object-contain">
            <div v-else class="flex flex-col items-center gap-0.5 text-slate-400">
              <ImageOff class="h-4 w-4" />
              <span class="text-[9px] uppercase tracking-wide">No image</span>
            </div>
          </div>
          <div class="min-w-0 flex-1 space-y-2">
            <input type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="form-file text-xs" @change="(e) => onFileChange(index, e)">
            <div class="flex flex-wrap items-center gap-2">
              <button v-if="itemImages[index]" type="button" class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50" @click="clearStaged(index)">
                <X class="h-3 w-3" /> Clear staged
              </button>
              <button v-if="!itemImages[index] && card.image_url && !card.image_remove" type="button" class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100" @click="markRemove(index)">
                <X class="h-3 w-3" /> Remove current
              </button>
              <button v-if="card.image_remove" type="button" class="inline-flex items-center gap-1 rounded-md border border-amber-200 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 hover:bg-amber-100" @click="undoRemove(index)">
                Keep current
              </button>
            </div>
            <input type="text" class="form-input text-xs" placeholder="Image alt text (optional)" :value="card.image_alt" @input="update(index, { image_alt: $event.target.value })">
          </div>
        </div>
      </div>
    </div>

    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2" @click="addCard">
      <Plus class="h-4 w-4" /> Add card
    </button>
  </div>
</template>
