<script setup>
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import {
  Image,
  Phone,
  MapPinned,
  BarChart3,
  FileText,
  LineChart,
  Mail,
  Share2,
  Settings2,
  Save,
} from 'lucide-vue-next'
import BrandingPanel from './panels/BrandingPanel.vue'
import ContactPanel from './panels/ContactPanel.vue'
import ContactSeoPanel from './panels/ContactSeoPanel.vue'
import SeoPanel from './panels/SeoPanel.vue'
import SitemapPanel from './panels/SitemapPanel.vue'
import TrackingPanel from './panels/TrackingPanel.vue'
import SmtpPanel from './panels/SmtpPanel.vue'
import SocialPanel from './panels/SocialPanel.vue'
import OtherPanel from './panels/OtherPanel.vue'

const props = defineProps({
  activeTab: { type: String, default: 'branding' },
  tabs: { type: Array, default: () => [] },
  branding: { type: Object, required: true },
  contact: { type: Object, required: true },
  contactSeo: { type: Object, required: true },
  seo: { type: Object, required: true },
  sitemap: { type: Object, required: true },
  tracking: { type: Object, required: true },
  smtp: { type: Object, required: true },
  social: { type: Object, required: true },
  other: { type: Object, required: true },
})

const page = usePage()
const flashSuccess = computed(() => page.props?.flash?.success)
const panelRef = ref(null)

const iconMap = {
  branding: Image,
  contact: Phone,
  'contact-seo': MapPinned,
  seo: BarChart3,
  sitemap: FileText,
  tracking: LineChart,
  smtp: Mail,
  social: Share2,
  other: Settings2,
}

const tabItems = computed(() =>
  (props.tabs.length ? props.tabs : []).map((t) => ({
    ...t,
    icon: iconMap[t.key] || Settings2,
  })),
)

const current = computed(() => props.activeTab || 'branding')

function selectTab(key) {
  router.get('/admin/web-settings', { tab: key }, { preserveState: true, preserveScroll: true, replace: true })
}

function saveAll() {
  panelRef.value?.save?.()
}
</script>

<template>
  <div class="-mx-4 -mt-2 sm:-mx-6">
    <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-6">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Web settings</h1>
          <p class="mt-1 text-sm text-slate-600">Global site configuration, branding, tracking, and more.</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
          @click="saveAll"
        >
          <Save class="h-4 w-4" />
          Save all
        </button>
      </div>

      <p v-if="flashSuccess" class="mt-3 rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 ring-1 ring-emerald-200">
        {{ flashSuccess }}
      </p>

      <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-9">
        <button
          v-for="tab in tabItems"
          :key="tab.key"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-semibold transition"
          :class="
            current === tab.key
              ? 'border-violet-600 bg-violet-600 text-white shadow-sm'
              : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'
          "
          @click="selectTab(tab.key)"
        >
          <component :is="tab.icon" class="h-4 w-4 shrink-0" />
          <span class="truncate">{{ tab.label }}</span>
        </button>
      </div>
    </div>

    <div class="px-4 py-6 sm:px-6">
      <BrandingPanel v-if="current === 'branding'" ref="panelRef" :branding="branding" />
      <ContactPanel v-else-if="current === 'contact'" ref="panelRef" :contact="contact" />
      <ContactSeoPanel v-else-if="current === 'contact-seo'" ref="panelRef" :contact-seo="contactSeo" />
      <SeoPanel v-else-if="current === 'seo'" ref="panelRef" :seo="seo" />
      <SitemapPanel v-else-if="current === 'sitemap'" ref="panelRef" :sitemap="sitemap" />
      <TrackingPanel v-else-if="current === 'tracking'" ref="panelRef" :tracking="tracking" />
      <SmtpPanel v-else-if="current === 'smtp'" ref="panelRef" :smtp="smtp" />
      <SocialPanel v-else-if="current === 'social'" ref="panelRef" :social="social" />
      <OtherPanel v-else-if="current === 'other'" ref="panelRef" :other="other" />
    </div>
  </div>
</template>
