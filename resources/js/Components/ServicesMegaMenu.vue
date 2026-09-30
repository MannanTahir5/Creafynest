<script setup>
import { Link } from '@inertiajs/vue3'
import * as LucideIcons from 'lucide-vue-next'
import { servicesMegaMenuSections } from '../config/servicesMegaMenu.js'

const emit = defineEmits(['navigate'])

const { Box } = LucideIcons

function onNavigate() {
  emit('navigate')
}

const colorStyles = {
  blue: { swatch: 'bg-blue-500', title: 'text-blue-600 dark:text-blue-400' },
  emerald: { swatch: 'bg-emerald-500', title: 'text-emerald-600 dark:text-emerald-400' },
  violet: { swatch: 'bg-violet-500', title: 'text-violet-600 dark:text-violet-400' },
  amber: { swatch: 'bg-amber-500', title: 'text-amber-600 dark:text-amber-400' },
  rose: { swatch: 'bg-rose-500', title: 'text-rose-600 dark:text-rose-400' },
  fuchsia: { swatch: 'bg-fuchsia-500', title: 'text-fuchsia-600 dark:text-fuchsia-400' },
  orange: { swatch: 'bg-orange-500', title: 'text-orange-600 dark:text-orange-400' },
  sky: { swatch: 'bg-sky-500', title: 'text-sky-600 dark:text-sky-400' },
  pink: { swatch: 'bg-pink-500', title: 'text-pink-600 dark:text-pink-400' },
  cyan: { swatch: 'bg-cyan-500', title: 'text-cyan-600 dark:text-cyan-400' },
  indigo: { swatch: 'bg-indigo-500', title: 'text-indigo-600 dark:text-indigo-400' },
  slate: { swatch: 'bg-slate-500', title: 'text-slate-600 dark:text-slate-400' },
}

function iconFor(name) {
  return LucideIcons[name] || Box
}

function colorFor(key) {
  return colorStyles[key] || colorStyles.slate
}
</script>

<template>
  <div
    class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xl ring-1 ring-black/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10 sm:p-6"
  >
    <div
      class="max-h-[min(70vh,36rem)] space-y-8 overflow-y-auto pr-1 [-ms-overflow-style:none] [scrollbar-width:thin] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 dark:[&::-webkit-scrollbar-thumb]:bg-slate-600"
    >
      <section
        v-for="section in servicesMegaMenuSections"
        :key="section.id"
        class="scroll-mt-2"
      >
        <div class="sticky top-0 z-10 -mx-1 mb-4 border-b border-slate-200/90 bg-white/95 px-1 pb-3 backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/95">
          <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-800 dark:text-slate-100">
            {{ section.label }}
          </h3>
        </div>

        <div
          class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
          :class="{
            'xl:grid-cols-4': section.id === 'development',
            'xl:grid-cols-5': section.id === 'design' || section.id === 'ai',
          }"
        >
          <div
            v-for="col in section.columns"
            :key="col.id"
            class="min-w-0"
          >
            <div class="flex items-center gap-2">
              <span
                class="h-2.5 w-2.5 shrink-0 rounded-sm"
                :class="colorFor(col.color).swatch"
                aria-hidden="true"
              />
              <p
                class="text-xs font-bold uppercase tracking-wide"
                :class="colorFor(col.color).title"
              >
                {{ col.title }}
              </p>
            </div>

            <ul class="mt-3 space-y-2.5">
              <li v-for="item in col.items" :key="item.title">
                <Link
                  :href="item.href"
                  class="group flex gap-2.5 rounded-lg p-2 -m-2 transition hover:bg-slate-50 dark:hover:bg-slate-800/80"
                  @click="onNavigate"
                >
                  <span class="mt-0.5 shrink-0 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300">
                    <component :is="iconFor(item.icon)" class="h-4 w-4" stroke-width="1.5" />
                  </span>
                  <span class="min-w-0">
                    <span class="block text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100">
                      {{ item.title }}
                    </span>
                    <span class="mt-0.5 block text-xs leading-relaxed text-slate-500 line-clamp-2 dark:text-slate-400">
                      {{ item.description }}
                    </span>
                  </span>
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </div>

    <div class="mt-5 border-t border-slate-200 pt-4 text-center dark:border-slate-700">
      <Link
        href="/services"
        class="text-sm font-semibold text-violet-600 hover:text-violet-500 dark:text-violet-400 dark:hover:text-violet-300"
        @click="onNavigate"
      >
        View all services →
      </Link>
    </div>
  </div>
</template>
