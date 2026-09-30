<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import {
  AlertTriangle,
  Boxes,
  CheckCircle2,
  Cpu,
  Globe,
  Image as ImageIcon,
  Layers,
  Layout,
  ListChecks,
  Search,
  Sparkles,
  Tag,
} from 'lucide-vue-next'
import RepeaterField from './RepeaterField.vue'
import ImageUploadField from './ImageUploadField.vue'

const props = defineProps({
  form: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
  isEdit: { type: Boolean, default: false },
})

// -------- Shape guards --------
function ensureSnapshotShape() {
  if (!props.form.content.snapshot || typeof props.form.content.snapshot !== 'object') {
    props.form.content.snapshot = {
      eyebrow: '',
      heading_prefix: '',
      heading_highlight: '',
      heading_suffix: '',
      paragraphs: [],
      caption_title: '',
      caption_subtitle: '',
      image_alt: '',
    }
  }
  if (!Array.isArray(props.form.content.snapshot.paragraphs)) {
    props.form.content.snapshot.paragraphs = []
  }
}

ensureSnapshotShape()

// -------- Snapshot paragraph helpers --------
function addSnapshotParagraph() {
  if (props.form.content.snapshot.paragraphs.length >= 6) return
  props.form.content.snapshot.paragraphs = [...props.form.content.snapshot.paragraphs, '']
}
function removeSnapshotParagraph(index) {
  props.form.content.snapshot.paragraphs = props.form.content.snapshot.paragraphs.filter((_, i) => i !== index)
}
function moveSnapshotParagraph(index, delta) {
  const target = index + delta
  const list = props.form.content.snapshot.paragraphs
  if (target < 0 || target >= list.length) return
  const next = list.slice()
  const [moved] = next.splice(index, 1)
  next.splice(target, 0, moved)
  props.form.content.snapshot.paragraphs = next
}
function setSnapshotParagraph(index, value) {
  props.form.content.snapshot.paragraphs = props.form.content.snapshot.paragraphs.map((p, i) => (i === index ? value : p))
}

// -------- Repeater field configs --------
const chipFields = [
  { key: 'icon', label: 'Lucide icon', placeholder: 'Sparkles' },
  { key: 'label', label: 'Label', placeholder: 'Systems & tokens' },
]
const techFields = [
  { key: 'name', label: 'Display name', placeholder: 'React' },
  { key: 'slug', label: 'Simple-icons slug', placeholder: 'react' },
]
const outcomeFields = [
  { key: 'icon', label: 'Lucide icon', placeholder: 'LayoutTemplate', span: 'half' },
  { key: 'title', label: 'Title', placeholder: 'Product & marketing sites', span: 'half' },
  { key: 'description', label: 'Description', placeholder: 'IA, key screens, and responsive layouts...', type: 'textarea', span: 'full' },
]
const newChip = () => ({ icon: 'Sparkles', label: '' })
const newTech = () => ({ name: '', slug: '' })
const newOutcomeCard = () => ({ icon: 'LayoutTemplate', title: '', description: '' })

// -------- Section nav --------
const sections = [
  { id: 'identity', label: 'Identity', icon: Tag, helper: 'Title, slug, category, status' },
  { id: 'hero', label: 'Hero', icon: Sparkles, helper: 'Top-of-page banner' },
  { id: 'tech', label: 'Tech stack', icon: Cpu, helper: 'Technologies & icons' },
  { id: 'outcomes', label: 'Outcomes', icon: ListChecks, helper: 'What we deliver cards' },
  { id: 'snapshot', label: 'Snapshot', icon: Layout, helper: 'Captioned showcase block' },
  { id: 'seo', label: 'SEO & Social', icon: Search, helper: 'Search & sharing previews' },
]

const activeSection = ref('identity')
const sectionRefs = ref({})

function setSectionRef(id, el) {
  if (el) sectionRefs.value[id] = el
}

function jumpTo(id) {
  const el = sectionRefs.value[id]
  if (el && typeof el.scrollIntoView === 'function') {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
  activeSection.value = id
}

// IntersectionObserver for active section
let observer = null
onMounted(() => {
  if (typeof IntersectionObserver === 'undefined') return
  observer = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((e) => e.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)
      if (visible[0]?.target?.dataset?.section) {
        activeSection.value = visible[0].target.dataset.section
      }
    },
    { rootMargin: '-30% 0px -55% 0px', threshold: [0.1, 0.5, 1] },
  )
  Object.values(sectionRefs.value).forEach((el) => el && observer.observe(el))
})

// -------- Slug auto-generation --------
const userTouchedSlug = ref(props.isEdit && !!props.form.slug)

function slugify(value) {
  return String(value || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 80)
}

watch(
  () => props.form.title,
  (next) => {
    if (!userTouchedSlug.value && !props.isEdit) {
      props.form.slug = slugify(next)
    }
  },
)

function onSlugInput(event) {
  userTouchedSlug.value = true
  props.form.slug = event.target.value
}

// -------- Section completeness counts (errors) --------
const errorMap = computed(() => props.form.errors || {})

function sectionErrorCount(prefix) {
  return Object.keys(errorMap.value).filter((k) => k === prefix || k.startsWith(prefix + '.') || k.startsWith(prefix + '_')).length
}

const sectionStatus = computed(() => ({
  identity:
    Object.keys(errorMap.value).filter((k) =>
      ['title', 'slug', 'icon', 'meta_description', 'sort_order', 'delivery_category_id'].includes(k),
    ).length,
  hero: Object.keys(errorMap.value).filter((k) => k.startsWith('content.hero') || k === 'hero_image').length,
  tech: Object.keys(errorMap.value).filter((k) => k.startsWith('content.tech') || k === 'tech_image').length,
  outcomes: Object.keys(errorMap.value).filter((k) => k.startsWith('content.outcomes') || k === 'outcomes_image').length,
  snapshot: Object.keys(errorMap.value).filter((k) => k.startsWith('content.snapshot') || k === 'snapshot_image').length,
  seo: Object.keys(errorMap.value).filter((k) =>
    [
      'meta_title',
      'meta_keywords',
      'og_title',
      'og_description',
      'og_image',
      'noindex',
      'canonical_url',
      'schema_type',
    ].includes(k),
  ).length,
}))

// -------- SEO live previews --------
const previewTitle = computed(() => props.form.meta_title || props.form.title || 'Service title')
const previewDesc = computed(() =>
  props.form.meta_description ||
  props.form.content?.hero?.description ||
  'Add an SEO description to control how this page appears in search results.',
)
const previewSlug = computed(() => props.form.slug || 'service-slug')

const titleLen = computed(() => (props.form.meta_title || '').length)
const descLen = computed(() => (props.form.meta_description || '').length)

function lengthState(value, ideal, max) {
  if (!value) return 'empty'
  if (value < ideal[0]) return 'short'
  if (value > max) return 'over'
  if (value > ideal[1]) return 'long'
  return 'good'
}

const titleState = computed(() => lengthState(titleLen.value, [40, 60], 70))
const descState = computed(() => lengthState(descLen.value, [120, 160], 180))

const stateColor = {
  empty: 'text-slate-400',
  short: 'text-amber-600',
  long: 'text-amber-600',
  over: 'text-rose-600',
  good: 'text-emerald-600',
}
const stateLabel = {
  empty: 'Empty',
  short: 'Too short',
  long: 'A bit long',
  over: 'Over limit',
  good: 'Optimal',
}

// Social preview uses og_* with fallback to meta_*
const socialTitle = computed(() => props.form.og_title || previewTitle.value)
const socialDesc = computed(() => props.form.og_description || previewDesc.value)
const socialImage = computed(
  () => props.form.og_image_current_url || props.form.hero_image_current_url || null,
)

const schemaTypes = [
  { value: '', label: 'Service (default)' },
  { value: 'Service', label: 'Service' },
  { value: 'ProfessionalService', label: 'Professional service' },
  { value: 'WebSite', label: 'Web site' },
  { value: 'CreativeWork', label: 'Creative work' },
  { value: 'Product', label: 'Product' },
]
</script>

<template>
  <div class="grid grid-cols-1 gap-8 lg:grid-cols-[14rem_minmax(0,1fr)] xl:grid-cols-[16rem_minmax(0,1fr)]">
    <!-- STICKY SECTION NAV -->
    <aside class="lg:sticky lg:top-6 lg:h-fit">
      <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
        <p class="px-3 pb-1 pt-2 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
          Sections
        </p>
        <ul class="space-y-1">
          <li v-for="s in sections" :key="s.id">
            <button
              type="button"
              :class="[
                'group flex w-full items-center gap-3 rounded-xl px-3 py-2 text-left text-sm transition',
                activeSection === s.id
                  ? 'bg-violet-50 text-violet-900 ring-1 ring-violet-200'
                  : 'text-slate-700 hover:bg-slate-50',
              ]"
              @click="jumpTo(s.id)"
            >
              <span
                :class="[
                  'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition',
                  activeSection === s.id
                    ? 'bg-violet-600 text-white shadow-sm'
                    : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200',
                ]"
              >
                <component :is="s.icon" class="h-4 w-4" stroke-width="2" />
              </span>
              <span class="min-w-0 flex-1">
                <span class="block font-semibold">{{ s.label }}</span>
                <span class="block truncate text-[11px] text-slate-500">{{ s.helper }}</span>
              </span>
              <span
                v-if="sectionStatus[s.id]"
                class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-100 px-1.5 text-[10px] font-bold text-rose-700"
                :title="`${sectionStatus[s.id]} validation issue${sectionStatus[s.id] === 1 ? '' : 's'}`"
              >
                {{ sectionStatus[s.id] }}
              </span>
            </button>
          </li>
        </ul>
      </div>

      <div class="mt-4 hidden rounded-2xl border border-violet-200/70 bg-gradient-to-br from-violet-50 via-white to-indigo-50 p-4 text-xs text-slate-600 lg:block">
        <p class="flex items-center gap-2 font-semibold text-violet-700">
          <Sparkles class="h-3.5 w-3.5" /> Pro tips
        </p>
        <ul class="mt-2 space-y-1.5 text-[11px] leading-relaxed">
          <li>Keep SEO titles 40–60 chars and descriptions 120–160 chars.</li>
          <li>Upload a 1200 × 630 OG image for sharp social cards.</li>
          <li>Use the Lucide icon name in PascalCase (e.g. <code class="rounded bg-white px-1">Sparkles</code>).</li>
        </ul>
      </div>
    </aside>

    <!-- MAIN FORM -->
    <div class="min-w-0 space-y-10">
      <!-- IDENTITY -->
      <section
        :ref="(el) => setSectionRef('identity', el)"
        data-section="identity"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <Tag class="h-4 w-4 text-violet-600" /> Identity & settings
          </h2>
          <p class="mt-1 text-xs text-slate-500">Title, public URL, category, listing icon, and status.</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="svc-title" class="form-label">Service title <span class="text-rose-500">*</span></label>
            <input
              id="svc-title"
              v-model="form.title"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.title }"
              placeholder="Website UI/UX"
            >
            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.title }}</p>
          </div>
          <div>
            <label for="svc-slug" class="form-label">
              Slug
              <span class="font-normal text-slate-400">(auto from title)</span>
            </label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400">/services/</span>
              <input
                id="svc-slug"
                :value="form.slug"
                type="text"
                class="form-input pl-[5.25rem]"
                :class="{ 'form-input-invalid': !!form.errors.slug }"
                placeholder="website-ui-ux"
                @input="onSlugInput"
              >
            </div>
            <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label for="svc-category" class="form-label">Category</label>
            <select
              id="svc-category"
              v-model="form.delivery_category_id"
              class="form-select"
              :class="{ 'form-input-invalid': !!form.errors.delivery_category_id }"
            >
              <option value="">Unassigned</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
            <p v-if="form.errors.delivery_category_id" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.delivery_category_id }}</p>
          </div>
          <div>
            <label for="svc-icon" class="form-label">Listing icon</label>
            <input
              id="svc-icon"
              v-model="form.icon"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.icon }"
              placeholder="Sparkles"
              autocomplete="off"
            >
            <p class="mt-1 text-[11px] text-slate-500">Lucide name (PascalCase).</p>
            <p v-if="form.errors.icon" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.icon }}</p>
          </div>
          <div>
            <label for="svc-sort" class="form-label">Sort order</label>
            <input
              id="svc-sort"
              v-model.number="form.sort_order"
              type="number"
              min="0"
              max="1000"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.sort_order }"
            >
            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.sort_order }}</p>
          </div>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
          <input v-model="form.is_active" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400">
          <span class="text-sm text-slate-700">
            <span class="block font-semibold text-slate-900">Live on site</span>
            <span class="block text-xs text-slate-500">Make this service page reachable at <code class="rounded bg-white px-1">/services/{{ previewSlug }}</code>.</span>
          </span>
        </label>

        <div>
          <label for="svc-summary" class="form-label">Short summary</label>
          <textarea
            id="svc-summary"
            v-model="form.meta_description"
            rows="2"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!form.errors.meta_description }"
            placeholder="One-sentence pitch shown in cards and search results when no SEO description is set."
          />
          <p class="mt-1 text-[11px] text-slate-500">
            Doubles as the default SEO description.
            <span :class="stateColor[descState]">
              {{ descLen }} / 160 — {{ stateLabel[descState] }}
            </span>
          </p>
        </div>
      </section>

      <!-- HERO -->
      <section
        :ref="(el) => setSectionRef('hero', el)"
        data-section="hero"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <Sparkles class="h-4 w-4 text-violet-600" /> Hero section
          </h2>
          <p class="mt-1 text-xs text-slate-500">Top-of-page banner with eyebrow, gradient heading, description and CTAs.</p>
        </header>

        <ImageUploadField
          label="Hero image"
          aspect="4 / 3"
          recommendation="Best at 1200 × 900 (4:3) PNG / WebP / SVG"
          hint="Used as the right-hand visual at the top of the service page."
          :file="form.hero_image"
          :current-url="form.hero_image_current_url"
          :remove-flag="form.hero_image_remove"
          :error="form.errors.hero_image"
          @update:file="(v) => (form.hero_image = v)"
          @update:remove-flag="(v) => (form.hero_image_remove = v)"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-eyebrow" class="form-label">Eyebrow badge</label>
            <input id="hero-eyebrow" v-model="form.content.hero.eyebrow" type="text" class="form-input" placeholder="Website UI / UX">
          </div>
          <div>
            <label for="hero-image-alt" class="form-label">Hero image alt text</label>
            <input id="hero-image-alt" v-model="form.content.hero.image_alt" type="text" class="form-input">
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-prefix" class="form-label">Heading prefix <span class="text-rose-500">*</span></label>
            <input id="hero-prefix" v-model="form.content.hero.heading_prefix" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.hero.heading_prefix'] }" placeholder="We structure">
            <p v-if="form.errors['content.hero.heading_prefix']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.hero.heading_prefix'] }}</p>
          </div>
          <div>
            <label for="hero-highlight" class="form-label">Heading highlight (gradient) <span class="text-rose-500">*</span></label>
            <input id="hero-highlight" v-model="form.content.hero.heading_highlight" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.hero.heading_highlight'] }" placeholder="ideas">
            <p v-if="form.errors['content.hero.heading_highlight']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.hero.heading_highlight'] }}</p>
          </div>
        </div>

        <div>
          <label for="hero-subhead" class="form-label">Sub-heading</label>
          <input id="hero-subhead" v-model="form.content.hero.subhead" type="text" class="form-input" placeholder="into clear, beautiful products">
        </div>

        <div>
          <label for="hero-desc" class="form-label">Description <span class="text-rose-500">*</span></label>
          <textarea id="hero-desc" v-model="form.content.hero.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors['content.hero.description'] }" />
          <p v-if="form.errors['content.hero.description']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.hero.description'] }}</p>
        </div>

        <div>
          <p class="form-label">Chips</p>
          <p class="mt-0.5 text-[11px] text-slate-500">Small pills shown under the hero description.</p>
          <RepeaterField
            v-model="form.content.hero.chips"
            label="Chip"
            :fields="chipFields"
            :new-row="newChip"
            :errors="form.errors"
            error-prefix="content.hero.chips"
            :max="6"
            empty-text="No chips yet."
            class="mt-2"
          />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-pcta-label" class="form-label">Primary CTA label <span class="text-rose-500">*</span></label>
            <input id="hero-pcta-label" v-model="form.content.hero.primary_cta_label" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.hero.primary_cta_label'] }">
            <p v-if="form.errors['content.hero.primary_cta_label']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.hero.primary_cta_label'] }}</p>
          </div>
          <div>
            <label for="hero-pcta-href" class="form-label">Primary CTA link <span class="text-rose-500">*</span></label>
            <input id="hero-pcta-href" v-model="form.content.hero.primary_cta_href" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.hero.primary_cta_href'] }">
            <p v-if="form.errors['content.hero.primary_cta_href']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.hero.primary_cta_href'] }}</p>
          </div>
          <div>
            <label for="hero-scta-label" class="form-label">Secondary CTA label <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="hero-scta-label" v-model="form.content.hero.secondary_cta_label" type="text" class="form-input">
          </div>
          <div>
            <label for="hero-scta-href" class="form-label">Secondary CTA link</label>
            <input id="hero-scta-href" v-model="form.content.hero.secondary_cta_href" type="text" class="form-input">
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="hero-cap-eyebrow" class="form-label">Floating caption eyebrow</label>
            <input id="hero-cap-eyebrow" v-model="form.content.hero.caption_eyebrow" type="text" class="form-input" placeholder="Interface layer">
          </div>
          <div>
            <label for="hero-cap-text" class="form-label">Floating caption text</label>
            <input id="hero-cap-text" v-model="form.content.hero.caption_text" type="text" class="form-input">
          </div>
        </div>
      </section>

      <!-- TECH STACK -->
      <section
        :ref="(el) => setSectionRef('tech', el)"
        data-section="tech"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <Cpu class="h-4 w-4 text-violet-600" /> Tech stack section
          </h2>
          <p class="mt-1 text-xs text-slate-500">Technologies used, with simple-icons logos.</p>
        </header>

        <ImageUploadField
          label="Tech stack image"
          aspect="16 / 9"
          recommendation="Optional — used as a banner above the tech icon grid"
          :file="form.tech_image"
          :current-url="form.tech_image_current_url"
          :remove-flag="form.tech_image_remove"
          :error="form.errors.tech_image"
          @update:file="(v) => (form.tech_image = v)"
          @update:remove-flag="(v) => (form.tech_image_remove = v)"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label for="tech-eyebrow" class="form-label">Eyebrow</label>
            <input id="tech-eyebrow" v-model="form.content.tech.eyebrow" type="text" class="form-input" placeholder="Web stack">
          </div>
          <div>
            <label for="tech-prefix" class="form-label">Heading prefix <span class="text-rose-500">*</span></label>
            <input id="tech-prefix" v-model="form.content.tech.heading_prefix" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.tech.heading_prefix'] }" placeholder="Web design">
            <p v-if="form.errors['content.tech.heading_prefix']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.tech.heading_prefix'] }}</p>
          </div>
          <div>
            <label for="tech-highlight" class="form-label">Heading highlight</label>
            <input id="tech-highlight" v-model="form.content.tech.heading_highlight" type="text" class="form-input" placeholder="& development">
          </div>
        </div>

        <div>
          <label for="tech-desc" class="form-label">Description <span class="text-rose-500">*</span></label>
          <textarea id="tech-desc" v-model="form.content.tech.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors['content.tech.description'] }" />
          <p v-if="form.errors['content.tech.description']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.tech.description'] }}</p>
        </div>

        <div>
          <p class="form-label">Technologies</p>
          <p class="mt-0.5 text-[11px] text-slate-500">
            Use slugs from
            <a href="https://simpleicons.org/" target="_blank" rel="noopener" class="text-violet-700 underline">simpleicons.org</a>
            (e.g. <code class="rounded bg-slate-100 px-1">react</code>, <code class="rounded bg-slate-100 px-1">vuedotjs</code>).
          </p>
          <RepeaterField
            v-model="form.content.tech.items"
            label="Tech"
            :fields="techFields"
            :new-row="newTech"
            :errors="form.errors"
            error-prefix="content.tech.items"
            :max="18"
            empty-text="No technologies added."
            class="mt-2"
          />
        </div>

        <div>
          <label for="tech-image-alt" class="form-label">Tech image alt text</label>
          <input id="tech-image-alt" v-model="form.content.tech.image_alt" type="text" class="form-input">
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="tech-cta-label" class="form-label">CTA label <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="tech-cta-label" v-model="form.content.tech.cta_label" type="text" class="form-input">
          </div>
          <div>
            <label for="tech-cta-href" class="form-label">CTA link</label>
            <input id="tech-cta-href" v-model="form.content.tech.cta_href" type="text" class="form-input">
          </div>
        </div>
      </section>

      <!-- OUTCOMES -->
      <section
        :ref="(el) => setSectionRef('outcomes', el)"
        data-section="outcomes"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <ListChecks class="h-4 w-4 text-violet-600" /> Outcomes section
          </h2>
          <p class="mt-1 text-xs text-slate-500">Three-column "what we deliver" grid with icon cards.</p>
        </header>

        <ImageUploadField
          label="Outcomes image"
          aspect="16 / 9"
          recommendation="Optional — banner above the outcome cards"
          :file="form.outcomes_image"
          :current-url="form.outcomes_image_current_url"
          :remove-flag="form.outcomes_image_remove"
          :error="form.errors.outcomes_image"
          @update:file="(v) => (form.outcomes_image = v)"
          @update:remove-flag="(v) => (form.outcomes_image_remove = v)"
        />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="out-eyebrow" class="form-label">Eyebrow</label>
            <input id="out-eyebrow" v-model="form.content.outcomes.eyebrow" type="text" class="form-input" placeholder="Outcomes">
          </div>
          <div>
            <label for="out-heading" class="form-label">Heading <span class="text-rose-500">*</span></label>
            <input id="out-heading" v-model="form.content.outcomes.heading" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors['content.outcomes.heading'] }" placeholder="What we deliver">
            <p v-if="form.errors['content.outcomes.heading']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.outcomes.heading'] }}</p>
          </div>
        </div>

        <div>
          <label for="out-desc" class="form-label">Description <span class="text-rose-500">*</span></label>
          <textarea id="out-desc" v-model="form.content.outcomes.description" rows="3" class="form-textarea" :class="{ 'form-input-invalid': !!form.errors['content.outcomes.description'] }" />
          <p v-if="form.errors['content.outcomes.description']" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors['content.outcomes.description'] }}</p>
        </div>

        <div>
          <p class="form-label">Outcome cards</p>
          <p class="mt-0.5 text-[11px] text-slate-500">Each card has a Lucide icon, title, and short description.</p>
          <RepeaterField
            v-model="form.content.outcomes.cards"
            label="Card"
            :fields="outcomeFields"
            :new-row="newOutcomeCard"
            :errors="form.errors"
            error-prefix="content.outcomes.cards"
            :max="9"
            empty-text="No outcome cards yet."
            class="mt-2"
          />
        </div>

        <div>
          <label for="out-image-alt" class="form-label">Outcomes image alt text</label>
          <input id="out-image-alt" v-model="form.content.outcomes.image_alt" type="text" class="form-input">
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="out-cta-label" class="form-label">CTA label <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="out-cta-label" v-model="form.content.outcomes.cta_label" type="text" class="form-input">
          </div>
          <div>
            <label for="out-cta-href" class="form-label">CTA link</label>
            <input id="out-cta-href" v-model="form.content.outcomes.cta_href" type="text" class="form-input">
          </div>
        </div>
      </section>

      <!-- SNAPSHOT -->
      <section
        :ref="(el) => setSectionRef('snapshot', el)"
        data-section="snapshot"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <Layout class="h-4 w-4 text-violet-600" /> Snapshot section
            <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Optional</span>
          </h2>
          <p class="mt-1 text-xs text-slate-500">
            Two-column band: split heading (one accent word in italic violet) + paragraphs on the left,
            captioned image on the right.
          </p>
        </header>

        <ImageUploadField
          label="Snapshot image"
          aspect="16 / 10"
          recommendation="Best at 1600 × 1000 — JPG / PNG / WebP"
          :file="form.snapshot_image"
          :current-url="form.snapshot_image_current_url"
          :remove-flag="form.snapshot_image_remove"
          :error="form.errors.snapshot_image"
          @update:file="(v) => (form.snapshot_image = v)"
          @update:remove-flag="(v) => (form.snapshot_image_remove = v)"
        />

        <div>
          <label for="snap-eyebrow" class="form-label">Eyebrow</label>
          <input id="snap-eyebrow" v-model="form.content.snapshot.eyebrow" type="text" class="form-input max-w-md" placeholder="Snapshot">
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label for="snap-prefix" class="form-label">Heading prefix</label>
            <input id="snap-prefix" v-model="form.content.snapshot.heading_prefix" type="text" class="form-input" placeholder="Built for">
          </div>
          <div>
            <label for="snap-highlight" class="form-label">
              Highlight word <span class="block font-normal text-[11px] text-slate-500">italic + violet</span>
            </label>
            <input id="snap-highlight" v-model="form.content.snapshot.heading_highlight" type="text" class="form-input" placeholder="load">
          </div>
          <div>
            <label for="snap-suffix" class="form-label">Heading suffix</label>
            <input id="snap-suffix" v-model="form.content.snapshot.heading_suffix" type="text" class="form-input" placeholder="& longevity">
          </div>
        </div>

        <div>
          <p class="form-label">Body paragraphs</p>
          <p class="mt-0.5 text-[11px] text-slate-500">Up to 6.</p>

          <div class="mt-2 space-y-3">
            <div
              v-if="!form.content.snapshot.paragraphs.length"
              class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500"
            >
              No paragraphs yet. Click "Add paragraph" to write the first one.
            </div>

            <div
              v-for="(p, idx) in form.content.snapshot.paragraphs"
              :key="idx"
              class="rounded-xl border border-slate-200 bg-white p-3"
            >
              <div class="flex items-center justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">
                  Paragraph {{ idx + 1 }}
                </p>
                <div class="flex items-center gap-1">
                  <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="idx === 0"
                    aria-label="Move up"
                    @click="moveSnapshotParagraph(idx, -1)"
                  >
                    ↑
                  </button>
                  <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="idx === form.content.snapshot.paragraphs.length - 1"
                    aria-label="Move down"
                    @click="moveSnapshotParagraph(idx, 1)"
                  >
                    ↓
                  </button>
                  <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50"
                    aria-label="Remove paragraph"
                    @click="removeSnapshotParagraph(idx)"
                  >
                    ✕
                  </button>
                </div>
              </div>
              <textarea
                rows="3"
                class="form-textarea mt-2"
                :class="{ 'form-input-invalid': !!form.errors[`content.snapshot.paragraphs.${idx}`] }"
                :value="p"
                placeholder="A short paragraph."
                @input="setSnapshotParagraph(idx, $event.target.value)"
              />
              <p v-if="form.errors[`content.snapshot.paragraphs.${idx}`]" class="mt-1 text-xs text-rose-600" role="alert">
                {{ form.errors[`content.snapshot.paragraphs.${idx}`] }}
              </p>
            </div>

            <button
              v-if="form.content.snapshot.paragraphs.length < 6"
              type="button"
              class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
              @click="addSnapshotParagraph"
            >
              + Add paragraph
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="snap-cap-title" class="form-label">Image caption title</label>
            <input id="snap-cap-title" v-model="form.content.snapshot.caption_title" type="text" class="form-input" placeholder="Production-minded structure">
          </div>
          <div>
            <label for="snap-cap-sub" class="form-label">Image caption subtitle</label>
            <input id="snap-cap-sub" v-model="form.content.snapshot.caption_subtitle" type="text" class="form-input" placeholder="Observable, secure, ready to evolve">
          </div>
        </div>

        <div>
          <label for="snap-image-alt" class="form-label">Image alt text</label>
          <input id="snap-image-alt" v-model="form.content.snapshot.image_alt" type="text" class="form-input">
        </div>
      </section>

      <!-- SEO & SOCIAL -->
      <section
        :ref="(el) => setSectionRef('seo', el)"
        data-section="seo"
        class="space-y-5 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <Search class="h-4 w-4 text-violet-600" /> SEO & social
          </h2>
          <p class="mt-1 text-xs text-slate-500">Search engine snippet, Open Graph / Twitter card, robots and canonical URL.</p>
        </header>

        <!-- Live Google snippet -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">
            <Globe class="h-3.5 w-3.5" /> Google search preview
          </p>
          <div class="mt-3 max-w-xl">
            <p class="truncate text-xs text-slate-500">www.example.com › services › <span class="text-slate-700">{{ previewSlug }}</span></p>
            <p class="mt-0.5 truncate text-base font-medium text-blue-700">
              {{ previewTitle }}
            </p>
            <p class="mt-1 line-clamp-2 text-sm leading-snug text-slate-700">
              {{ previewDesc }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="seo-title" class="form-label">SEO title</label>
            <input
              id="seo-title"
              v-model="form.meta_title"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.meta_title }"
              placeholder="Defaults to service title"
              maxlength="180"
            >
            <p class="mt-1 text-[11px]" :class="stateColor[titleState]">
              {{ titleLen }} / 60 — {{ stateLabel[titleState] }}
            </p>
            <p v-if="form.errors.meta_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.meta_title }}</p>
          </div>
          <div>
            <label for="seo-keywords" class="form-label">Meta keywords</label>
            <input id="seo-keywords" v-model="form.meta_keywords" type="text" class="form-input" placeholder="ui ux, web design, ...">
            <p class="mt-1 text-[11px] text-slate-500">Comma-separated. Used by some legacy crawlers.</p>
            <p v-if="form.errors.meta_keywords" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.meta_keywords }}</p>
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
          <div class="flex items-center justify-between gap-3">
            <p class="flex items-center gap-2 text-sm font-bold text-slate-800">
              <ImageIcon class="h-4 w-4 text-violet-600" /> Social card
            </p>
            <p class="text-[11px] text-slate-500">
              Open Graph (Facebook, LinkedIn) and Twitter
            </p>
          </div>

          <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,17rem)]">
            <div class="space-y-3">
              <div>
                <label for="og-title" class="form-label">OG title</label>
                <input id="og-title" v-model="form.og_title" type="text" class="form-input" :placeholder="previewTitle">
                <p v-if="form.errors.og_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.og_title }}</p>
              </div>
              <div>
                <label for="og-desc" class="form-label">OG description</label>
                <textarea id="og-desc" v-model="form.og_description" rows="2" class="form-textarea" :placeholder="previewDesc" />
                <p v-if="form.errors.og_description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.og_description }}</p>
              </div>
              <ImageUploadField
                label="OG image"
                aspect="1.91 / 1"
                recommendation="Recommended 1200 × 630 (1.91:1)"
                hint="Falls back to the hero image."
                :file="form.og_image"
                :current-url="form.og_image_current_url"
                :remove-flag="form.og_image_remove"
                :error="form.errors.og_image"
                @update:file="(v) => (form.og_image = v)"
                @update:remove-flag="(v) => (form.og_image_remove = v)"
              />
            </div>

            <!-- Live social card -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <div class="aspect-[1.91/1] w-full overflow-hidden bg-gradient-to-br from-violet-200 via-slate-100 to-indigo-200">
                <img
                  v-if="socialImage"
                  :src="socialImage"
                  :alt="socialTitle"
                  class="h-full w-full object-cover"
                >
                <div v-else class="flex h-full items-center justify-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                  No social image
                </div>
              </div>
              <div class="p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-400">www.example.com</p>
                <p class="mt-0.5 line-clamp-2 text-sm font-semibold text-slate-900">{{ socialTitle }}</p>
                <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ socialDesc }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="seo-canonical" class="form-label">Canonical URL override</label>
            <input id="seo-canonical" v-model="form.canonical_url" type="text" class="form-input" placeholder="Leave blank to auto-generate">
            <p class="mt-1 text-[11px] text-slate-500">Use only when this content lives at a different primary URL.</p>
            <p v-if="form.errors.canonical_url" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.canonical_url }}</p>
          </div>
          <div>
            <label for="seo-schema" class="form-label">Schema.org type</label>
            <select id="seo-schema" v-model="form.schema_type" class="form-select">
              <option v-for="t in schemaTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
            </select>
            <p v-if="form.errors.schema_type" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.schema_type }}</p>
          </div>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
          <input v-model="form.noindex" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-400">
          <span class="text-sm text-amber-900">
            <span class="flex items-center gap-1.5 font-semibold">
              <AlertTriangle class="h-4 w-4" /> Tell search engines NOT to index this page
            </span>
            <span class="mt-0.5 block text-xs text-amber-800">
              Adds <code class="rounded bg-amber-100 px-1">noindex, nofollow</code> meta robots.
              Use for drafts, internal pages, or duplicates.
            </span>
          </span>
        </label>
      </section>
    </div>
  </div>
</template>
