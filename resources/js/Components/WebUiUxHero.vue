<script setup>
import { Link } from '@inertiajs/vue3'
import { Sparkles, Palette, Layers, Accessibility } from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import heroIllustration from '../assets/web-uiux-hero.png'
</script>

<template>
  <div class="bg-slate-950/95 backdrop-blur-sm">
    <section
      class="relative w-full overflow-hidden rounded-t-[2.75rem] bg-gradient-to-br from-violet-100/90 via-white to-indigo-100/85 pb-20 pt-10 dark:from-slate-900 dark:via-slate-950 dark:to-violet-950/35 sm:rounded-t-[3.75rem] sm:pb-28 sm:pt-14 md:rounded-t-[4.5rem] md:pt-16 lg:rounded-t-[5rem] lg:pb-32"
      aria-labelledby="web-uiux-hero-heading"
    >
      <!-- Atmosphere (Services-aligned) -->
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

      <div
        class="pointer-events-none absolute inset-0 opacity-[0.14] dark:opacity-[0.1]"
        style="background-image: linear-gradient(rgba(100,116,139,0.22) 1px, transparent 1px), linear-gradient(90deg, rgba(100,116,139,0.22) 1px, transparent 1px); background-size: 52px 52px;"
        aria-hidden="true"
      />

      <!-- Flow line -->
      <svg
        class="pointer-events-none absolute bottom-[5%] left-0 right-0 hidden h-32 w-full opacity-90 md:block"
        viewBox="0 0 1200 120"
        preserveAspectRatio="none"
        aria-hidden="true"
      >
        <defs>
          <linearGradient id="web-uiux-flow" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#a855f7" stop-opacity="0" />
            <stop offset="40%" stop-color="#a855f7" stop-opacity="0.45" />
            <stop offset="60%" stop-color="#6366f1" stop-opacity="0.4" />
            <stop offset="100%" stop-color="#06b6d4" stop-opacity="0" />
          </linearGradient>
        </defs>
        <path
          d="M0,75 C200,110 400,40 600,80 C800,120 1000,50 1200,70"
          fill="none"
          stroke="url(#web-uiux-flow)"
          stroke-width="1.5"
          class="drop-shadow-[0_0_12px_rgba(139,92,246,0.35)]"
        />
      </svg>

      <Container class="relative z-[1] px-4 sm:px-6">
        <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-2 lg:gap-12 xl:gap-20">
          <!-- Copy -->
          <div class="min-w-0 max-w-xl lg:max-w-none">
            <div
              class="inline-flex items-center gap-2.5 rounded-full border border-violet-200/90 bg-white/70 px-3 py-1.5 pl-2 pr-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-900 shadow-sm shadow-violet-500/5 ring-1 ring-violet-500/10 backdrop-blur-xl dark:border-white/10 dark:bg-slate-900/70 dark:text-violet-100 dark:ring-white/10 sm:text-xs"
            >
              <span
                class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 text-white shadow-md shadow-violet-500/30"
                aria-hidden="true"
              >
                <Sparkles class="h-3.5 w-3.5" stroke-width="2" />
              </span>
              <span>Website UI / UX</span>
            </div>

            <h1
              id="web-uiux-hero-heading"
              class="mt-7 text-balance font-sans font-bold tracking-tight text-slate-900 lg:mt-8 dark:text-white"
            >
              <span class="block text-[2.25rem] leading-[1.08] sm:text-5xl lg:text-[3.35rem] xl:text-[3.65rem]">
                <span class="text-slate-900 dark:text-white">We structure </span>
                <span
                  class="bg-gradient-to-r from-violet-600 via-fuchsia-500 to-indigo-600 bg-clip-text text-transparent dark:from-violet-400 dark:via-fuchsia-400 dark:to-indigo-400"
                >ideas</span>
              </span>
              <span
                class="mt-3 block max-w-xl text-[1.35rem] font-bold leading-snug text-slate-800 sm:mt-4 sm:text-2xl lg:text-[1.75rem] xl:text-[2rem] dark:text-slate-100"
              >
                into clear, beautiful products
              </span>
            </h1>

            <p class="mt-6 max-w-lg text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-400">
              Research, flows, design systems, and refined visual craft—so the first interaction with your product feels
              intentional, accessible, and premium.
            </p>

            <!-- UX credibility chips -->
            <div class="mt-8 flex flex-wrap gap-2">
              <span
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/75 px-3.5 py-2.5 text-xs font-medium text-slate-700 shadow-[0_1px_0_0_rgba(255,255,255,0.6)_inset] backdrop-blur-md dark:border-slate-600/80 dark:bg-slate-800/75 dark:text-slate-200 dark:shadow-none"
              >
                <Layers class="h-3.5 w-3.5 text-violet-500" stroke-width="2" aria-hidden="true" />
                Systems &amp; tokens
              </span>
              <span
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/75 px-3.5 py-2.5 text-xs font-medium text-slate-700 shadow-[0_1px_0_0_rgba(255,255,255,0.6)_inset] backdrop-blur-md dark:border-slate-600/80 dark:bg-slate-800/75 dark:text-slate-200 dark:shadow-none"
              >
                <Palette class="h-3.5 w-3.5 text-fuchsia-500" stroke-width="2" aria-hidden="true" />
                Visual craft
              </span>
              <span
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/75 px-3.5 py-2.5 text-xs font-medium text-slate-700 shadow-[0_1px_0_0_rgba(255,255,255,0.6)_inset] backdrop-blur-md dark:border-slate-600/80 dark:bg-slate-800/75 dark:text-slate-200 dark:shadow-none"
              >
                <Accessibility class="h-3.5 w-3.5 text-cyan-600 dark:text-cyan-400" stroke-width="2" aria-hidden="true" />
                Accessible patterns
              </span>
            </div>

            <div class="mt-10 flex flex-wrap gap-3 sm:gap-4">
              <Link
                href="/contact"
                class="group inline-flex min-h-[3rem] items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:shadow-xl hover:shadow-violet-500/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white motion-safe:active:scale-[0.98] dark:focus-visible:ring-offset-slate-950"
              >
                Start a project
                <span class="transition-transform group-hover:translate-x-0.5" aria-hidden="true">→</span>
              </Link>
              <Link
                href="/services"
                class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl border border-slate-300/80 bg-white/90 px-8 py-3 text-sm font-semibold text-slate-900 shadow-sm backdrop-blur-sm transition hover:border-violet-300/80 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:border-slate-600 dark:bg-slate-800/80 dark:text-white dark:hover:border-violet-500/40 dark:hover:bg-slate-800 motion-safe:active:scale-[0.98] dark:focus-visible:ring-offset-slate-950"
              >
                All services
              </Link>
            </div>
          </div>

          <!-- Visual stage: transparent isometric laptop / tablet / phone illustration -->
          <div class="relative mx-auto w-full min-w-0 max-w-lg lg:mx-0 lg:max-w-none">
            <!-- Ambient stacks -->
            <div
              class="pointer-events-none absolute -right-8 top-1/2 h-[85%] w-[70%] -translate-y-1/2 rounded-[2rem] bg-gradient-to-br from-fuchsia-500/25 via-violet-500/15 to-cyan-500/20 blur-3xl dark:from-fuchsia-600/20 dark:via-violet-600/15 dark:to-cyan-600/15"
              aria-hidden="true"
            />
            <div
              class="pointer-events-none absolute -left-10 bottom-8 h-40 w-40 rounded-full bg-violet-400/30 blur-3xl dark:bg-violet-600/25"
              aria-hidden="true"
            />

            <div class="relative z-10 mx-auto pb-24 sm:pb-28 lg:ml-auto lg:mr-0 lg:max-w-[min(100%,32rem)] xl:max-w-[38rem]">
              <!-- Outer chroma ring -->
              <div
                class="absolute -inset-px rounded-[1.9rem] bg-gradient-to-br from-violet-500/45 via-fuchsia-400/30 to-cyan-400/35 opacity-90 blur-[1px] dark:opacity-75"
                aria-hidden="true"
              />
              <div
                class="relative overflow-hidden rounded-[1.8rem] border border-violet-200/50 bg-gradient-to-b from-violet-50/25 via-transparent to-indigo-50/20 shadow-[0_28px_70px_-18px_rgba(91,33,182,0.28)] ring-1 ring-violet-500/10 backdrop-blur-md dark:border-white/10 dark:from-slate-900/35 dark:via-transparent dark:to-violet-950/25 dark:shadow-[0_44px_100px_-24px_rgba(0,0,0,0.55)] dark:ring-white/[0.06]"
              >
                <div class="relative overflow-hidden rounded-[1.65rem] bg-transparent">
                  <div
                    class="pointer-events-none absolute inset-0 rounded-[1.5rem] bg-gradient-to-tr from-violet-400/[0.05] via-transparent to-cyan-400/[0.04]"
                    aria-hidden="true"
                  />
                  <img
                    :src="heroIllustration"
                    alt="Isometric 3D illustration of a laptop, tablet, and smartphone with dashboards, code, and data visualizations, with floating PHP, C++, and CSS labels on a transparent background"
                    class="relative z-[1] h-auto w-full object-contain object-center px-2 py-3 sm:px-4 sm:py-5 transition duration-500 ease-out motion-safe:hover:scale-[1.012] drop-shadow-[0_20px_50px_rgba(91,33,182,0.2)] dark:drop-shadow-[0_24px_60px_rgba(0,0,0,0.4)]"
                    width="960"
                    height="720"
                    decoding="async"
                    fetchpriority="high"
                  >
                </div>
              </div>

              <!-- Floating caption card — editorial layer -->
              <div
                class="absolute -bottom-4 left-4 right-4 z-20 mx-auto max-w-sm rounded-2xl border border-white/70 bg-white/95 px-4 py-3.5 shadow-[0_22px_50px_-12px_rgba(91,33,182,0.22)] backdrop-blur-xl dark:border-slate-600/80 dark:bg-slate-900/95 dark:shadow-black/50 sm:left-auto sm:right-6 sm:mx-0"
              >
                <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-violet-600 dark:text-violet-300">
                  Interface layer
                </p>
                <p class="mt-1.5 text-sm font-medium leading-snug text-slate-800 dark:text-slate-100">
                  Production-ready UI — clarity, motion, and trust.
                </p>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>
  </div>
</template>
