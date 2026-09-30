<script setup>
import { computed } from 'vue'
import * as LucideIcons from 'lucide-vue-next'
import Container from './Ui/Container.vue'

const { Building2 } = LucideIcons

const props = defineProps({
  section: { type: Object, default: null },
})

function iconComponent(name) {
  return LucideIcons[name] || Building2
}

const fallback = {
  eyebrow: 'Who we serve',
  heading_lead: 'Industries',
  heading_highlight: 'We Transform',
  description:
    'From healthcare to logistics, we deliver tailored digital solutions that address industry-specific challenges and unlock growth.',
  industries: [
    { title: 'Healthcare', icon: 'HeartPulse' },
    { title: 'Finance & Banking', icon: 'Landmark' },
    { title: 'Retail & E-commerce', icon: 'ShoppingBag' },
    { title: 'Manufacturing', icon: 'Factory' },
    { title: 'Education', icon: 'GraduationCap' },
    { title: 'Real Estate', icon: 'Building2' },
    { title: 'Logistics', icon: 'Truck' },
    { title: 'Technology & SaaS', icon: 'Cpu' },
    { title: 'Food & Beverage', icon: 'UtensilsCrossed' },
    { title: 'Media & Entertainment', icon: 'Tv' },
    { title: 'Travel & Hospitality', icon: 'Plane' },
    { title: 'Insurance', icon: 'Shield' },
    { title: 'Legal', icon: 'Scale' },
    { title: 'Nonprofit', icon: 'HeartHandshake' },
  ],
}

const eyebrow = computed(() => props.section?.eyebrow || fallback.eyebrow)
const headingLead = computed(() => props.section?.heading_lead || fallback.heading_lead)
const headingHighlight = computed(() => props.section?.heading_highlight || fallback.heading_highlight)
const description = computed(() => props.section?.description || fallback.description)
const industries = computed(() => (props.section?.industries?.length ? props.section.industries : fallback.industries))
</script>

<template>
  <section class="bg-gradient-to-b from-slate-50/90 to-white py-16 dark:from-slate-900/50 dark:to-slate-950 sm:py-20 lg:py-24">
    <Container>
      <header class="mx-auto max-w-2xl text-center lg:max-w-3xl">
        <p
          v-if="eyebrow"
          class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-950/90 dark:text-indigo-200/90 sm:text-xs"
        >
          {{ eyebrow }}
        </p>
        <h2
          class="mt-3 flex flex-wrap items-baseline justify-center gap-x-1.5 gap-y-0.5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.5rem] dark:text-white"
        >
          <span v-if="headingLead">{{ headingLead }}</span>
          <span
            v-if="headingHighlight"
            class="bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-600 bg-clip-text text-transparent"
          >
            {{ headingHighlight }}
          </span>
        </h2>
        <p class="mt-5 max-w-xl text-pretty text-sm leading-relaxed text-slate-600 sm:mx-auto sm:text-base dark:text-slate-400">
          {{ description }}
        </p>
      </header>

      <div
        class="mt-14 grid grid-cols-1 gap-3.5 sm:grid-cols-2 sm:gap-4 md:grid-cols-3 lg:mt-20 lg:grid-cols-4 lg:gap-5"
      >
        <div
          v-for="(item, ii) in industries"
          :key="item.id ?? `${item.title}-${ii}`"
          class="flex min-h-[4rem] items-center gap-4 rounded-2xl border border-slate-200/70 bg-white/90 px-4 py-3.5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] ring-1 ring-slate-900/[0.02] backdrop-blur-[2px] transition hover:-translate-y-0.5 hover:border-slate-300/80 hover:shadow-[0_8px_24px_-8px_rgba(15,23,42,0.08)] dark:border-slate-700/60 dark:bg-slate-900/50 dark:ring-white/[0.03] dark:hover:border-slate-600"
        >
          <div
            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-100/90 to-indigo-50/80 dark:from-violet-950/70 dark:to-indigo-950/40"
          >
            <component
              :is="iconComponent(item.icon)"
              class="h-[22px] w-[22px] text-indigo-950 dark:text-violet-200"
              stroke-width="1.5"
              aria-hidden="true"
            />
          </div>
          <span class="text-left text-[0.8125rem] font-semibold leading-snug tracking-tight text-slate-900 sm:text-sm dark:text-slate-100">
            {{ item.title }}
          </span>
        </div>
      </div>
    </Container>
  </section>
</template>
