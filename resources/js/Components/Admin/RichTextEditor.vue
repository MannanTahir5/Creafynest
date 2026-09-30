<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  invalid: { type: Boolean, default: false },
  placeholder: { type: String, default: 'Write something...' },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue || '',
  extensions: [StarterKit],
  editorProps: {
    attributes: {
      class: 'rich-editor-content focus:outline-none min-h-[120px] px-4 py-3 text-sm leading-7 text-slate-800',
    },
  },
  onUpdate: ({ editor }) => {
    // Emit empty string when editor is empty rather than <p></p>
    const html = editor.isEmpty ? '' : editor.getHTML()
    emit('update:modelValue', html)
  },
})

// Sync when parent updates the value externally (e.g. edit page load)
watch(
  () => props.modelValue,
  (newVal) => {
    if (!editor.value) return
    const current = editor.value.isEmpty ? '' : editor.value.getHTML()
    if (current !== newVal) {
      editor.value.commands.setContent(newVal || '', false)
    }
  },
)

onBeforeUnmount(() => editor.value?.destroy())

function runCommand(fn) {
  editor.value?.chain().focus().run()
  fn(editor.value?.chain().focus())
}
</script>

<template>
  <div
    class="rich-editor overflow-hidden rounded-lg border bg-white transition-colors"
    :class="invalid ? 'border-rose-400 ring-1 ring-rose-400' : 'border-slate-300 focus-within:border-slate-500 focus-within:ring-1 focus-within:ring-slate-400'"
  >
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-0.5 border-b border-slate-200 bg-slate-50 px-2 py-1.5">
      <!-- Bold -->
      <button
        type="button"
        title="Bold"
        :class="editor?.isActive('bold') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleBold().run()"
      >
        B
      </button>

      <!-- Italic -->
      <button
        type="button"
        title="Italic"
        :class="editor?.isActive('italic') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm italic transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleItalic().run()"
      >
        I
      </button>

      <!-- Strike -->
      <button
        type="button"
        title="Strikethrough"
        :class="editor?.isActive('strike') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm line-through transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleStrike().run()"
      >
        S
      </button>

      <div class="mx-1 h-5 w-px bg-slate-300" />

      <!-- H2 -->
      <button
        type="button"
        title="Heading 2"
        :class="editor?.isActive('heading', { level: 2 }) ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
      >
        H2
      </button>

      <!-- H3 -->
      <button
        type="button"
        title="Heading 3"
        :class="editor?.isActive('heading', { level: 3 }) ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
      >
        H3
      </button>

      <div class="mx-1 h-5 w-px bg-slate-300" />

      <!-- Bullet list -->
      <button
        type="button"
        title="Bullet list"
        :class="editor?.isActive('bulletList') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleBulletList().run()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1" fill="currentColor" stroke="none"/></svg>
      </button>

      <!-- Ordered list -->
      <button
        type="button"
        title="Ordered list"
        :class="editor?.isActive('orderedList') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleOrderedList().run()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
      </button>

      <!-- Blockquote -->
      <button
        type="button"
        title="Blockquote"
        :class="editor?.isActive('blockquote') ? 'bg-slate-200 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="rounded px-2 py-1 text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().toggleBlockquote().run()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 4h6v6H5c0 2 1 4 4 4v2c-5 0-6-4-6-8V4zm12 0h6v6h-4c0 2 1 4 4 4v2c-5 0-6-4-6-8V4z"/></svg>
      </button>

      <div class="mx-1 h-5 w-px bg-slate-300" />

      <!-- Undo -->
      <button
        type="button"
        title="Undo"
        :disabled="!editor?.can().undo()"
        class="rounded px-2 py-1 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-40 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().undo().run()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M3 13C5.33 6.36 13.5 3.5 20 8a9 9 0 0 1 1 13"/></svg>
      </button>

      <!-- Redo -->
      <button
        type="button"
        title="Redo"
        :disabled="!editor?.can().redo()"
        class="rounded px-2 py-1 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-40 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
        @click="editor?.chain().focus().redo().run()"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"/><path d="M21 13C18.67 6.36 10.5 3.5 4 8a9 9 0 0 0-1 13"/></svg>
      </button>
    </div>

    <!-- Editor content area -->
    <EditorContent :editor="editor" />
  </div>
</template>

<style>
/* Tiptap editor prose styles — scoped to .rich-editor-content */
.rich-editor-content h2 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-top: 1rem;
  margin-bottom: 0.25rem;
  color: #0f172a;
}
.rich-editor-content h3 {
  font-size: 1rem;
  font-weight: 600;
  margin-top: 0.75rem;
  margin-bottom: 0.25rem;
  color: #0f172a;
}
.rich-editor-content p {
  margin-bottom: 0.5rem;
}
.rich-editor-content ul {
  list-style-type: disc;
  padding-left: 1.25rem;
  margin-bottom: 0.5rem;
}
.rich-editor-content ol {
  list-style-type: decimal;
  padding-left: 1.25rem;
  margin-bottom: 0.5rem;
}
.rich-editor-content li {
  margin-bottom: 0.2rem;
}
.rich-editor-content blockquote {
  border-left: 3px solid #cbd5e1;
  padding-left: 0.75rem;
  color: #64748b;
  font-style: italic;
  margin: 0.5rem 0;
}
.rich-editor-content strong {
  font-weight: 700;
}
.rich-editor-content em {
  font-style: italic;
}
.rich-editor-content s {
  text-decoration: line-through;
}
.rich-editor-content p.is-editor-empty:first-child::before {
  color: #94a3b8;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}
</style>
