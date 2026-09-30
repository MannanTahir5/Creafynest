<script setup>
import { computed } from 'vue'
import { Sparkles } from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import { simpleIconSvgUrl } from '../utils/simpleIconCdn.js'

const props = defineProps({
  section: { type: Object, default: null },
})

const fallbackEyebrow = 'Latest technologies'
const fallbackLead = 'Our core'
const fallbackHighlight = 'technologies'
const fallbackDescription =
  'We work across a modern stack to deliver effective, scalable, and future-proof custom web and mobile experiences.'

const fallbackRow1 = [
  { name: 'React', slug: 'react' },
  { name: 'Vue.js', slug: 'vuedotjs' },
  { name: 'Angular', slug: 'angular' },
  { name: 'HTML5', slug: 'html5' },
  { name: 'CSS3', slug: 'css3' },
  { name: 'Sass', slug: 'sass' },
  { name: 'Node.js', slug: 'nodedotjs' },
  { name: 'Java', slug: 'java' },
  { name: 'JavaScript', slug: 'javascript' },
  { name: 'Vite', slug: 'vite' },
  { name: 'Redis', slug: 'redis' },
  { name: 'PostgreSQL', slug: 'postgresql' },
  { name: 'MongoDB', slug: 'mongodb' },
  { name: 'GraphQL', slug: 'graphql' },
  { name: 'Alpine.js', slug: 'alpinejs' },
  { name: 'Jest', slug: 'jest' },
]

const fallbackRow2 = [
  { name: 'Laravel', slug: 'laravel' },
  { name: 'PHP', slug: 'php' },
  { name: 'TypeScript', slug: 'typescript' },
  { name: '.NET', slug: 'dotnet' },
  { name: 'Python', slug: 'python' },
  { name: 'Tailwind CSS', slug: 'tailwindcss' },
  { name: 'MySQL', slug: 'mysql' },
  { name: 'Docker', slug: 'docker' },
  { name: 'Git', slug: 'git' },
  { name: 'GitHub Actions', slug: 'githubactions' },
  { name: 'Nginx', slug: 'nginx' },
  { name: 'Kubernetes', slug: 'kubernetes' },
  { name: 'AWS', slug: 'amazonaws' },
  { name: 'PHPUnit', slug: 'phpunit' },
  { name: 'Composer', slug: 'composer' },
  { name: 'npm', slug: 'npm' },
  { name: 'Webpack', slug: 'webpack' },
  { name: 'ESLint', slug: 'eslint' },
  { name: 'Prettier', slug: 'prettier' },
]

function slugIcon(slug) {
  return simpleIconSvgUrl(slug)
}

const eyebrow = computed(() => props.section?.eyebrow || fallbackEyebrow)
const headingLead = computed(() => props.section?.heading_lead || fallbackLead)
const headingHighlight = computed(() => props.section?.heading_highlight || fallbackHighlight)
const description = computed(() => props.section?.description || fallbackDescription)

const row1 = computed(() => {
  if (props.section?.rowOne?.length) {
    return props.section.rowOne.map((t) => ({
      name: t.name,
      iconSrc: t.iconSrc,
      imageAlt: t.imageAlt,
    }))
  }
  return fallbackRow1.map((t) => ({ name: t.name, iconSrc: slugIcon(t.slug), imageAlt: '' }))
})

const row2 = computed(() => {
  if (props.section?.rowTwo?.length) {
    return props.section.rowTwo.map((t) => ({
      name: t.name,
      iconSrc: t.iconSrc,
      imageAlt: t.imageAlt,
    }))
  }
  return fallbackRow2.map((t) => ({ name: t.name, iconSrc: slugIcon(t.slug), imageAlt: '' }))
})

const srTechList = computed(() => [...row1.value, ...row2.value].map((t) => t.name).join(', '))
</script>

<template>
  <section
    class="border-t border-slate-200/80 bg-[#f8f9fa] py-14 dark:border-slate-800 dark:bg-slate-950 sm:py-16 md:py-20"
    aria-labelledby="core-technologies-heading"
  >
    <Container>
      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mx-auto inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200"
        >
          <span
            class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200"
            aria-hidden="true"
          >
            <Sparkles class="h-3.5 w-3.5" stroke-width="2" />
          </span>
          {{ eyebrow }}
        </div>

        <h2
          id="core-technologies-heading"
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-black sm:text-4xl md:text-[2.15rem] dark:text-white"
        >
          {{ headingLead }}
          <span v-if="headingHighlight" class="bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-violet-400 dark:to-purple-400">
            {{ headingHighlight }}
          </span>
        </h2>

        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          {{ description }}
        </p>
        <p class="sr-only">
          Logos shown in motion include: {{ srTechList }}.
        </p>
      </div>

      <!-- Row 1: marquee -->
      <div
        v-if="row1.length"
        class="relative mt-10 overflow-hidden sm:mt-12 md:mt-14"
        aria-hidden="true"
      >
        <div class="tech-marquee flex w-max gap-4 md:gap-5">
          <template v-for="dup in [1, 2]" :key="'r1-dup-' + dup">
            <div
              v-for="(tech, ti) in row1"
              :key="'r1-' + dup + '-' + ti"
              class="flex w-[11.5rem] shrink-0 items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3.5 shadow-[0_4px_14px_-4px_rgba(15,23,42,0.08)] sm:w-[12rem] dark:border-slate-700 dark:bg-slate-900 dark:shadow-none"
            >
              <img
                v-if="tech.iconSrc"
                :src="tech.iconSrc"
                :alt="tech.imageAlt || ''"
                class="h-9 w-9 shrink-0 object-contain opacity-90 dark:invert dark:opacity-95"
                width="36"
                height="36"
                loading="lazy"
                decoding="async"
              >
              <span class="text-left text-sm font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ tech.name }}
              </span>
            </div>
          </template>
        </div>
      </div>

      <!-- Row 2: reverse marquee -->
      <div
        v-if="row2.length"
        class="relative mt-4 overflow-hidden md:mt-5"
        aria-hidden="true"
      >
        <div class="tech-marquee tech-marquee-reverse flex w-max gap-4 md:gap-5">
          <template v-for="dup in [1, 2]" :key="'r2-dup-' + dup">
            <div
              v-for="(tech, ti) in row2"
              :key="'r2-' + dup + '-' + ti"
              class="flex w-[11.5rem] shrink-0 items-center gap-3 rounded-xl border border-slate-100 bg-white px-4 py-3.5 shadow-[0_4px_14px_-4px_rgba(15,23,42,0.08)] sm:w-[12rem] dark:border-slate-700 dark:bg-slate-900 dark:shadow-none"
            >
              <img
                v-if="tech.iconSrc"
                :src="tech.iconSrc"
                :alt="tech.imageAlt || ''"
                class="h-9 w-9 shrink-0 object-contain opacity-90 dark:invert dark:opacity-95"
                width="36"
                height="36"
                loading="lazy"
                decoding="async"
              >
              <span class="text-left text-sm font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ tech.name }}
              </span>
            </div>
          </template>
        </div>
      </div>
    </Container>
  </section>
</template>

<style scoped>
@keyframes tech-marquee {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(-50%);
  }
}

.tech-marquee {
  animation: tech-marquee 72s linear infinite;
}

.tech-marquee-reverse {
  animation-direction: reverse;
  animation-duration: 78s;
}

@media (prefers-reduced-motion: reduce) {
  .tech-marquee {
    animation: none;
    transform: none;
  }
}
</style>
