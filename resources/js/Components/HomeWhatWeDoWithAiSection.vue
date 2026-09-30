<script setup>
import { computed } from 'vue'
import * as LucideIcons from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
import Container from './Ui/Container.vue'

const { Sparkles, Star, MessagesSquare, Mic } = LucideIcons

const props = defineProps({
  section: { type: Object, default: null },
})

function iconComponent(name) {
  return LucideIcons[name] || Sparkles
}

function colorClass(theme) {
  const map = {
    amber: 'rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
    emerald: 'rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
    orange: 'rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400',
    violet: 'rounded-lg bg-violet-100 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400',
    sky: 'rounded-lg bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400',
    rose: 'rounded-lg bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400',
    slate: 'rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-700/30 dark:text-slate-200',
  }
  return map[theme] || map.amber
}

const fallback = {
  eyebrow: 'Our top services',
  heading: 'What We Do With AI',
  description:
    'We specialize in Generative AI development, creating intelligent systems capable of generating human-like content, including text, images, audio, and even code.',
  cards: [
    { title: 'Generative AI development', description: 'We specialize in Generative AI development, creating intelligent systems capable of generating human-like content, including text, images, audio, and even code.', href: '/services/generative-ai', icon: 'Sparkles', color_theme: 'violet', image_url: null, image_alt: '' },
    { title: 'Conversational AI development', description: 'We develop advanced Conversational AI solutions that enable seamless, human-like interactions between businesses and their customers.', href: '/services/rag-retrieval-systems', icon: 'MessagesSquare', color_theme: 'violet', image_url: null, image_alt: '' },
    { title: 'Voice AI development', description: 'We specialize in Voice AI development, creating intelligent voice-enabled solutions that enhance user interactions through natural and seamless communication.', href: '/services/voice-ai', icon: 'Mic', color_theme: 'violet', image_url: null, image_alt: '' },
  ],
}

const eyebrow = computed(() => props.section?.eyebrow || fallback.eyebrow)
const heading = computed(() => props.section?.heading || fallback.heading)
const description = computed(() => props.section?.description || fallback.description)
const cards = computed(() => (props.section?.cards?.length ? props.section.cards : fallback.cards))
</script>

<template>
  <section
    class="border-t border-slate-200/80 bg-[#f4f5f7] py-14 dark:border-slate-800 dark:bg-slate-950 sm:py-16 md:py-20"
    aria-labelledby="what-we-do-ai-heading"
  >
    <Container>
      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mx-auto inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold tracking-wide text-white shadow-sm dark:bg-slate-800"
        >
          <span class="flex items-center gap-0.5" aria-hidden="true">
            <Star class="h-3 w-3 fill-white text-white" stroke-width="0" />
            <Star class="h-3 w-3 fill-white text-white" stroke-width="0" />
          </span>
          {{ eyebrow }}
        </div>

        <h2
          id="what-we-do-ai-heading"
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.15rem] dark:text-white"
        >
          {{ heading }}
        </h2>

        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          {{ description }}
        </p>
      </div>

      <ul
        class="mx-auto mt-10 grid max-w-6xl list-none gap-6 sm:mt-12 md:mt-14 md:grid-cols-2 md:gap-7 lg:grid-cols-3"
      >
        <li
          v-for="(card, ci) in cards"
          :key="card.id ?? ci"
          class="flex h-full flex-col rounded-2xl border border-slate-200/90 bg-slate-100/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/80 sm:p-7"
        >
          <div
            class="flex h-12 w-12 shrink-0 items-center justify-center shadow-sm ring-1 ring-black/5 dark:ring-white/10"
            :class="colorClass(card.color_theme)"
          >
            <component :is="iconComponent(card.icon)" class="h-6 w-6" stroke-width="2" aria-hidden="true" />
          </div>

          <img
            v-if="card.image_url"
            :src="card.image_url"
            :alt="card.image_alt || card.title"
            class="mt-5 max-h-40 w-full rounded-lg object-cover ring-1 ring-slate-200/60 dark:ring-white/10"
            loading="lazy"
            decoding="async"
          >

          <h3
            class="mt-5 truncate text-lg font-bold tracking-tight text-slate-900 dark:text-white"
            :title="card.title"
          >
            {{ card.title }}
          </h3>

          <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            {{ card.description }}
          </p>

          <div v-if="card.href" class="mt-6">
            <Link
              :href="card.href"
              class="text-sm font-semibold text-blue-600 underline decoration-blue-600/80 underline-offset-2 transition hover:text-blue-700 dark:text-blue-400 dark:decoration-blue-400/80 dark:hover:text-blue-300"
            >
              Read more
            </Link>
          </div>
        </li>
      </ul>
    </Container>
  </section>
</template>
