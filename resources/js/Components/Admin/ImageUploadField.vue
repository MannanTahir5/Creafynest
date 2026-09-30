<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import {
  Image as ImageIcon,
  ImageOff,
  RotateCcw,
  Trash2,
  UploadCloud,
  X,
} from 'lucide-vue-next'

const props = defineProps({
  label: { type: String, default: 'Image' },
  hint: { type: String, default: '' },
  aspect: { type: String, default: '4 / 3' },
  recommendation: { type: String, default: '' },
  file: { default: null, validator: (v) => v === null || (typeof File !== 'undefined' && v instanceof File) },
  currentUrl: { type: String, default: null },
  removeFlag: { type: Boolean, default: false },
  error: { type: String, default: '' },
  accept: {
    type: String,
    default:
      'image/png,image/jpeg,image/webp,image/avif,image/svg+xml,image/gif,image/x-icon,image/heic,image/heif',
  },
  maxBytes: { type: Number, default: 4 * 1024 * 1024 },
})

const emit = defineEmits(['update:file', 'update:removeFlag'])

const inputRef = ref(null)
const localPreview = ref(null)
const naturalSize = ref({ w: 0, h: 0 })
const isDragging = ref(false)
const localError = ref('')

const previewUrl = computed(() => {
  if (localPreview.value) return localPreview.value
  if (props.removeFlag) return null
  return props.currentUrl ?? null
})

const previewSource = computed(() => {
  if (localPreview.value) return 'staged'
  if (props.removeFlag) return 'pending-remove'
  return props.currentUrl ? 'current' : 'empty'
})

const fileMeta = computed(() => {
  if (!props.file) return null
  return {
    name: props.file.name,
    size: humanBytes(props.file.size),
    type: simplifyType(props.file.type, props.file.name),
  }
})

const aspectStyle = computed(() => ({ aspectRatio: props.aspect }))

const errorMessage = computed(() => localError.value || props.error)

const dropzoneClass = computed(() => [
  'relative flex w-full cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-slate-50/70 text-center transition',
  'focus-within:border-violet-500 focus-within:bg-violet-50/40 focus-within:ring-2 focus-within:ring-violet-200',
  isDragging.value
    ? 'border-violet-500 bg-violet-50 ring-4 ring-violet-100'
    : errorMessage.value
      ? 'border-rose-300 hover:border-rose-400'
      : 'border-slate-300 hover:border-slate-400 hover:bg-slate-50',
])

function humanBytes(bytes) {
  if (!Number.isFinite(bytes)) return ''
  const units = ['B', 'KB', 'MB', 'GB']
  let i = 0
  let n = bytes
  while (n >= 1024 && i < units.length - 1) {
    n /= 1024
    i++
  }
  return `${n < 10 && i > 0 ? n.toFixed(1) : Math.round(n)} ${units[i]}`
}

function simplifyType(mime, fileName = '') {
  if (mime && mime.startsWith('image/')) return mime.replace('image/', '').toUpperCase()
  const ext = (fileName.match(/\.([^.]+)$/) || [])[1]
  return ext ? ext.toUpperCase() : ''
}

function clearPreviewObjectUrl() {
  if (localPreview.value) {
    URL.revokeObjectURL(localPreview.value)
    localPreview.value = null
  }
  naturalSize.value = { w: 0, h: 0 }
}

function setLocalPreviewFromFile(file) {
  clearPreviewObjectUrl()
  if (file && file.type && file.type.startsWith('image/')) {
    localPreview.value = URL.createObjectURL(file)
    const img = new Image()
    img.onload = () => {
      naturalSize.value = { w: img.naturalWidth, h: img.naturalHeight }
    }
    img.src = localPreview.value
  }
}

function validateFile(file) {
  if (!file) return ''
  if (file.size > props.maxBytes) {
    return `File is ${humanBytes(file.size)} — must be ${humanBytes(props.maxBytes)} or smaller.`
  }
  const ok = file.type === '' || file.type.startsWith('image/')
  if (!ok) {
    return 'Only image files are accepted.'
  }
  return ''
}

function commitFile(file) {
  localError.value = ''
  if (!file) {
    emit('update:file', null)
    clearPreviewObjectUrl()
    return
  }
  const err = validateFile(file)
  if (err) {
    localError.value = err
    if (inputRef.value) inputRef.value.value = ''
    return
  }
  emit('update:file', file)
  emit('update:removeFlag', false)
  setLocalPreviewFromFile(file)
}

function onFileChange(event) {
  const file = event.target.files?.[0] ?? null
  commitFile(file)
}

function onDrop(event) {
  event.preventDefault()
  isDragging.value = false
  const file = event.dataTransfer?.files?.[0] ?? null
  commitFile(file)
}

function onDragOver(event) {
  event.preventDefault()
  isDragging.value = true
}

function onDragLeave(event) {
  if (event.currentTarget.contains(event.relatedTarget)) return
  isDragging.value = false
}

function pickFile() {
  inputRef.value?.click()
}

function clearStaged() {
  localError.value = ''
  clearPreviewObjectUrl()
  emit('update:file', null)
  if (inputRef.value) inputRef.value.value = ''
}

function markRemove() {
  clearStaged()
  emit('update:removeFlag', true)
}

function undoRemove() {
  emit('update:removeFlag', false)
}

watch(
  () => props.file,
  (next) => {
    if (!next && localPreview.value) {
      clearPreviewObjectUrl()
    }
  },
)

onBeforeUnmount(() => {
  clearPreviewObjectUrl()
})
</script>

<template>
  <div>
    <div class="flex flex-wrap items-center justify-between gap-2">
      <p class="text-sm font-semibold text-slate-800">{{ label }}</p>
      <p v-if="recommendation" class="text-[11px] text-slate-500">
        {{ recommendation }}
      </p>
    </div>
    <p v-if="hint" class="mt-0.5 text-xs text-slate-500">{{ hint }}</p>

    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-[minmax(0,18rem)_minmax(0,1fr)]">
      <!-- PREVIEW CARD -->
      <div
        class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5"
        :style="aspectStyle"
      >
        <div
          v-if="previewUrl"
          class="absolute inset-0 bg-[length:16px_16px] bg-[position:0_0,8px_8px]"
          style="background-image:
            linear-gradient(45deg, rgba(15,23,42,0.04) 25%, transparent 25%),
            linear-gradient(-45deg, rgba(15,23,42,0.04) 25%, transparent 25%);
          "
          aria-hidden="true"
        />

        <img
          v-if="previewUrl"
          :src="previewUrl"
          alt="Preview"
          class="relative z-[1] h-full w-full object-contain transition duration-300 group-hover:scale-[1.01]"
          @load="(e) => (naturalSize = { w: e.target.naturalWidth, h: e.target.naturalHeight })"
        >

        <div
          v-else
          class="flex h-full w-full flex-col items-center justify-center gap-1.5 text-slate-400"
        >
          <ImageOff class="h-7 w-7" />
          <span class="text-[11px] font-semibold uppercase tracking-[0.2em]">No image</span>
        </div>

        <span
          v-if="previewSource === 'staged'"
          class="absolute left-2 top-2 z-[2] inline-flex items-center gap-1 rounded-full bg-violet-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm"
        >
          <UploadCloud class="h-3 w-3" />
          Staged
        </span>
        <span
          v-else-if="previewSource === 'current'"
          class="absolute left-2 top-2 z-[2] inline-flex items-center gap-1 rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm"
        >
          <ImageIcon class="h-3 w-3" />
          Saved
        </span>

        <button
          v-if="previewSource === 'staged'"
          type="button"
          class="absolute right-2 top-2 z-[2] inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/90 text-white shadow-lg ring-2 ring-white/40 backdrop-blur transition hover:bg-rose-600 hover:scale-105"
          aria-label="Clear staged file"
          title="Clear staged file"
          @click.stop="clearStaged"
        >
          <X class="h-4 w-4" stroke-width="2.5" />
        </button>
        <button
          v-else-if="previewSource === 'current'"
          type="button"
          class="absolute right-2 top-2 z-[2] inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/90 text-white shadow-lg ring-2 ring-white/40 backdrop-blur transition hover:bg-rose-600 hover:scale-105"
          aria-label="Remove image"
          title="Remove image"
          @click.stop="markRemove"
        >
          <X class="h-4 w-4" stroke-width="2.5" />
        </button>

        <div
          v-if="previewSource === 'pending-remove'"
          class="absolute inset-0 z-[1] flex flex-col items-center justify-center gap-2 bg-rose-50/95"
        >
          <Trash2 class="h-7 w-7 text-rose-600" />
          <p class="px-3 text-center text-xs font-semibold uppercase tracking-wide text-rose-700">
            Will be removed on save
          </p>
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-md border border-amber-300 bg-white px-2.5 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-50"
            @click="undoRemove"
          >
            <RotateCcw class="h-3.5 w-3.5" />
            Undo
          </button>
        </div>
      </div>

      <!-- DROPZONE + ACTIONS -->
      <div class="flex min-w-0 flex-col gap-3">
        <div
          :class="dropzoneClass"
          tabindex="0"
          role="button"
          :aria-label="`Upload ${label}`"
          @click="pickFile"
          @keydown.enter.prevent="pickFile"
          @keydown.space.prevent="pickFile"
          @drop="onDrop"
          @dragover="onDragOver"
          @dragenter="onDragOver"
          @dragleave="onDragLeave"
        >
          <input
            ref="inputRef"
            type="file"
            class="sr-only"
            :accept="accept"
            @change="onFileChange"
          >
          <UploadCloud
            class="h-9 w-9 transition"
            :class="isDragging ? 'text-violet-600' : 'text-slate-400 group-hover:text-slate-500'"
            stroke-width="1.5"
          />
          <p class="mt-2 text-sm font-semibold text-slate-800">
            <span class="text-violet-600">Click to upload</span>
            <span class="text-slate-500"> or drag & drop</span>
          </p>
          <p class="mt-0.5 text-[11px] uppercase tracking-wide text-slate-500">
            PNG · JPG · WebP · SVG · AVIF · up to {{ humanBytes(maxBytes) }}
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-[11px]">
          <span
            v-if="fileMeta"
            class="inline-flex items-center gap-1 rounded-md bg-violet-50 px-2 py-1 font-medium text-violet-800 ring-1 ring-violet-100"
          >
            <span class="font-semibold">{{ fileMeta.type || 'IMG' }}</span>
            <span class="text-violet-300">·</span>
            <span class="truncate max-w-[12rem]" :title="fileMeta.name">{{ fileMeta.name }}</span>
            <span class="text-violet-300">·</span>
            <span>{{ fileMeta.size }}</span>
          </span>

          <span
            v-if="naturalSize.w && naturalSize.h"
            class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 font-medium text-slate-700 ring-1 ring-slate-200"
          >
            {{ naturalSize.w }} × {{ naturalSize.h }} px
          </span>
        </div>

        <p v-if="errorMessage" class="text-xs font-semibold text-rose-600" role="alert">
          {{ errorMessage }}
        </p>
      </div>
    </div>
  </div>
</template>
