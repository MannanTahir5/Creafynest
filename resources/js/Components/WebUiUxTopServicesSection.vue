<script setup>
import { Link } from '@inertiajs/vue3'
import { Star } from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import { simpleIconSvgUrl } from '../utils/simpleIconCdn.js'

/** Nine icons — matches reference strip (Simple Icons CDN, same as Services page). */
const techIcons = [
  { name: 'Node.js', slug: 'nodedotjs' },
  { name: 'Sass', slug: 'sass' },
  { name: 'PHP', slug: 'php' },
  { name: 'Python', slug: 'python' },
  { name: '.NET', slug: 'dotnet' },
  { name: 'React', slug: 'react' },
  { name: 'Java', slug: 'java' },
  { name: 'Vue.js', slug: 'vuedotjs' },
  { name: 'Angular', slug: 'angular' },
]

function iconSrc(slug) {
  return simpleIconSvgUrl(slug)
}
</script>

<template>
  <section
    class="relative overflow-hidden border-t border-slate-200/70 bg-gradient-to-b from-slate-50 via-white to-violet-50/40 py-16 dark:border-slate-800/80 dark:from-slate-950 dark:via-slate-900 dark:to-violet-950/25 sm:py-20 md:py-24"
    aria-labelledby="web-uiux-top-services-heading"
  >
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-violet-300/60 to-transparent dark:via-violet-500/35"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -left-32 top-1/4 h-64 w-64 rounded-full bg-violet-400/15 blur-3xl dark:bg-violet-600/20"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -right-24 bottom-0 h-56 w-56 rounded-full bg-indigo-400/15 blur-3xl dark:bg-indigo-600/15"
      aria-hidden="true"
    />

    <Container class="relative z-[1] px-4 text-center sm:px-6">
      <div
        class="mx-auto inline-flex items-center gap-2.5 rounded-full border border-slate-200/80 bg-white/80 px-3 py-1.5 pl-2 pr-4 shadow-sm shadow-slate-900/5 ring-1 ring-slate-900/[0.04] backdrop-blur-md dark:border-white/10 dark:bg-slate-900/70 dark:ring-white/10"
      >
        <span
          class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-violet-600 to-indigo-700 text-white shadow-md shadow-violet-500/25"
          aria-hidden="true"
        >
          <Star class="h-3.5 w-3.5 fill-current stroke-current" stroke-width="1.25" />
        </span>
        <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-700 dark:text-slate-200 sm:text-xs">
          Web stack
        </span>
      </div>

      <h2
        id="web-uiux-top-services-heading"
        class="mx-auto mt-8 max-w-4xl text-balance font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.5rem] md:leading-[1.15] dark:text-white"
      >
        Web design
        <span
          class="bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent dark:from-violet-400 dark:to-indigo-400"
        >&amp; development</span>
      </h2>

      <p
        class="mx-auto mt-6 max-w-3xl text-pretty text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-400"
      >
        Full-cycle delivery—design, build, hosting, and ongoing care—using modern stacks our teams ship with every day.
        Reliable, scalable products built with the tools your roadmap already speaks.
      </p>

      <div
        class="mx-auto mt-12 max-w-5xl rounded-3xl border border-slate-200/70 bg-white/70 p-6 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/[0.03] backdrop-blur-md dark:border-white/10 dark:bg-slate-900/50 dark:shadow-black/40 dark:ring-white/[0.06] sm:mt-14 sm:p-8"
      >
        <p class="mb-5 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
          Technologies we use
        </p>
        <div
          class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 md:gap-5"
          aria-label="Technologies we use"
        >
          <div
            v-for="tech in techIcons"
            :key="tech.slug"
            class="flex h-[3.25rem] w-[3.25rem] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-b from-white to-slate-50 shadow-[0_8px_24px_-12px_rgba(15,23,42,0.15)] ring-1 ring-slate-900/[0.06] transition duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-violet-500/10 dark:from-slate-800 dark:to-slate-900 dark:shadow-black/40 dark:ring-white/10 sm:h-16 sm:w-16"
            :title="tech.name"
          >
            <img
              :src="iconSrc(tech.slug)"
              :alt="tech.name"
              class="h-7 w-7 object-contain opacity-90 sm:h-8 sm:w-8 dark:invert dark:opacity-95"
              width="32"
              height="32"
              loading="lazy"
              decoding="async"
            >
          </div>
        </div>
      </div>

      <div class="mt-12 sm:mt-14">
        <Link
          href="/contact"
          class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 px-10 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:shadow-xl hover:shadow-violet-500/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white motion-safe:active:scale-[0.98] dark:focus-visible:ring-offset-slate-950"
        >
          Let’s talk about your project
        </Link>
      </div>
    </Container>
  </section>
</template>
