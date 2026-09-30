<script setup>
import Container from './Ui/Container.vue'
import { Sparkles } from 'lucide-vue-next'
import caseStudiesHeroImg from '../../images/case-studies-hero-mockups.png'
</script>

<template>
  <section
    class="relative overflow-hidden border-b border-slate-200/70 bg-gradient-to-b from-slate-50 via-white to-white py-20 sm:py-24 lg:py-28 dark:border-slate-800/80 dark:from-slate-950 dark:via-slate-950 dark:to-slate-950"
    aria-label="Case studies"
  >
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,rgba(124,58,237,0.11),transparent)] dark:bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,rgba(124,58,237,0.14),transparent)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgb(148_163_184/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.06)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgb(148_163_184/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.05)_1px,transparent_1px)]"
      aria-hidden="true"
    />

    <Container class="relative">
      <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-10 xl:gap-16">
        <div class="max-w-xl">
          <div
            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm backdrop-blur-sm dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200"
          >
            <Sparkles class="h-4 w-4 shrink-0 text-violet-600 dark:text-violet-400" aria-hidden="true" stroke-width="2" />
            <Sparkles class="h-4 w-4 shrink-0 text-violet-600 dark:text-violet-400" aria-hidden="true" stroke-width="2" />
            <span class="tracking-wide">Projects</span>
          </div>

          <h1 class="mt-6 text-4xl font-bold leading-[1.12] tracking-tight text-slate-900 sm:text-5xl lg:text-[3.25rem] dark:text-slate-100">
            Our Work Speaks Itself
          </h1>

          <p class="mt-5 text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-400">
            Boost efficiency, automate workflows, enhance decision-making, and unlock new opportunities with
            cutting-edge AI solutions tailored for success.
          </p>
        </div>

        <div class="relative mx-auto w-full max-w-xl lg:mx-0 lg:max-w-none lg:justify-self-end">
          <img
            :src="caseStudiesHeroImg"
            width="1200"
            height="675"
            class="mx-auto h-auto max-h-[min(26rem,55vh)] w-full rounded-2xl object-contain object-center lg:max-h-[28rem]"
            alt="Collage of shipped websites and product interfaces across industries."
            loading="eager"
            decoding="async"
          >
        </div>
      </div>
    </Container>
  </section>
</template>
