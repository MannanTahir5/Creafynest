<script setup>
import { Link } from '@inertiajs/vue3'

import Container from '../Components/Ui/Container.vue'
import HomeCaseStudiesSection from '../Components/HomeCaseStudiesSection.vue'
import ServicesPremiumCarouselSection from '../Components/ServicesPremiumCarouselSection.vue'
import ServicesProvenTrackSection from '../Components/ServicesProvenTrackSection.vue'
import HomePricingSection from '../Components/HomePricingSection.vue'
import { simpleIconSvgUrl } from '../utils/simpleIconCdn.js'

defineProps({
  recentCaseStudies: { type: Array, default: () => [] },
  deliveryCategories: { type: Array, default: () => [] },
})

/** Simple Icons slugs — same CDN pattern as elsewhere in the app */
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
  <div>
    <!-- Same surface as SiteNavbar (bg + blur) so curved corners blend -->
    <div class="bg-slate-950/95 backdrop-blur-sm">
      <!-- Hero: full-width, curved top; no bottom border / wave strip -->
      <section
        class="relative w-full overflow-hidden rounded-t-[2.75rem] bg-gradient-to-br from-violet-100/90 via-white to-indigo-100/85 pt-0 pb-20 dark:from-slate-900 dark:via-slate-950 dark:to-violet-950/35 sm:rounded-t-[3.75rem] sm:pb-24 md:rounded-t-[4.5rem] lg:rounded-t-[5rem] md:pb-28"
      >
      <!-- Top-corner curves: soft gradient “scoops” + fine arc strokes -->
      <div
        class="pointer-events-none absolute -left-24 -top-28 h-[17rem] w-[17rem] rounded-full bg-gradient-to-br from-violet-400/45 via-fuchsia-400/15 to-transparent blur-3xl dark:from-violet-600/30 dark:via-fuchsia-600/10"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute -right-20 -top-32 h-[18rem] w-[18rem] rounded-full bg-gradient-to-bl from-indigo-400/45 via-sky-400/15 to-transparent blur-3xl dark:from-indigo-500/28 dark:via-sky-500/10"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute left-3 top-3 h-14 w-14 rounded-tl-[1.35rem] border-l-2 border-t-2 border-violet-400/55 sm:left-5 sm:top-5 sm:h-16 sm:w-16 sm:rounded-tl-[1.75rem] dark:border-violet-400/35"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute right-3 top-3 h-14 w-14 rounded-tr-[1.35rem] border-r-2 border-t-2 border-indigo-400/55 sm:right-5 sm:top-5 sm:h-16 sm:w-16 sm:rounded-tr-[1.75rem] dark:border-indigo-400/35"
        aria-hidden="true"
      />

      <div
        class="pointer-events-none absolute -bottom-12 left-1/2 h-72 w-[min(90vw,52rem)] -translate-x-1/2 rounded-full bg-violet-300/25 blur-3xl dark:bg-violet-700/15"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute -left-16 bottom-0 h-52 w-52 rounded-full bg-cyan-200/35 blur-3xl dark:bg-cyan-600/15"
        aria-hidden="true"
      />

      <Container class="relative z-10 px-4 pb-10 pt-16 text-center sm:px-6 sm:pb-12 sm:pt-20 md:pb-14 md:pt-24">
        <div class="mx-auto max-w-3xl">
          <h1 class="font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-5xl dark:text-white">
            We are development experts
          </h1>
          <p class="mx-auto mt-6 text-sm leading-relaxed text-slate-600 sm:text-base md:text-lg dark:text-slate-400">
            From greenfield web apps to internal tools and MVPs, we ship maintainable products with clear UX and
            production-grade engineering. Our teams work across modern stacks—so you get speed, quality, and a roadmap
            that scales with your business.
          </p>

          <div
            class="mx-auto mt-10 flex max-w-xl flex-wrap items-center justify-center gap-4 sm:mt-12 sm:gap-5 md:max-w-none md:gap-6"
            aria-label="Technologies we work with"
          >
            <div
              v-for="tech in techIcons"
              :key="tech.slug"
              class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full border border-slate-200/90 bg-white/90 shadow-sm ring-1 ring-slate-900/5 backdrop-blur-sm dark:border-slate-600 dark:bg-slate-800/80 dark:ring-white/10 sm:h-[3.75rem] sm:w-[3.75rem]"
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

          <div class="mt-12 sm:mt-14">
            <Link
              href="/contact"
              class="inline-flex min-h-[3rem] items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:brightness-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-950"
            >
              Let’s talk about your project
            </Link>
          </div>
        </div>
      </Container>
      </section>
    </div>

    <ServicesPremiumCarouselSection :categories="deliveryCategories" />

    <HomeCaseStudiesSection
      heading-lead="Recent work &"
      :projects="recentCaseStudies"
    />

    <ServicesProvenTrackSection />

    <HomePricingSection />
  </div>
</template>
