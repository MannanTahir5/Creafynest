<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ArrowRight, Check } from 'lucide-vue-next'
import { gradientById } from '../config/heroGradients.js'

const props = defineProps({
  hero: { type: Object, default: null },
})

const defaults = {
  eyebrow: 'Premium digital solutions',
  trust_count: '200+',
  trust_text: 'Trusted by teams shipping real products',
  heading_line_one: 'Elevate your corporate',
  heading_line_two: 'digital presence',
  heading_gradient: 'cyan-violet-fuchsia',
  description:
    'We deliver premium web, mobile, and AI solutions tailored for the modern enterprise. Innovation meets elegance.',
  feature_lines: [
    'Production-grade engineering',
    'Modern UX & accessibility',
    'Pragmatic delivery from discovery to launch',
  ],
  primary_cta_label: 'Our services',
  primary_cta_href: '/services',
  secondary_cta_label: 'Contact us',
  secondary_cta_href: '/contact',
  avatars: [],
}

const content = computed(() => ({ ...defaults, ...(props.hero ?? {}) }))

const fallbackAvatarStyles = [
  'from-slate-400 to-slate-600',
  'from-violet-400 to-indigo-600',
  'from-emerald-400 to-teal-600',
  'from-amber-400 to-orange-600',
  'from-fuchsia-400 to-pink-600',
]

const featureLines = computed(() => {
  const lines = content.value.feature_lines
  if (!Array.isArray(lines)) return []
  return lines.map((s) => (typeof s === 'string' ? s.trim() : '')).filter(Boolean)
})

const avatars = computed(() => {
  const list = content.value.avatars
  return Array.isArray(list) ? list.filter((a) => a && a.image_url) : []
})

const useCustomAvatars = computed(() => avatars.value.length > 0)

const gradient = computed(() => gradientById(content.value.heading_gradient))
</script>

<template>
  <section
    id="home-hero"
    class="relative scroll-mt-20 overflow-x-hidden border-b border-slate-200/90 pb-12 pt-14 text-center text-slate-900 sm:scroll-mt-24 sm:pb-16 sm:pt-16 md:pb-20 md:pt-20 dark:border-slate-800/90 dark:text-slate-100"
    aria-labelledby="home-hero-heading"
  >
    <!-- Light + dark atmosphere -->
    <div
      class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-br from-[#f2efff] via-[#fafbff] to-[#e8f6ff] dark:from-slate-950 dark:via-slate-900 dark:to-violet-950/30"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-gradient-to-r from-violet-100/85 via-white/40 to-cyan-100/80 dark:from-violet-950/25 dark:via-slate-950/80 dark:to-cyan-950/20"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -left-[20%] top-[-10%] h-[75%] w-[65%] rounded-full bg-violet-200/50 blur-[100px] dark:bg-violet-600/15"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -right-[15%] top-[5%] h-[70%] w-[60%] rounded-full bg-sky-200/55 blur-[95px] dark:bg-cyan-600/10"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute left-1/2 top-[35%] h-[55%] w-[55%] -translate-x-1/2 rounded-full bg-white/70 blur-[80px] dark:bg-violet-500/5"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.5)_0%,transparent_42%,transparent_58%,rgba(224,242,254,0.35)_100%)] dark:bg-[linear-gradient(135deg,rgba(15,23,42,0.4)_0%,transparent_45%,transparent_55%,rgba(76,29,149,0.12)_100%)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 z-0 bg-[linear-gradient(to_right,rgb(148_163_184/0.12)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.12)_1px,transparent_1px)] bg-[size:2.5rem_2.5rem] [mask-image:linear-gradient(to_bottom,black_20%,transparent_95%)] dark:bg-[linear-gradient(to_right,rgb(148_163_184/0.08)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.08)_1px,transparent_1px)]"
      aria-hidden="true"
    />

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6">
      <div class="relative z-10 mx-auto max-w-4xl overflow-visible">
        <p
          v-if="content.eyebrow"
          class="text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-900/70 sm:text-xs dark:text-violet-300/85"
        >
          {{ content.eyebrow }}
        </p>

        <div
          v-if="content.trust_count || content.trust_text"
          class="mt-6 flex items-center justify-center -space-x-3"
        >
          <template v-if="useCustomAvatars">
            <div
              v-for="(av, i) in avatars"
              :key="av.id ?? i"
              class="relative z-[1] h-11 w-11 overflow-hidden rounded-full border-2 border-white bg-slate-100 shadow-md ring-1 ring-slate-200/80 dark:border-slate-800 dark:bg-slate-800 dark:ring-slate-700/80"
            >
              <img
                :src="av.image_url"
                :alt="av.image_alt || av.name || ''"
                class="h-full w-full object-cover"
                loading="lazy"
                decoding="async"
              >
            </div>
          </template>
          <template v-else>
            <div
              v-for="(g, i) in fallbackAvatarStyles"
              :key="i"
              class="relative z-[1] h-11 w-11 rounded-full border-2 border-white bg-gradient-to-br shadow-md ring-1 ring-slate-200/80 dark:border-slate-800 dark:ring-slate-700/80"
              :class="g"
            />
          </template>
          <div
            v-if="content.trust_count"
            class="relative z-[2] flex h-11 min-w-[2.75rem] items-center justify-center rounded-full border-2 border-white bg-slate-800 px-2 text-[11px] font-bold tracking-wide text-white shadow-md ring-1 ring-slate-300/60 dark:border-slate-700 dark:bg-violet-600 dark:ring-violet-400/30"
          >
            {{ content.trust_count }}
          </div>
        </div>
        <p
          v-if="content.trust_text"
          class="mt-3 text-xs text-slate-600 dark:text-slate-400"
        >
          {{ content.trust_text }}
        </p>

        <h1
          id="home-hero-heading"
          class="mt-8 overflow-visible pb-1 font-sans text-4xl font-bold leading-[1.2] tracking-tight text-slate-900 sm:text-5xl sm:leading-[1.15] md:text-[3.35rem] dark:text-white"
        >
          <span class="block">{{ content.heading_line_one }}</span>
          <span
            class="mt-2 block bg-clip-text pb-1.5 text-transparent"
            :class="gradient.headingClass"
            style="-webkit-box-decoration-break: clone; box-decoration-break: clone;"
          >
            {{ content.heading_line_two }}
          </span>
        </h1>

        <p class="relative z-10 mx-auto mt-6 max-w-2xl text-sm leading-relaxed text-slate-600 sm:mt-7 sm:text-base sm:leading-relaxed dark:text-slate-400">
          {{ content.description }}
        </p>

        <ul
          v-if="featureLines.length"
          class="mx-auto mt-8 flex max-w-3xl flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-700 dark:text-slate-300"
        >
          <li
            v-for="(line, i) in featureLines"
            :key="i"
            class="inline-flex items-center gap-1.5"
          >
            <span
              class="inline-flex h-5 w-5 items-center justify-center rounded-full text-white shadow-sm"
              :class="gradient.swatch"
              aria-hidden="true"
            >
              <Check class="h-3 w-3" stroke-width="3" />
            </span>
            {{ line }}
          </li>
        </ul>

        <div class="mt-9 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
          <Link
            :href="content.primary_cta_href"
            class="inline-flex items-center gap-2 rounded-xl px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:shadow-violet-900/40 dark:focus-visible:ring-violet-400 dark:focus-visible:ring-offset-slate-950"
            :class="gradient.primaryButtonClass"
          >
            {{ content.primary_cta_label }}
            <ArrowRight class="h-4 w-4 shrink-0" stroke-width="2.25" />
          </Link>
          <Link
            v-if="content.secondary_cta_label && content.secondary_cta_href"
            :href="content.secondary_cta_href"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300/90 bg-white/70 px-7 py-3.5 text-sm font-semibold text-slate-800 shadow-sm backdrop-blur-sm transition hover:border-slate-400 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-100 dark:hover:border-slate-500 dark:hover:bg-slate-800/90 dark:focus-visible:ring-slate-500 dark:focus-visible:ring-offset-slate-950"
          >
            {{ content.secondary_cta_label }}
          </Link>
        </div>

      </div>
    </div>
  </section>
</template>
