<script setup>
import { computed, ref } from 'vue'
import { marked } from 'marked'
import DOMPurify from 'dompurify'

const props = defineProps({
  content: { type: String, default: '' },
  contentFormat: { type: String, default: 'markdown' },
})

const emit = defineEmits(['update:content', 'update:contentFormat'])

const tab = ref('write')

marked.setOptions({ mangle: false, headerIds: false })

const previewHtml = computed(() => {
  const raw = props.contentFormat === 'markdown'
    ? marked.parse(props.content || '')
    : (props.content || '')

  return DOMPurify.sanitize(raw)
})

function setFormat(fmt) {
  emit('update:contentFormat', fmt)
}
</script>

<template>
  <div class="space-y-3">
    <div class="flex flex-wrap items-center gap-3">
      <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Content format</span>
      <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
        <input
          type="radio"
          class="border-slate-300 text-slate-900 focus:ring-2 focus:ring-slate-400 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
          :checked="contentFormat === 'markdown'"
          @change="setFormat('markdown')"
        >
        Markdown
      </label>
      <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
        <input
          type="radio"
          class="border-slate-300 text-slate-900 focus:ring-2 focus:ring-slate-400 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
          :checked="contentFormat === 'html'"
          @change="setFormat('html')"
        >
        HTML
      </label>
    </div>

    <div class="flex gap-2 border-b border-slate-200 text-sm font-semibold dark:border-slate-700">
      <button
        type="button"
        class="border-b-2 px-3 py-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950"
        :class="tab === 'write' ? 'border-slate-900 text-slate-900 dark:border-slate-100 dark:text-slate-100' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
        @click="tab = 'write'"
      >
        Write
      </button>
      <button
        type="button"
        class="border-b-2 px-3 py-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        :class="tab === 'preview' ? 'border-slate-900 text-slate-900 dark:border-slate-100 dark:text-slate-100' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
        @click="tab = 'preview'"
      >
        Preview
      </button>
    </div>

    <div v-show="tab === 'write'">
      <label for="blog-editor-content" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Content</label>
      <textarea
        id="blog-editor-content"
        :value="content"
        rows="14"
        class="form-textarea font-mono text-sm"
        @input="emit('update:content', $event.target.value)"
      />
    </div>

    <div
      v-show="tab === 'preview'"
      class="prose prose-slate max-w-none rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm dark:prose-invert dark:border-slate-700 dark:bg-slate-900/40"
      v-html="previewHtml"
    />
  </div>
</template>
