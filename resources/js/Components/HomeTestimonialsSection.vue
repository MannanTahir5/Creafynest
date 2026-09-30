<script setup>
import { computed } from 'vue'
import { Quote, UserRound } from 'lucide-vue-next'
import Container from './Ui/Container.vue'
import Button from './Ui/Button.vue'
import EmptyState from './Ui/EmptyState.vue'

const props = defineProps({
  testimonials: { type: Array, default: () => [] },
  section: { type: Object, default: null },
})

const sectionEyebrow = computed(() => props.section?.eyebrow || 'Clients reviews')
const sectionHeading = computed(() => props.section?.heading || 'Testimonials of our valued customers')
const sectionDescription = computed(() => props.section?.description || 'Discover how our clients have achieved remarkable success through tailored solutions. Their words inspire us to keep delivering excellence and innovation.')

const total = computed(() => props.testimonials.length)

/** Up to three cards on the home page. */
const cards = computed(() => props.testimonials.slice(0, 3))

/** Optional two-paragraph feedback: first block = quote title, rest = body; else split on first sentence. */
function parseFeedback(text) {
  if (!text) return { title: '', body: '' }
  const trimmed = text.trim()
  const blocks = trimmed.split(/\n\n+/)
  if (blocks.length >= 2) {
    return { title: blocks[0].trim(), body: blocks.slice(1).join('\n\n').trim() }
  }
  const idx = trimmed.search(/\.\s+/)
  if (idx >= 28 && idx <= 140) {
    return {
      title: trimmed.slice(0, idx + 1).trim(),
      body: trimmed.slice(idx + 1).trim(),
    }
  }
  return { title: 'An outstanding experience from start to finish', body: trimmed }
}

const cardsWithParsed = computed(() =>
  cards.value.map((t) => ({
    name: t.name,
    role: t.role,
    image: t.image,
    feedback: t.feedback,
    parsed: parseFeedback(t.feedback),
  })),
)

function initials(name) {
  return (name || '?')
    .split(/\s+/)
    .map((p) => p[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}
</script>

<template>
  <section
    class="bg-[#f9fafb] py-14 dark:bg-slate-950 sm:py-16 md:py-20"
    aria-labelledby="testimonials-heading"
  >
    <Container>
      <div class="mx-auto max-w-3xl text-center">
        <div
          class="mx-auto inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-100/90 px-3 py-1.5 text-xs font-medium text-slate-800 shadow-sm dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-200"
        >
          <span
            class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-[10px] font-bold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-200 dark:ring-slate-600"
            aria-hidden="true"
          >
            <UserRound class="h-3.5 w-3.5" stroke-width="2" />
          </span>
          {{ sectionEyebrow }}
        </div>

        <h2
          id="testimonials-heading"
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.15rem] dark:text-white"
        >
          {{ sectionHeading }}
        </h2>

        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          {{ sectionDescription }}
        </p>
      </div>

      <template v-if="total">
        <ul
          class="mx-auto mt-10 grid max-w-6xl list-none gap-6 sm:mt-12 md:mt-14 md:gap-7 lg:grid-cols-3"
          aria-label="Featured testimonials"
        >
          <li
            v-for="(card, i) in cardsWithParsed"
            :key="card.name + '-' + i"
            class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900"
          >
            <div class="relative flex flex-1 flex-col p-6 sm:p-7">
              <Quote
                class="pointer-events-none absolute right-5 top-5 h-10 w-10 text-slate-100 dark:text-slate-700/80"
                stroke-width="1"
                aria-hidden="true"
              />
              <h3
                class="relative pr-10 text-base font-bold leading-snug tracking-tight text-slate-900 dark:text-white"
              >
                {{ card.parsed.title }}
              </h3>
              <p
                class="relative mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400"
              >
                {{ card.parsed.body }}
              </p>

              <div class="relative mt-6 flex items-center gap-3 border-t border-slate-100 pt-5 dark:border-slate-700/80">
                <div
                  v-if="card.image"
                  class="h-11 w-11 shrink-0 overflow-hidden rounded-full ring-2 ring-white dark:ring-slate-800"
                >
                  <img
                    :src="card.image"
                    alt=""
                    class="h-full w-full object-cover"
                    width="44"
                    height="44"
                    loading="lazy"
                  >
                </div>
                <div
                  v-else
                  class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-slate-200 to-slate-300 text-xs font-bold text-slate-700 dark:from-slate-600 dark:to-slate-700 dark:text-slate-100"
                  aria-hidden="true"
                >
                  {{ initials(card.name) }}
                </div>
                <div class="min-w-0 text-left">
                  <p class="truncate text-sm font-bold text-slate-900 dark:text-white">
                    {{ card.name }}
                  </p>
                  <p v-if="card.role" class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    {{ card.role }}
                  </p>
                  <p v-else class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                    Valued client
                  </p>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </template>

      <div v-else class="mx-auto mt-10 max-w-lg">
        <EmptyState
          title="No testimonials yet"
          description="Add testimonials in the admin area to feature them here."
        >
          <Button href="/contact" variant="secondary">Get in touch</Button>
        </EmptyState>
      </div>
    </Container>
  </section>
</template>
