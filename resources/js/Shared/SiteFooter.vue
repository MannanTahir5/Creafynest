<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Mail } from 'lucide-vue-next'

const page = usePage()
const year = computed(() => new Date().getFullYear())
const footer = computed(() => page.props.footer ?? {})
const contactEmail = 'Creafynest@gmail.com'

const serviceColumns = computed(() => {
  const columns = footer.value.columns ?? []

  return columns.filter((column) => {
    const key = String(column?.key || '').toLowerCase()
    return Array.isArray(column?.links) && column.links.length > 0 && !['consultancy', 'solutions'].includes(key)
  })
})
</script>

<template>
  <footer class="bg-[#141416] text-slate-400" role="contentinfo" aria-label="Site footer">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
      <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-center gap-4">
          <Link
            href="/"
            class="group inline-flex items-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[#141416]"
            :aria-label="footer.home_aria_label || 'Home'"
          >
            <img
              :src="footer.logo_url || '/images/creafynest-logo.png'"
              :alt="footer.logo_alt || 'Site logo'"
              class="h-10 w-auto"
              width="220"
              height="86"
              fetchpriority="low"
            >
          </Link>
        </div>

        <nav v-if="serviceColumns.length" aria-label="Service categories" class="flex-1">
          <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            <div v-for="column in serviceColumns" :key="column.key || column.heading" class="space-y-3">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                {{ column.heading }}
              </p>
              <ul class="space-y-2 text-sm">
                <li v-for="item in column.links" :key="item.label">
                  <Link :href="item.href" class="text-slate-300 transition hover:text-white">
                    {{ item.label }}
                  </Link>
                </li>
              </ul>
            </div>
          </div>
        </nav>

        <div class="shrink-0 lg:pt-1">
          <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact</p>
          <a
            :href="'mailto:' + contactEmail"
            class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white"
          >
            <Mail class="h-4 w-4" stroke-width="1.6" aria-hidden="true" />
            {{ contactEmail }}
          </a>
        </div>
      </div>

      <div class="mt-8 border-t border-white/10 pt-5 text-center text-xs text-slate-500 sm:text-left">
        © {{ year }} {{ footer.copyright_entity || 'Creafynest' }}. All rights reserved.
      </div>
    </div>
  </footer>
</template>
