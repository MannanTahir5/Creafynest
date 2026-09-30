<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import * as LucideIcons from 'lucide-vue-next'
import Container from '../../Components/Ui/Container.vue'
import { themeForCategorySlug } from '../../config/serviceCategoryThemes.js'
import { simpleIconSvgUrl } from '../../utils/simpleIconCdn.js'

const { Sparkles, Star } = LucideIcons

const props = defineProps({
  service: { type: Object, required: true },
})

const theme = computed(() => themeForCategorySlug(props.service.category?.slug))

const categoryHref = computed(() =>
  props.service.category?.slug ? `/services#${props.service.category.slug}` : '/services',
)

function iconComponent(name) {
  return LucideIcons[name] || Sparkles
}

function techIconSrc(slug) {
  return simpleIconSvgUrl(slug)
}

const brokenTechIcons = ref({})
const heroImageBroken = ref(false)

/** Served from public/ — must bind :src (Vite treats static src="/…" as a module import). */
const FALLBACK_SERVICE_HERO_SRC = '/images/fallbacks/service-hero.svg'

const displayHeroSrc = computed(() => {
  if (heroImageBroken.value) {
    return FALLBACK_SERVICE_HERO_SRC
  }

  return props.service.hero_image_url || FALLBACK_SERVICE_HERO_SRC
})

function onHeroImageError() {
  if (props.service.hero_image_url && !heroImageBroken.value) {
    heroImageBroken.value = true
  }
}

function techStackItemId(t, index) {
  return `${t.slug}:${index}:${t.name ?? ''}`
}

function onTechIconError(id) {
  brokenTechIcons.value = { ...brokenTechIcons.value, [id]: true }
}

function techIconInitial(name) {
  const s = String(name ?? '').trim()
  const m = s.match(/[A-Za-z0-9]/)
  return m ? m[0].toUpperCase() : '?'
}

const hero = computed(() => props.service.content?.hero ?? {})
const tech = computed(() => props.service.content?.tech ?? {})
const outcomes = computed(() => props.service.content?.outcomes ?? {})
const snapshot = computed(() => props.service.content?.snapshot ?? {})
const snapshotParagraphs = computed(() => Array.isArray(snapshot.value?.paragraphs) ? snapshot.value.paragraphs : [])
const snapshotHasHeading = computed(() => {
  const s = snapshot.value
  return !!(s?.heading_prefix || s?.heading_highlight || s?.heading_suffix)
})
const hasSnapshotSection = computed(() => {
  return !!(snapshotHasHeading.value || snapshotParagraphs.value.length > 0 || props.service.snapshot_image_url)
})
</script>

<template>
  <div>
    <!-- HERO -->
    <div class="bg-slate-950/95 backdrop-blur-sm">
      <section
        :class="theme.heroSection"
        aria-labelledby="service-hero-heading"
      >
        <div :class="theme.heroBlobTL" aria-hidden="true" />
        <div :class="theme.heroBlobTR" aria-hidden="true" />
        <div :class="theme.heroCornerL" aria-hidden="true" />
        <div :class="theme.heroCornerR" aria-hidden="true" />
        <div :class="theme.heroBlobBottom" aria-hidden="true" />
        <div :class="theme.heroBlobBL" aria-hidden="true" />
        <div
          class="pointer-events-none absolute inset-0 opacity-[0.14] dark:opacity-[0.1]"
          style="background-image: linear-gradient(rgba(100,116,139,0.22) 1px, transparent 1px), linear-gradient(90deg, rgba(100,116,139,0.22) 1px, transparent 1px); background-size: 52px 52px;"
          aria-hidden="true"
        />

        <Container class="relative z-[1] px-4 sm:px-6">
          <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-2 lg:gap-12 xl:gap-20">
            <div class="min-w-0 max-w-xl lg:max-w-none">
              <Link
                v-if="service.category"
                :href="categoryHref"
                :class="theme.categoryPill"
              >
                <span :class="theme.categorySwatch" aria-hidden="true" />
                {{ service.category.title }}
              </Link>

              <div
                v-if="hero.eyebrow"
                :class="theme.eyebrowBadge"
              >
                <span :class="theme.eyebrowIcon" aria-hidden="true">
                  <Sparkles class="h-3.5 w-3.5" stroke-width="2" />
                </span>
                <span>{{ hero.eyebrow }}</span>
              </div>

              <h1
                id="service-hero-heading"
                class="mt-7 text-balance font-sans font-bold tracking-tight text-slate-900 lg:mt-8 dark:text-white"
              >
                <span class="block text-[2.25rem] leading-[1.08] sm:text-5xl lg:text-[3.35rem] xl:text-[3.65rem]">
                  <span class="text-slate-900 dark:text-white">{{ hero.heading_prefix }} </span>
                  <span :class="theme.headingHighlight">
                    {{ hero.heading_highlight }}
                  </span>
                </span>
                <span
                  v-if="hero.subhead"
                  class="mt-3 block max-w-xl text-[1.35rem] font-bold leading-snug text-slate-800 sm:mt-4 sm:text-2xl lg:text-[1.75rem] xl:text-[2rem] dark:text-slate-100"
                >
                  {{ hero.subhead }}
                </span>
              </h1>

              <p class="mt-6 max-w-lg text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-400">
                {{ hero.description }}
              </p>

              <div v-if="hero.chips?.length" class="mt-8 flex flex-wrap gap-2">
                <span
                  v-for="(chip, i) in hero.chips"
                  :key="i"
                  class="inline-flex items-center gap-2 rounded-2xl border border-slate-200/80 bg-white/75 px-3.5 py-2.5 text-xs font-medium text-slate-700 shadow-[0_1px_0_0_rgba(255,255,255,0.6)_inset] backdrop-blur-md dark:border-slate-600/80 dark:bg-slate-800/75 dark:text-slate-200 dark:shadow-none"
                >
                  <component :is="iconComponent(chip.icon)" :class="theme.chipIcon" stroke-width="2" aria-hidden="true" />
                  {{ chip.label }}
                </span>
              </div>

              <div class="mt-10 flex flex-wrap gap-3 sm:gap-4">
                <Link
                  :href="hero.primary_cta_href"
                  :class="theme.primaryCta"
                >
                  {{ hero.primary_cta_label }}
                  <span class="transition-transform group-hover:translate-x-0.5" aria-hidden="true">→</span>
                </Link>
                <Link
                  v-if="hero.secondary_cta_label && hero.secondary_cta_href"
                  :href="hero.secondary_cta_href"
                  :class="theme.secondaryCta"
                >
                  {{ hero.secondary_cta_label }}
                </Link>
              </div>
            </div>

            <div class="relative mx-auto w-full min-w-0 max-w-lg lg:mx-0 lg:max-w-none">
              <div :class="theme.heroImageGlow" aria-hidden="true" />
              <div :class="theme.heroImageBlob" aria-hidden="true" />

              <div class="relative z-10 mx-auto pb-24 sm:pb-28 lg:ml-auto lg:mr-0 lg:max-w-[min(100%,32rem)] xl:max-w-[38rem]">
                <div :class="theme.heroImageRing" aria-hidden="true" />
                <div :class="theme.heroImageFrame">
                  <div class="relative overflow-hidden rounded-[1.65rem] bg-transparent">
                    <div :class="theme.heroImageInner" aria-hidden="true" />
                    <img
                      :src="displayHeroSrc"
                      :alt="hero.image_alt || service.title"
                      :class="theme.heroImageDrop"
                      width="960"
                      height="720"
                      decoding="async"
                      fetchpriority="high"
                      @error="onHeroImageError"
                    >
                  </div>
                </div>

                <div
                  v-if="hero.caption_eyebrow || hero.caption_text"
                  :class="theme.heroCaption"
                >
                  <p v-if="hero.caption_eyebrow" :class="theme.heroCaptionEyebrow">
                    {{ hero.caption_eyebrow }}
                  </p>
                  <p v-if="hero.caption_text" class="mt-1.5 text-sm font-medium leading-snug text-slate-800 dark:text-slate-100">
                    {{ hero.caption_text }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Container>
      </section>
    </div>

    <!-- TECH STACK -->
    <section
      :class="theme.techSection"
      aria-labelledby="service-tech-heading"
    >
      <div :class="theme.techDivider" aria-hidden="true" />
      <div :class="theme.techBlobL" aria-hidden="true" />
      <div :class="theme.techBlobR" aria-hidden="true" />

      <Container class="relative z-[1] px-4 text-center sm:px-6">
        <div
          v-if="tech.eyebrow"
          class="mx-auto inline-flex items-center gap-2.5 rounded-full border border-slate-200/80 bg-white/80 px-3 py-1.5 pl-2 pr-4 shadow-sm shadow-slate-900/5 ring-1 ring-slate-900/[0.04] backdrop-blur-md dark:border-white/10 dark:bg-slate-900/70 dark:ring-white/10"
        >
          <span :class="theme.techEyebrowIcon" aria-hidden="true">
            <Star class="h-3.5 w-3.5 fill-current stroke-current" stroke-width="1.25" />
          </span>
          <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-700 dark:text-slate-200 sm:text-xs">{{ tech.eyebrow }}</span>
        </div>

        <h2
          id="service-tech-heading"
          class="mx-auto mt-8 max-w-4xl text-balance font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl md:text-[2.5rem] md:leading-[1.15] dark:text-white"
        >
          {{ tech.heading_prefix }}
          <span
            v-if="tech.heading_highlight"
            :class="theme.techHeadingHighlight"
          >
            {{ tech.heading_highlight }}
          </span>
        </h2>

        <p class="mx-auto mt-6 max-w-3xl text-pretty text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-400">
          {{ tech.description }}
        </p>

        <div class="mx-auto mt-12 max-w-5xl rounded-3xl border border-slate-200/70 bg-white/70 p-6 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/[0.03] backdrop-blur-md dark:border-white/10 dark:bg-slate-900/50 dark:shadow-black/40 dark:ring-white/[0.06] sm:mt-14 sm:p-8">
          <p class="mb-5 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
            Technologies we use
          </p>

          <img
            v-if="service.tech_image_url"
            :src="service.tech_image_url"
            :alt="tech.image_alt || (tech.heading_prefix + ' visual')"
            class="mx-auto mb-8 max-h-72 w-auto rounded-2xl object-contain"
            loading="lazy"
            decoding="async"
          >

          <div
            v-if="tech.items?.length"
            class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 md:gap-5"
            aria-label="Technologies we use"
          >
            <div
              v-for="(t, techIdx) in tech.items"
              :key="techStackItemId(t, techIdx)"
              :class="theme.techIconTile"
              :title="t.name"
            >
              <img
                v-if="!brokenTechIcons[techStackItemId(t, techIdx)]"
                :src="techIconSrc(t.slug)"
                :alt="t.name"
                class="h-7 w-7 object-contain opacity-90 sm:h-8 sm:w-8 dark:invert dark:opacity-95"
                width="32"
                height="32"
                loading="lazy"
                decoding="async"
                @error="onTechIconError(techStackItemId(t, techIdx))"
              >
              <span v-else class="relative flex h-7 w-7 items-center justify-center sm:h-8 sm:w-8">
                <span class="sr-only">{{ t.name }}</span>
                <span
                  :class="theme.techIconFallback"
                  aria-hidden="true"
                >{{ techIconInitial(t.name) }}</span>
              </span>
            </div>
          </div>
        </div>

        <div v-if="tech.cta_label && tech.cta_href" class="mt-12 sm:mt-14">
          <Link
            :href="tech.cta_href"
            :class="theme.techCta"
          >
            {{ tech.cta_label }}
          </Link>
        </div>
      </Container>
    </section>

    <!-- OUTCOMES (What we deliver) -->
    <section class="relative overflow-hidden border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950 sm:py-20 md:py-24">
      <div :class="theme.outcomesTopFade" aria-hidden="true" />
      <Container class="relative">
        <p v-if="outcomes.eyebrow" :class="theme.outcomesEyebrow">
          {{ outcomes.eyebrow }}
        </p>
        <h2 class="mt-3 text-center font-sans text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl md:text-[2.125rem]">
          {{ outcomes.heading }}
        </h2>
        <p class="mx-auto mt-4 max-w-2xl text-pretty text-center text-sm leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base">
          {{ outcomes.description }}
        </p>

        <img
          v-if="service.outcomes_image_url"
          :src="service.outcomes_image_url"
          :alt="outcomes.image_alt || outcomes.heading || 'Outcomes'"
          class="mx-auto mt-10 max-h-80 w-auto rounded-2xl object-contain"
          loading="lazy"
          decoding="async"
        >

        <ul
          v-if="outcomes.cards?.length"
          class="mt-12 grid gap-5 sm:grid-cols-3 sm:gap-6"
        >
          <li
            v-for="(card, i) in outcomes.cards"
            :key="i"
            :class="theme.outcomesCard"
          >
            <div :class="theme.outcomesCardIcon">
              <component :is="iconComponent(card.icon)" :class="theme.outcomesCardIconColor" stroke-width="1.5" aria-hidden="true" />
            </div>
            <h3 class="mt-5 font-semibold text-slate-900 dark:text-white">{{ card.title }}</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ card.description }}</p>
          </li>
        </ul>

        <div v-if="outcomes.cta_label && outcomes.cta_href" class="mt-14 text-center">
          <Link
            :href="outcomes.cta_href"
            class="inline-flex min-h-[3rem] items-center justify-center rounded-2xl border border-slate-200 bg-slate-900 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98] dark:border-white/10 dark:bg-white dark:text-slate-950 dark:shadow-none dark:hover:bg-slate-100 dark:focus-visible:ring-white/50"
          >
            {{ outcomes.cta_label }}
          </Link>
        </div>
      </Container>
    </section>

    <!-- SNAPSHOT -->
    <section
      v-if="hasSnapshotSection"
      class="relative border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950 sm:py-20 md:py-24"
      aria-labelledby="service-snapshot-heading"
    >
      <Container class="relative">
        <div
          :class="theme.snapshotPanel"
        >
          <div :class="theme.snapshotBlobTR" aria-hidden="true" />
          <div :class="theme.snapshotBlobBL" aria-hidden="true" />

          <div class="relative grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-14">
            <div class="min-w-0">
              <p
                v-if="snapshot.eyebrow"
                :class="theme.snapshotEyebrow"
              >
                {{ snapshot.eyebrow }}
              </p>

              <h2
                v-if="snapshotHasHeading"
                id="service-snapshot-heading"
                class="mt-4 font-sans text-3xl font-bold leading-[1.1] tracking-tight text-slate-900 dark:text-white sm:text-4xl md:text-[2.625rem] md:leading-[1.05]"
              >
                <span v-if="snapshot.heading_prefix">{{ snapshot.heading_prefix }} </span>
                <em
                  v-if="snapshot.heading_highlight"
                  class="not-italic"
                >
                  <span :class="theme.snapshotHeadingHighlight">
                    {{ snapshot.heading_highlight }}
                  </span>
                </em>
                <span v-if="snapshot.heading_suffix"> {{ snapshot.heading_suffix }}</span>
              </h2>

              <div
                v-if="snapshotParagraphs.length"
                class="mt-6 space-y-4 text-base leading-relaxed text-slate-600 dark:text-slate-400 sm:text-[1.0625rem]"
              >
                <p v-for="(p, i) in snapshotParagraphs" :key="i" class="whitespace-pre-line">
                  {{ p }}
                </p>
              </div>
            </div>

            <div class="relative">
              <div
                class="relative overflow-hidden rounded-2xl border border-slate-900/10 bg-slate-100 shadow-[0_30px_60px_-25px_rgba(15,23,42,0.45)] ring-1 ring-slate-900/5 dark:border-white/10 dark:bg-slate-900 dark:shadow-[0_30px_60px_-25px_rgba(0,0,0,0.6)] dark:ring-white/10"
              >
                <img
                  v-if="service.snapshot_image_url"
                  :src="service.snapshot_image_url"
                  :alt="snapshot.image_alt || snapshot.caption_title || 'Snapshot'"
                  class="block h-auto w-full object-cover"
                  loading="lazy"
                  decoding="async"
                >
                <div
                  v-else
                  class="flex aspect-[16/10] w-full items-center justify-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400"
                >
                  Add snapshot image
                </div>

                <div
                  v-if="(snapshot.caption_title || snapshot.caption_subtitle) && service.snapshot_image_url"
                  class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 via-black/40 to-transparent px-6 pb-5 pt-12"
                >
                  <p
                    v-if="snapshot.caption_title"
                    class="text-base font-semibold text-white sm:text-lg"
                  >
                    {{ snapshot.caption_title }}
                  </p>
                  <p
                    v-if="snapshot.caption_subtitle"
                    class="mt-1 text-xs text-slate-200 sm:text-sm"
                  >
                    {{ snapshot.caption_subtitle }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>

  </div>
</template>
