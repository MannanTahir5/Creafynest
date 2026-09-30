<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Check, Crown, Rocket, Sparkles, Zap } from 'lucide-vue-next'
import Container from './Ui/Container.vue'

const mode = ref('project')

const modes = [
  {
    id: 'project',
    label: 'Project',
    hint: 'Milestones & delivery',
  },
  {
    id: 'retainer',
    label: 'Retainer',
    hint: 'Ongoing partnership',
  },
]

const tiers = computed(() => {
  const base = [
    {
      key: 'sprint',
      name: 'Sprint',
      tag: 'Ship something real',
      icon: Zap,
      accent: 'from-amber-400 to-orange-500',
      ring: 'ring-amber-400/20',
      popular: false,
      blurb:
        mode.value === 'project'
          ? 'A tight scope, one roadmap, and a crisp handoff—ideal for MVPs, marketing sites, and proof-of-concept builds.'
          : 'A focused block of hours each month for iteration, polish, and small features without the overhead of a full squad.',
      highlights:
        mode.value === 'project'
          ? ['Defined scope & timeline', 'Weekly async updates', 'Staging + launch support', '30-day stabilization window']
          : ['Rolling priority queue', 'Monthly planning call', 'Async standups & Loom reviews', 'Light maintenance included'],
      cta: 'Discuss a sprint',
    },
    {
      key: 'scale',
      name: 'Scale',
      tag: 'Full product momentum',
      icon: Rocket,
      accent: 'from-violet-500 to-indigo-600',
      ring: 'ring-violet-500/35',
      popular: true,
      blurb:
        mode.value === 'project'
          ? 'Multi-phase delivery with UX, engineering, and QA woven together—when the product roadmap is serious but still ship-friendly.'
          : 'A dedicated cadence that feels embedded: roadmap hygiene, predictable velocity, and room for experimentation.',
      highlights:
        mode.value === 'project'
          ? ['Phased milestones & demos', 'Design systems & reusable UI', 'Integrations & performance passes', 'SLA-aligned bug triage']
          : ['Quarterly roadmap refresh', 'Shared backlog & grooming', 'Feature + hardening cycles', 'Slack channel with engineers'],
      cta: 'Plan this phase',
    },
    {
      key: 'orbit',
      name: 'Orbit',
      tag: 'Your long-term crew',
      icon: Crown,
      accent: 'from-fuchsia-500 to-purple-700',
      ring: 'ring-fuchsia-500/25',
      popular: false,
      blurb:
        mode.value === 'project'
          ? 'Enterprise-friendly delivery with architecture reviews, documentation, and the kind of continuity big initiatives need.'
          : 'A fractional product + platform team that can own outcomes across web, integrations, and internal tooling.',
      highlights:
        mode.value === 'project'
          ? ['Solution architecture docs', 'Security-minded reviews', 'Runbooks & handoff training', 'Optional on-site workshop days']
          : ['Named lead + rotating specialists', 'On-call coverage options', 'Infra & release strategy', 'Vendor + API governance'],
      cta: 'Book a discovery call',
    },
  ]

  return base
})
</script>

<template>
  <section
    class="relative isolate overflow-hidden border-t border-violet-100/80 bg-gradient-to-b from-white via-violet-50/40 to-slate-50 py-16 dark:border-slate-800 dark:from-slate-950 dark:via-violet-950/20 dark:to-slate-950 sm:py-20 md:py-24"
    aria-labelledby="pricing-heading"
  >
    <div
      class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_18%_12%,rgba(124,58,237,0.14),transparent_42%),radial-gradient(circle_at_88%_8%,rgba(99,102,241,0.12),transparent_40%),radial-gradient(circle_at_50%_110%,rgba(236,72,153,0.07),transparent_55%)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -left-32 top-1/4 -z-10 h-72 w-72 rounded-full bg-violet-400/15 blur-3xl dark:bg-violet-500/10"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -right-24 bottom-0 -z-10 h-80 w-80 rounded-full bg-indigo-400/12 blur-3xl dark:bg-indigo-500/10"
      aria-hidden="true"
    />

    <Container>
      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mx-auto inline-flex items-center gap-2 rounded-full border border-violet-200/90 bg-white/90 px-4 py-1.5 text-xs font-semibold text-violet-900 shadow-sm backdrop-blur dark:border-violet-500/30 dark:bg-slate-900/80 dark:text-violet-100"
        >
          <Sparkles class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400" stroke-width="2" aria-hidden="true" />
          Pricing & engagements
        </div>

        <h2
          id="pricing-heading"
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.65rem] dark:text-white"
        >
          Plans that bend to
          <span
            class="bg-gradient-to-r from-violet-600 via-indigo-600 to-fuchsia-600 bg-clip-text text-transparent dark:from-violet-400 dark:via-indigo-400 dark:to-fuchsia-400"
          >
            how you build
          </span>
        </h2>

        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          Pick an engagement shape—same craft, different gravity. Every tier is scoped with you; nothing cookie-cutter, nothing hidden in fine print.
        </p>

        <div
          class="mx-auto mt-8 inline-flex rounded-full border border-slate-200/90 bg-white/80 p-1 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/80"
          role="tablist"
          aria-label="Engagement style"
        >
          <button
            v-for="m in modes"
            :key="m.id"
            type="button"
            class="relative rounded-full px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500/60 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-950 sm:px-5"
            :class="
              mode === m.id
                ? 'bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-md shadow-violet-600/25'
                : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
            "
            role="tab"
            :aria-selected="mode === m.id"
            @click="mode = m.id"
          >
            <span class="block leading-tight">{{ m.label }}</span>
            <span
              class="mt-0.5 block text-[0.65rem] font-medium opacity-80"
              :class="mode === m.id ? 'text-violet-100' : 'text-slate-500 dark:text-slate-500'"
            >
              {{ m.hint }}
            </span>
          </button>
        </div>
      </div>

      <div class="mx-auto mt-14 grid max-w-6xl gap-6 lg:mt-16 lg:grid-cols-3 lg:gap-8 lg:items-end">
        <article
          v-for="tier in tiers"
          :key="tier.key"
          class="relative flex flex-col rounded-[1.65rem] border bg-white/90 p-6 shadow-[0_18px_50px_-28px_rgba(15,23,42,0.25)] ring-1 backdrop-blur-sm transition hover:-translate-y-0.5 hover:shadow-[0_22px_60px_-26px_rgba(91,33,182,0.22)] dark:bg-slate-900/75 sm:p-7"
          :class="[
            tier.popular
              ? 'border-violet-300/70 ring-2 ring-violet-500/35 dark:border-violet-500/40 lg:scale-[1.04] lg:z-10 lg:shadow-[0_28px_80px_-32px_rgba(91,33,182,0.45)]'
              : 'border-slate-200/80 ring-slate-200/60 dark:border-slate-700 dark:ring-slate-700/80',
          ]"
        >
          <div
            v-if="tier.popular"
            class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-1 text-[11px] font-bold uppercase tracking-widest text-white shadow-md shadow-violet-600/30"
          >
            Most chosen
          </div>

          <div class="flex items-start justify-between gap-4">
            <div
              class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg"
              :class="[tier.accent, tier.ring, 'shadow-black/10']"
            >
              <component :is="tier.icon" class="h-6 w-6" stroke-width="2" aria-hidden="true" />
            </div>
            <span
              class="rounded-full border border-slate-200/90 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200"
            >
              {{ tier.tag }}
            </span>
          </div>

          <h3 class="mt-5 font-sans text-xl font-bold text-slate-900 dark:text-white">
            {{ tier.name }}
          </h3>

          <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            {{ tier.blurb }}
          </p>

          <ul class="mt-6 space-y-3 text-sm text-slate-700 dark:text-slate-300" role="list">
            <li v-for="(item, hi) in tier.highlights" :key="hi" class="flex gap-3">
              <span
                class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"
              >
                <Check class="h-3.5 w-3.5" stroke-width="2.5" aria-hidden="true" />
              </span>
              <span class="leading-relaxed">{{ item }}</span>
            </li>
          </ul>

          <div class="mt-8 flex flex-col gap-3 border-t border-slate-100 pt-6 dark:border-slate-700/80">
            <Link
              href="/contact"
              class="inline-flex min-h-[2.75rem] items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-violet-600/25 transition hover:from-violet-500 hover:to-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-slate-950"
            >
              {{ tier.cta }}
            </Link>
            <p class="text-center text-xs text-slate-500 dark:text-slate-500">
              Custom quotes · clear milestones · no surprise line items
            </p>
          </div>
        </article>
      </div>

      <p class="mx-auto mt-12 max-w-2xl text-center text-xs leading-relaxed text-slate-500 dark:text-slate-500">
        Numbers depend on scope, integrations, and compliance needs. We'll give you a written estimate and a milestone map before kickoff—no pressure, no jargon.
      </p>
    </Container>
  </section>
</template>
