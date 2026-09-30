<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import DOMPurify from 'dompurify'
import { ArrowLeft, ArrowRight, ChevronLeft, ChevronRight, FolderOpen, Maximize2, X } from 'lucide-vue-next'
import Container from '../../Components/Ui/Container.vue'
import ContentImage from '../../Components/ContentImage.vue'

const props = defineProps({
  project: { type: Object, required: true },
})

const descriptionParagraphs = computed(() =>
  String(props.project.description || '')
    .split(/\n\s*\n|\r?\n/)
    .map((paragraph) => paragraph.trim())
    .filter(Boolean),
)

/**
 * Sanitize stored HTML from the rich text editor before rendering with v-html.
 * Falls back to plain-text paragraph rendering if the content has no HTML tags
 * (i.e. it was saved before the rich-text editor was introduced).
 */
function sanitizeHtml(raw) {
  if (!raw) return ''
  // If there are no HTML tags at all, wrap newline-split paragraphs in <p> tags
  // so existing plain-text records still display correctly.
  if (!/<[a-z][^>]*>/i.test(raw)) {
    return raw
      .split(/\n\s*\n|\r?\n/)
      .map((p) => p.trim())
      .filter(Boolean)
      .map((p) => `<p>${p}</p>`)
      .join('')
  }
  return DOMPurify.sanitize(raw, { USE_PROFILES: { html: true } })
}

const storySections = computed(() => {
  const visuals = props.project.gallery || []

  const sections = [
    {
      title: props.project.how_it_started_title || props.project.problem_title || '01 — How it started',
      text: props.project.how_it_started_text || props.project.problem_text || '',
      image: props.project.how_it_started_image || visuals[0] || null,
    },
    {
      title: props.project.challenge_title || props.project.system_title || '02 — The challenge',
      text: props.project.challenge_text || props.project.system_text || '',
      image: props.project.challenge_image || visuals[1] || visuals[0] || null,
    },
    {
      title: props.project.approach_title || '03 — Our approach',
      text: props.project.approach_text || '',
      image: props.project.approach_image || visuals[2] || visuals[1] || null,
    },
    {
      title: props.project.results_title || props.project.repeat_title || '04 — Results',
      text: props.project.results_text || props.project.repeat_text || props.project.next_text || '',
      image: props.project.results_image || visuals[3] || visuals[2] || null,
    },
  ]

  return sections.filter((section) => section.text)
})

const metaRows = computed(() => [
  { label: 'Client', value: props.project.client_name || '—' },
  { label: 'Location', value: props.project.location || '—' },
  { label: 'Industry', value: props.project.industry || '—' },
])

const servicesList = computed(() => props.project.services?.length ? props.project.services : props.project.tech_stack || [])
const techCount = computed(() => props.project.tech_stack?.length ?? 0)
const galleryStartIndex = ref(0)
const itemsPerPage = 3
const modalVisual = ref(null)

const galleryVisuals = computed(() => props.project.gallery?.filter(Boolean) ?? [])
const galleryCount = computed(() => galleryVisuals.value.length)

const visibleGalleryVisuals = computed(() => {
  const list = galleryVisuals.value
  if (!list.length) return []
  if (list.length <= itemsPerPage) {
    return list.map((item, idx) => ({ ...item, originalIndex: idx }))
  }

  const result = []
  for (let i = 0; i < itemsPerPage; i++) {
    const idx = (galleryStartIndex.value + i) % list.length
    result.push({ ...list[idx], originalIndex: idx })
  }
  return result
})

function previousGallery() {
  const count = galleryVisuals.value.length
  if (count <= 1) return
  galleryStartIndex.value = (galleryStartIndex.value - 1 + count) % count
}

function nextGallery() {
  const count = galleryVisuals.value.length
  if (count <= 1) return
  galleryStartIndex.value = (galleryStartIndex.value + 1) % count
}

function openModal(visual) {
  modalVisual.value = visual
}

function closeModal() {
  modalVisual.value = null
}

const heroVisual = computed(() => props.project.image_url
  ? { url: props.project.image_url, webp_url: props.project.image_webp_url || '' }
  : galleryVisuals.value[0] || null)

const videoPlayers = ref([])
let videoVisibilityObserver

function setVideoPlayer(element, index) {
  if (element) {
    videoPlayers.value[index] = element
  }
}

function setEmbedPlayback(frame, command) {
  frame?.contentWindow?.postMessage(JSON.stringify({
    event: 'command',
    func: command,
    args: '',
  }), '*')
}

function updateVideoPlayback(container, shouldPlay) {
  const video = container.querySelector('video')

  if (video) {
    if (shouldPlay) {
      video.play().catch(() => {})
    } else {
      video.pause()
    }

    return
  }

  const frame = container.querySelector('iframe')
  setEmbedPlayback(frame, shouldPlay ? 'playVideo' : 'pauseVideo')
}

onMounted(async () => {
  await nextTick()

  videoVisibilityObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => updateVideoPlayback(entry.target, entry.isIntersecting))
  }, { threshold: 0.45 })

  videoPlayers.value.forEach((player) => videoVisibilityObserver.observe(player))
})

onBeforeUnmount(() => videoVisibilityObserver?.disconnect())

const embedVideoUrl = (rawUrl) => {
  const raw = String(rawUrl || '').trim()

  if (!raw) {
    return ''
  }

  if (/^https?:\/\/(www\.)?youtube\.com\/embed\//i.test(raw) || /^https?:\/\/(www\.)?youtube-nocookie\.com\/embed\//i.test(raw)) {
    return raw
  }

  try {
    const url = new URL(raw)
    const host = url.hostname.replace(/^www\./, '').toLowerCase()

    if (host === 'youtu.be') {
      const id = url.pathname.replace('/', '').trim()
      return id ? `https://www.youtube.com/embed/${id}` : ''
    }

    if (host === 'youtube.com' || host === 'm.youtube.com') {
      const id = url.searchParams.get('v')
      if (id) {
        return `https://www.youtube.com/embed/${id}`
      }

      const shortId = url.pathname.match(/\/shorts\/(.+)/i)?.[1]
      if (shortId) {
        return `https://www.youtube.com/embed/${shortId}`
      }

      const liveId = url.pathname.match(/\/live\/(.+)/i)?.[1]
      if (liveId) {
        return `https://www.youtube.com/embed/${liveId}`
      }
    }
  } catch {
    // Ignore malformed URLs; fall back to the raw value below.
  }

  if (/\.(mp4|webm|ogg|ogv|mov|avi)(?:[?#]|$)/i.test(raw)) {
    return ''
  }

  return raw.includes('youtube.com') || raw.includes('youtu.be')
    ? raw.replace(/\/watch\?v=/i, '/embed/').replace(/\/shorts\//i, '/embed/').replace(/youtu\.be\//i, 'youtube.com/embed/')
    : raw
}

const autoplayEmbedUrl = (rawUrl) => {
  const embedUrl = embedVideoUrl(rawUrl)

  if (!embedUrl) {
    return ''
  }

  try {
    const url = new URL(embedUrl, window.location.origin)

    if (/(^|\.)youtube(?:-nocookie)?\.com$/i.test(url.hostname)) {
      url.searchParams.set('autoplay', '1')
      url.searchParams.set('mute', '1')
      url.searchParams.set('playsinline', '1')
      url.searchParams.set('enablejsapi', '1')
      url.searchParams.set('controls', '0')
      url.searchParams.set('disablekb', '1')
    }

    return url.toString()
  } catch {
    return embedUrl
  }
}

const videoUrls = computed(() => {
  const urls = Array.isArray(props.project.video_urls) && props.project.video_urls.length
    ? props.project.video_urls
    : props.project.video_url
      ? [props.project.video_url]
      : []

  return urls.filter(Boolean)
})

const overviewStats = computed(() => [
  { label: 'Category', value: props.project.category || 'Case Study' },
  { label: 'Stack', value: `${techCount.value} tools` },
  { label: 'Gallery', value: `${galleryCount.value} shots` },
])

</script>

<template>
  <main class="min-h-screen bg-[#f8fafc] text-slate-900 dark:bg-[#110d0d] dark:text-slate-100">
    <div class="bg-[#201312] text-white dark:bg-[#080505]">
      <div class="mx-auto flex h-[70px] max-w-6xl items-end justify-between px-4 sm:px-6 lg:px-8">
        <Link
          href="/portfolio"
          class="flex h-[52px] items-center gap-2 rounded-t-[22px] bg-white px-5 text-base font-medium text-slate-900 dark:bg-[#211817] dark:text-white sm:px-7"
        >
          <FolderOpen class="h-4 w-4 text-slate-400" aria-hidden="true" />
          Case Study
        </Link>
        <nav class="mb-4 flex items-center gap-5 text-sm font-semibold text-yellow-300 sm:gap-8" aria-label="Case study navigation">
          <Link
            v-if="project.previous_project"
            :href="`/portfolio/${project.previous_project.slug}`"
            class="inline-flex items-center gap-1 transition hover:text-yellow-100"
            :aria-label="`Previous case study: ${project.previous_project.title}`"
          >
            <ArrowLeft class="h-3.5 w-3.5" aria-hidden="true" />
            Previous
          </Link>
          <span v-else class="inline-flex items-center gap-1 text-white/30">
            <ArrowLeft class="h-3.5 w-3.5" aria-hidden="true" />
            Previous
          </span>
          <Link
            v-if="project.next_project"
            :href="`/portfolio/${project.next_project.slug}`"
            class="inline-flex items-center gap-1 transition hover:text-yellow-100"
            :aria-label="`Next case study: ${project.next_project.title}`"
          >
            Next
            <ArrowRight class="h-3.5 w-3.5" aria-hidden="true" />
          </Link>
          <span v-else class="inline-flex items-center gap-1 text-white/30">
            Next
            <ArrowRight class="h-3.5 w-3.5" aria-hidden="true" />
          </span>
        </nav>
      </div>
    </div>
    <Container>
      <div class="mx-auto max-w-6xl pb-16 pt-8 sm:pb-20 sm:pt-12">
        <section class="overflow-hidden rounded-b-[32px] border-x border-b border-slate-200 bg-white px-6 py-10 shadow-[0_30px_80px_-46px_rgba(33,19,18,0.32)] dark:border-[#43302e] dark:bg-[#211817] dark:shadow-[0_30px_80px_-46px_rgba(0,0,0,0.9)] sm:px-10 lg:px-12 lg:py-16">
          <div class="flex flex-col gap-8">
            <section class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
              <div class="max-w-xl">
                <img
                  v-if="project.logo_url"
                  :src="project.logo_url"
                  :alt="`${project.title} logo`"
                  class="mb-7 max-h-36 max-w-[300px] object-contain object-left"
                  :class="project.logo_is_light ? 'brightness-0 dark:brightness-100 dark:invert-0' : 'dark:brightness-0 dark:invert'"
                >
                <h1 class="text-4xl font-black tracking-[-0.06em] text-slate-950 dark:text-white sm:text-5xl">{{ project.title }}</h1>
                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                  <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-950 dark:text-white">Client</p>
                    <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ project.client_name || project.title }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-950 dark:text-white">Industry</p>
                    <p class="mt-1.5 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ project.industry || '—' }}</p>
                  </div>
                </div>
                <div v-if="servicesList.length" class="mt-5">
                  <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-950 dark:text-white">Services</p>
                  <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ servicesList.join(', ') }}</p>
                </div>
                <div v-if="project.live_url || project.github_url" class="mt-8 flex flex-wrap items-center gap-3">
                  <a
                    v-if="project.live_url"
                    :href="project.live_url"
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex items-center gap-2 bg-yellow-300 px-6 py-3 text-sm font-extrabold uppercase tracking-[0.16em] text-slate-950 transition hover:bg-yellow-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                  >
                    View website
                    <ArrowRight class="h-4 w-4" aria-hidden="true" />
                  </a>
                  <a
                    v-if="project.github_url"
                    :href="project.github_url"
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex items-center gap-2 border border-slate-300 bg-slate-100 px-5 py-3 text-sm font-extrabold uppercase tracking-[0.14em] text-slate-900 transition hover:bg-slate-200 dark:border-white/20 dark:bg-white/10 dark:text-white dark:hover:bg-white/20"
                  >
                    GitHub Repo
                    <ArrowRight class="h-4 w-4" aria-hidden="true" />
                  </a>
                </div>
              </div>
              <div v-if="heroVisual" class="relative mx-auto w-full max-w-xl pt-4">
                <div class="overflow-hidden rounded-xl border-[7px] border-slate-100 bg-slate-950 shadow-[0_24px_50px_-22px_rgba(15,23,42,0.6)] ring-1 ring-slate-300 dark:border-[#342524] dark:ring-[#57403d]">
                  <ContentImage
                    :src="heroVisual.url"
                    :webp-src="heroVisual.webp_url || ''"
                    :alt="`${project.title} website preview`"
                    :img-class="'h-auto w-full object-contain'"
                  />
                </div>
                <div class="mx-auto h-9 w-2/5 rounded-b-xl bg-gradient-to-b from-slate-300 to-slate-500 shadow-[0_14px_20px_-16px_rgba(15,23,42,0.9)] dark:from-[#4d3835] dark:to-[#1c1312]" />
                <div class="mx-auto h-2 w-3/5 rounded-full bg-slate-300 shadow-sm dark:bg-[#4d3835]" />
              </div>
            </section>

            <div v-if="descriptionParagraphs.length" class="border-t border-slate-200 pt-8 dark:border-[#43302e]">
              <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Overview</p>
              <div class="mt-4 space-y-4 text-base leading-8 text-slate-700 dark:text-slate-200 sm:text-lg sm:leading-9">
                <p v-for="(paragraph, index) in descriptionParagraphs" :key="index">{{ paragraph }}</p>
              </div>
            </div>

            <div v-if="storySections.length" class="space-y-12 border-t border-slate-200 pt-10 dark:border-[#43302e]">
              <div
                v-for="(section, index) in storySections"
                :key="`${section.title}-${index}`"
                class="grid gap-8 items-center lg:grid-cols-12"
              >
                <div :class="section.image ? 'lg:col-span-7' : 'lg:col-span-12'" class="order-2 lg:order-none">
                  <span class="inline-block rounded-full bg-amber-500/10 px-3.5 py-1 text-xs font-extrabold uppercase tracking-[0.2em] text-amber-600 dark:bg-yellow-400/10 dark:text-yellow-300">
                    {{ section.title }}
                  </span>
                  <div
                    v-if="section.text"
                    class="rich-content mt-4 text-sm leading-7 text-slate-700 dark:text-slate-200 sm:text-base sm:leading-8"
                    v-html="sanitizeHtml(section.text)"
                  />
                </div>

                <div
                  v-if="section.image"
                  class="lg:col-span-5 flex justify-center"
                  :class="index % 2 === 1 ? 'lg:order-first lg:justify-start' : 'lg:justify-end'"
                >
                  <div class="group relative max-w-sm overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-2 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-[#43302e] dark:bg-[#1a1211]">
                    <ContentImage
                      :src="section.image.url"
                      :webp-src="section.image.webp_url || ''"
                      :alt="`${section.title} visual`"
                      :img-class="'max-h-[260px] w-full rounded-xl object-cover shadow-inner'"
                    />
                  </div>
                </div>
              </div>
            </div>

            <section v-if="videoUrls.length || galleryVisuals.length" class="space-y-6 border-t border-slate-200 pt-8 dark:border-[#43302e]" aria-label="Project media">
              <div v-if="videoUrls.length" class="space-y-4">
                <div
                  v-for="(videoUrl, index) in videoUrls"
                  :key="`${videoUrl}-${index}`"
                  :ref="(element) => setVideoPlayer(element, index)"
                  class="overflow-hidden rounded-[24px] border border-slate-200 bg-slate-950 shadow-[0_28px_60px_-38px_rgba(76,29,149,0.6)]"
                >
                  <div class="aspect-video w-full overflow-hidden">
                    <iframe
                      v-if="embedVideoUrl(videoUrl)"
                      :src="autoplayEmbedUrl(videoUrl)"
                      class="pointer-events-none h-full w-full"
                      :title="`Project video ${index + 1}`"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen
                      referrerpolicy="strict-origin-when-cross-origin"
                    />
                    <video
                      v-else
                      :src="videoUrl"
                      class="h-full w-full object-cover"
                      autoplay
                      muted
                      playsinline
                    />
                  </div>
                </div>
              </div>

              <section
                v-if="galleryVisuals.length"
                class="overflow-hidden rounded-[24px] border border-slate-200 bg-slate-50/80 p-5 shadow-sm dark:border-[#43302e] dark:bg-[#17100f] sm:p-6"
                aria-label="Project image carousel"
              >
                <div class="mb-4 flex items-center justify-between">
                  <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-[0.18em] text-slate-900 dark:text-white">Project Gallery</h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ galleryVisuals.length }} visual{{ galleryVisuals.length > 1 ? 's' : '' }}</p>
                  </div>
                  <div v-if="galleryVisuals.length > 3" class="flex items-center gap-2">
                    <button
                      type="button"
                      class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-[#43302e] dark:bg-[#211817] dark:text-slate-200 dark:hover:bg-[#2e2120]"
                      aria-label="Show previous images"
                      @click="previousGallery"
                    >
                      <ChevronLeft class="h-4 w-4" aria-hidden="true" />
                    </button>
                    <button
                      type="button"
                      class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-[#43302e] dark:bg-[#211817] dark:text-slate-200 dark:hover:bg-[#2e2120]"
                      aria-label="Show next images"
                      @click="nextGallery"
                    >
                      <ChevronRight class="h-4 w-4" aria-hidden="true" />
                    </button>
                  </div>
                </div>

                <!-- 3 Images Slider Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                  <div
                    v-for="item in visibleGalleryVisuals"
                    :key="`${item.url}-${item.originalIndex}`"
                    class="group relative cursor-pointer overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-[#43302e] dark:bg-[#211817]"
                    @click="openModal(item)"
                  >
                    <ContentImage
                      :src="item.url"
                      :webp-src="item.webp_url || ''"
                      :alt="`${project.title} gallery visual ${item.originalIndex + 1}`"
                      :img-class="'h-44 sm:h-48 w-full object-cover rounded-xl transition-transform duration-300 group-hover:scale-105'"
                    />
                    <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                      <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 backdrop-blur-sm">
                        <Maximize2 class="h-3.5 w-3.5" aria-hidden="true" />
                        Expand
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Pagination dots if more than 3 -->
                <div v-if="galleryVisuals.length > 3" class="mt-4 flex items-center justify-center gap-1.5">
                  <button
                    v-for="(_, index) in galleryVisuals"
                    :key="index"
                    type="button"
                    class="h-2 rounded-full transition-all duration-300"
                    :class="index === galleryStartIndex ? 'w-5 bg-yellow-400' : 'w-2 bg-slate-300 dark:bg-slate-700'"
                    :aria-label="`Slide to image ${index + 1}`"
                    @click="galleryStartIndex = index"
                  />
                </div>
              </section>
            </section>

            <div class="rounded-[28px] border border-violet-200 bg-gradient-to-r from-violet-600 to-violet-700 p-8 text-center text-white shadow-[0_30px_70px_-35px_rgba(76,29,149,0.9)] sm:p-12">
              <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-100">Let’s talk</p>
              <h3 class="mt-4 text-3xl font-black tracking-[-0.05em] sm:text-5xl">Have a project in mind?</h3>
              <p class="mt-4 text-lg text-violet-50">Let’s build something great together.</p>
              <Link
                href="/contact"
                class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-violet-700 transition hover:bg-violet-50"
              >
                Start a project
                <ArrowRight class="h-4 w-4" aria-hidden="true" />
              </Link>
            </div>
          </div>
        </section>
      </div>
    </Container>
    <!-- Lightbox Modal -->
    <Teleport to="body">
      <div
        v-if="modalVisual"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <div class="relative max-h-[90vh] max-w-4xl overflow-hidden rounded-2xl bg-slate-950 p-2 shadow-2xl">
          <button
            type="button"
            class="absolute right-4 top-4 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black"
            aria-label="Close image preview"
            @click="closeModal"
          >
            <X class="h-5 w-5" aria-hidden="true" />
          </button>
          <ContentImage
            :src="modalVisual.url"
            :webp-src="modalVisual.webp_url || ''"
            :alt="`${project.title} enlarged visual`"
            :img-class="'max-h-[85vh] w-auto max-w-full rounded-xl object-contain mx-auto'"
          />
        </div>
      </div>
    </Teleport>
  </main>
</template>
