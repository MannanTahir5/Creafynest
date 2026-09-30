<script setup>
import { computed, onMounted, ref } from 'vue'
import { Moon, Sun } from 'lucide-vue-next'

const props = defineProps({
  /** Use `nav` on dark headers so the control stays visible. */
  variant: { type: String, default: 'default' },
})

const STORAGE_KEY = 'azee-theme'

const mode = ref('light')

onMounted(() => {
  mode.value = document.documentElement.classList.contains('dark') ? 'dark' : 'light'
})

function setMode(next) {
  document.documentElement.classList.toggle('dark', next === 'dark')
  try {
    localStorage.setItem(STORAGE_KEY, next)
  } catch {
    /* ignore */
  }
  mode.value = next
}

function toggle() {
  setMode(mode.value === 'dark' ? 'light' : 'dark')
}

const buttonClass = computed(() =>
  props.variant === 'nav'
    ? 'inline-flex items-center justify-center rounded-md p-2 text-slate-200 transition hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950'
    : 'inline-flex items-center justify-center rounded-md p-2 text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100 dark:focus-visible:ring-offset-slate-950',
)
</script>

<template>
  <button
    type="button"
    :class="buttonClass"
    :aria-label="mode === 'dark' ? 'Switch to light theme' : 'Switch to dark theme'"
    @click="toggle"
  >
    <Sun v-if="mode === 'dark'" class="h-5 w-5" />
    <Moon v-else class="h-5 w-5" />
  </button>
</template>
