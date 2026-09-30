<script setup>
import { computed, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import {
  LayoutGrid,
  Sparkles,
  Package,
  Cpu,
  Bot,
  FolderKanban,
  Building2,
  MessageSquareQuote,
  Mail,
  ExternalLink,
  Eye,
} from 'lucide-vue-next'
import OverviewPanel from './panels/OverviewPanel.vue'
import HeroPanel from './panels/HeroPanel.vue'
import ServicesPanel from './panels/ServicesPanel.vue'
import TechPanel from './panels/TechPanel.vue'
import AiPanel from './panels/AiPanel.vue'
import CaseStudiesPanel from './panels/CaseStudiesPanel.vue'
import IndustriesPanel from './panels/IndustriesPanel.vue'
import TestimonialsPanel from './panels/TestimonialsPanel.vue'
import ConnectPanel from './panels/ConnectPanel.vue'

const props = defineProps({
  activeSection: { type: String, default: 'overview' },
  previewUrl: { type: String, default: '/' },
  settings: { type: Object, required: true },
  sections: { type: Array, required: true },
  seo: { type: Object, default: () => ({}) },
  hero: { type: Object, default: null },
  heroGradients: { type: Array, default: () => [] },
  deliveryCategories: { type: Array, default: () => [] },
  technologySection: { type: Object, required: true },
  aiServiceSection: { type: Object, required: true },
  industriesSection: { type: Object, required: true },
  caseStudiesSection: { type: Object, required: true },
  testimonialsSection: { type: Object, required: true },
  connectSection: { type: Object, required: true },
  projectPicker: { type: Array, default: () => [] },
  testimonialPicker: { type: Array, default: () => [] },
})

const page = usePage()
const flashSuccess = computed(() => page.props?.flash?.success)

const iconMap = {
  overview: LayoutGrid,
  hero: Sparkles,
  services: Package,
  technologies: Cpu,
  ai: Bot,
  case_studies: FolderKanban,
  industries: Building2,
  testimonials: MessageSquareQuote,
  connect: Mail,
}

const navItems = computed(() => [
  { key: 'overview', label: 'Overview & visibility', icon: LayoutGrid },
  ...props.sections.map((s) => ({
    key: s.key,
    label: s.label,
    icon: iconMap[s.key] || LayoutGrid,
    visible: s.visible,
  })),
])

const current = computed(() => props.activeSection || 'overview')

const panelTitle = computed(() => navItems.value.find((n) => n.key === current.value)?.label ?? 'Home page')

function selectSection(key) {
  router.get('/admin/home-page', { section: key }, { preserveState: true, preserveScroll: true, replace: true })
}

watch(
  () => props.activeSection,
  () => {
    if (typeof window !== 'undefined') {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  },
)
</script>

<template>
  <div class="-mx-4 -mt-2 sm:-mx-6 lg:-mx-8">
    <div class="sticky top-0 z-20 border-b border-slate-200/90 bg-white/90 px-4 py-4 backdrop-blur-md sm:px-6 lg:px-8">
      <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Home page editor</h1>
          <p class="mt-0.5 text-sm text-slate-600">Edit every section visitors see on your homepage.</p>
        </div>
        <div class="flex items-center gap-2">
          <a
            :href="previewUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50"
          >
            <Eye class="h-4 w-4" />
            Preview site
            <ExternalLink class="h-3.5 w-3.5 opacity-60" />
          </a>
        </div>
      </div>
      <p v-if="flashSuccess" class="mx-auto mt-3 max-w-7xl rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 ring-1 ring-emerald-200">
        {{ flashSuccess }}
      </p>
    </div>

    <div class="mx-auto grid max-w-7xl gap-0 lg:grid-cols-[15rem_minmax(0,1fr)] xl:grid-cols-[17rem_minmax(0,1fr)]">
      <aside class="border-b border-slate-200 bg-slate-50/80 px-3 py-4 lg:sticky lg:top-[5.5rem] lg:h-[calc(100vh-5.5rem)] lg:overflow-y-auto lg:border-b-0 lg:border-r">
        <p class="px-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">Sections</p>
        <nav class="mt-2 space-y-0.5" aria-label="Home page sections">
          <button
            type="button"
            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition"
            :class="current === 'overview' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-700 hover:bg-white hover:shadow-sm'"
            @click="selectSection('overview')"
          >
            <LayoutGrid class="h-4 w-4 shrink-0" />
            Overview
          </button>
          <button
            v-for="item in sections"
            :key="item.key"
            type="button"
            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition"
            :class="current === item.key ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-700 hover:bg-white hover:shadow-sm'"
            @click="selectSection(item.key)"
          >
            <component :is="iconMap[item.key] || LayoutGrid" class="h-4 w-4 shrink-0" />
            <span class="min-w-0 flex-1 truncate">{{ item.label }}</span>
            <span
              class="shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-bold"
              :class="current === item.key ? 'bg-white/20 text-white' : item.visible ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500'"
            >
              {{ item.visible ? 'On' : 'Off' }}
            </span>
          </button>
        </nav>
      </aside>

      <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <h2 class="text-lg font-semibold text-slate-900">{{ panelTitle }}</h2>
        <div class="mt-6">
          <OverviewPanel
            v-if="current === 'overview'"
            :settings="settings"
            :sections="sections"
            :project-picker="projectPicker"
            :testimonial-picker="testimonialPicker"
            :seo="seo"
            :active-section="activeSection"
          />
          <HeroPanel v-else-if="current === 'hero'" :hero="hero" :gradients="heroGradients" />
          <ServicesPanel v-else-if="current === 'services'" :delivery-categories="deliveryCategories" />
          <TechPanel v-else-if="current === 'technologies'" :section="technologySection" />
          <AiPanel v-else-if="current === 'ai'" :section="aiServiceSection" />
          <CaseStudiesPanel v-else-if="current === 'case_studies'" :section="caseStudiesSection" />
          <IndustriesPanel v-else-if="current === 'industries'" :section="industriesSection" />
          <TestimonialsPanel v-else-if="current === 'testimonials'" :section="testimonialsSection" />
          <ConnectPanel v-else-if="current === 'connect'" :section="connectSection" />
        </div>
      </main>
    </div>
  </div>
</template>
