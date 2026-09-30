<script setup>
import WebUiUxHero from '../../Components/WebUiUxHero.vue'
import WebUiUxTopServicesSection from '../../Components/WebUiUxTopServicesSection.vue'
import Container from '../../Components/Ui/Container.vue'
import { Link } from '@inertiajs/vue3'
import { LayoutTemplate, MousePointer2, Palette } from 'lucide-vue-next'
</script>

<template>
  <div>
    <WebUiUxHero />

    <WebUiUxTopServicesSection />

    <section class="relative overflow-hidden border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950 sm:py-20 md:py-24">
      <div
        class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-violet-50/80 to-transparent dark:from-violet-950/30"
        aria-hidden="true"
      />
      <Container class="relative">
        <p class="text-center text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-600 dark:text-violet-400">
          Outcomes
        </p>
        <h2 class="mt-3 text-center font-sans text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl md:text-[2.125rem]">
          What we deliver
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-pretty text-center text-sm leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base">
          Research-backed UI, accessible components, and a handoff your engineers can ship with confidence.
        </p>
        <ul class="mt-12 grid gap-5 sm:grid-cols-3 sm:gap-6">
          <li
            class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-7 shadow-sm shadow-slate-900/[0.03] ring-1 ring-slate-900/[0.03] transition duration-300 hover:-translate-y-1 hover:border-violet-200/90 hover:shadow-xl hover:shadow-violet-500/[0.08] dark:border-slate-800 dark:from-slate-900 dark:to-slate-950 dark:shadow-none dark:ring-white/[0.06] dark:hover:border-violet-500/35"
          >
            <div
              class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/15 to-blue-600/5 ring-1 ring-blue-500/20 dark:from-blue-400/20 dark:to-blue-600/10 dark:ring-blue-400/25"
            >
              <LayoutTemplate class="h-7 w-7 text-blue-600 dark:text-blue-400" stroke-width="1.5" aria-hidden="true" />
            </div>
            <h3 class="mt-5 font-semibold text-slate-900 dark:text-white">Product &amp; marketing sites</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
              IA, key screens, and responsive layouts that match your brand and performance budget.
            </p>
          </li>
          <li
            class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-7 shadow-sm shadow-slate-900/[0.03] ring-1 ring-slate-900/[0.03] transition duration-300 hover:-translate-y-1 hover:border-violet-200/90 hover:shadow-xl hover:shadow-violet-500/[0.08] dark:border-slate-800 dark:from-slate-900 dark:to-slate-950 dark:shadow-none dark:ring-white/[0.06] dark:hover:border-violet-500/35"
          >
            <div
              class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500/15 to-fuchsia-600/5 ring-1 ring-violet-500/20 dark:from-violet-400/20 dark:to-fuchsia-600/10 dark:ring-violet-400/25"
            >
              <MousePointer2 class="h-7 w-7 text-violet-600 dark:text-violet-400" stroke-width="1.5" aria-hidden="true" />
            </div>
            <h3 class="mt-5 font-semibold text-slate-900 dark:text-white">Design systems</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
              Tokens, components, and documentation so every new feature stays on-brand.
            </p>
          </li>
          <li
            class="group rounded-3xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 p-7 shadow-sm shadow-slate-900/[0.03] ring-1 ring-slate-900/[0.03] transition duration-300 hover:-translate-y-1 hover:border-violet-200/90 hover:shadow-xl hover:shadow-violet-500/[0.08] dark:border-slate-800 dark:from-slate-900 dark:to-slate-950 dark:shadow-none dark:ring-white/[0.06] dark:hover:border-violet-500/35"
          >
            <div
              class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500/15 to-teal-600/5 ring-1 ring-cyan-500/25 dark:from-cyan-400/20 dark:to-teal-600/10 dark:ring-cyan-400/30"
            >
              <Palette class="h-7 w-7 text-cyan-600 dark:text-cyan-400" stroke-width="1.5" aria-hidden="true" />
            </div>
            <h3 class="mt-5 font-semibold text-slate-900 dark:text-white">UX refinement</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
              Flow fixes, usability passes, and measurable improvements to completion and retention.
            </p>
          </li>
        </ul>

        <div class="mt-14 text-center">
          <Link
            href="/contact"
            class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl border border-slate-200 bg-slate-900 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98] dark:border-white/10 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-100 dark:focus-visible:ring-white/50"
          >
            Discuss your UI / UX
          </Link>
        </div>
      </Container>
    </section>
  </div>
</template>
