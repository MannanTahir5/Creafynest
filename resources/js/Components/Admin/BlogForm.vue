<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import {
  AlertTriangle,
  FileText,
  Globe,
  Image as ImageIcon,
  Search,
  Sparkles,
  Tag,
} from 'lucide-vue-next'
import BlogContentEditor from '../BlogContentEditor.vue'
import ImageUploadField from './ImageUploadField.vue'

const props = defineProps({
  form: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
  isEdit: { type: Boolean, default: false },
})

// -------- Section nav --------
const sections = [
  { id: 'identity', label: 'Identity', icon: Tag, helper: 'Title, slug, category' },
  { id: 'content', label: 'Content', icon: FileText, helper: 'Body — markdown / HTML' },
  { id: 'cover', label: 'Cover image', icon: ImageIcon, helper: 'Hero image for the post' },
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

// -------- Section error counts --------
const errorMap = computed(() => props.form.errors || {})

const sectionStatus = computed(() => ({
  identity: Object.keys(errorMap.value).filter((k) =>
    ['title', 'slug', 'category_id'].includes(k),
  ).length,
  content: Object.keys(errorMap.value).filter((k) =>
    ['content', 'content_format'].includes(k),
  ).length,
  cover: Object.keys(errorMap.value).filter((k) => k === 'image').length,
  seo: Object.keys(errorMap.value).filter((k) =>
    [
      'meta_title',
      'meta_description',
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
const previewTitle = computed(() => props.form.meta_title || props.form.title || 'Blog post title')
const previewSlug = computed(() => props.form.slug || 'blog-slug')
const previewDesc = computed(
  () =>
    props.form.meta_description ||
    'Add an SEO description to control how this post appears in search results.',
)

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

const socialTitle = computed(() => props.form.og_title || previewTitle.value)
const socialDesc = computed(() => props.form.og_description || previewDesc.value)
const socialImage = computed(
  () => props.form.og_image_current_url || props.form.image_current_url || null,
)

const schemaTypes = [
  { value: '', label: 'Blog posting (default)' },
  { value: 'BlogPosting', label: 'Blog posting' },
  { value: 'Article', label: 'Article' },
  { value: 'NewsArticle', label: 'News article' },
  { value: 'TechArticle', label: 'Tech article' },
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
          <li>Compelling titles run 40–60 chars; descriptions 120–160 chars.</li>
          <li>1200 × 630 cover / OG images keep social cards sharp.</li>
          <li>Pick the matching schema.org type for the best rich-result eligibility.</li>
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
          <p class="mt-1 text-xs text-slate-500">Title, public URL, and category.</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label for="blog-title" class="form-label">Title <span class="text-rose-500">*</span></label>
            <input
              id="blog-title"
              v-model="form.title"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.title }"
              placeholder="Designing a smarter dashboard"
            >
            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.title }}</p>
          </div>
          <div>
            <label for="blog-slug" class="form-label">
              Slug
              <span class="font-normal text-slate-400">(auto from title)</span>
            </label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-xs text-slate-400">/blog/</span>
              <input
                id="blog-slug"
                :value="form.slug"
                type="text"
                class="form-input pl-[3.5rem]"
                :class="{ 'form-input-invalid': !!form.errors.slug }"
                placeholder="designing-a-smarter-dashboard"
                @input="onSlugInput"
              >
            </div>
            <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
          </div>
        </div>

        <div>
          <label for="blog-category" class="form-label">Category <span class="text-rose-500">*</span></label>
          <select
            id="blog-category"
            v-model="form.category_id"
            class="form-select"
            :class="{ 'form-input-invalid': !!form.errors.category_id }"
          >
            <option value="" disabled>
              Select…
            </option>
            <option v-for="c in categories" :key="c.id" :value="String(c.id)">
              {{ c.name }}
            </option>
          </select>
          <p v-if="form.errors.category_id" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.category_id }}</p>
        </div>
      </section>

      <!-- CONTENT -->
      <section
        :ref="(el) => setSectionRef('content', el)"
        data-section="content"
        class="space-y-3 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <FileText class="h-4 w-4 text-violet-600" /> Body content
          </h2>
          <p class="mt-1 text-xs text-slate-500">Write in Markdown or paste HTML — switch using the toggle in the editor.</p>
        </header>

        <BlogContentEditor
          :content="form.content"
          :content-format="form.content_format"
          @update:content="form.content = $event"
          @update:contentFormat="form.content_format = $event"
        />
        <p v-if="form.errors.content" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.content }}</p>
        <p v-if="form.errors.content_format" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.content_format }}</p>
      </section>

      <!-- COVER IMAGE -->
      <section
        :ref="(el) => setSectionRef('cover', el)"
        data-section="cover"
        class="space-y-3 scroll-mt-6"
      >
        <header>
          <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
            <ImageIcon class="h-4 w-4 text-violet-600" /> Cover image
          </h2>
          <p class="mt-1 text-xs text-slate-500">Used at the top of the post and as the default sharing image.</p>
        </header>

        <ImageUploadField
          label="Cover image"
          aspect="16 / 9"
          recommendation="Best at 1600 × 900 — JPG / PNG / WebP / AVIF / SVG"
          hint="Auto-generates a WebP variant on save."
          :file="form.image"
          :current-url="form.image_current_url"
          :remove-flag="form.image_remove"
          :error="form.errors.image"
          @update:file="(v) => (form.image = v)"
          @update:remove-flag="(v) => (form.image_remove = v)"
        />
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
            <p class="truncate text-xs text-slate-500">www.example.com › blog › <span class="text-slate-700">{{ previewSlug }}</span></p>
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
              placeholder="Defaults to post title"
              maxlength="190"
            >
            <p class="mt-1 text-[11px]" :class="stateColor[titleState]">
              {{ titleLen }} / 60 — {{ stateLabel[titleState] }}
            </p>
            <p v-if="form.errors.meta_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.meta_title }}</p>
          </div>
          <div>
            <label for="seo-keywords" class="form-label">Meta keywords</label>
            <input id="seo-keywords" v-model="form.meta_keywords" type="text" class="form-input" placeholder="design systems, ui, product">
            <p class="mt-1 text-[11px] text-slate-500">Comma-separated. Used by some legacy crawlers.</p>
            <p v-if="form.errors.meta_keywords" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.meta_keywords }}</p>
          </div>
        </div>

        <div>
          <label for="seo-desc" class="form-label">Meta description</label>
          <textarea
            id="seo-desc"
            v-model="form.meta_description"
            rows="2"
            class="form-textarea"
            :class="{ 'form-input-invalid': !!form.errors.meta_description }"
            placeholder="One- or two-sentence summary shown in search results."
          />
          <p class="mt-1 text-[11px]" :class="stateColor[descState]">
            {{ descLen }} / 160 — {{ stateLabel[descState] }}
          </p>
          <p v-if="form.errors.meta_description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.meta_description }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
          <div class="flex items-center justify-between gap-3">
            <p class="flex items-center gap-2 text-sm font-bold text-slate-800">
              <ImageIcon class="h-4 w-4 text-violet-600" /> Social card
            </p>
            <p class="text-[11px] text-slate-500">Open Graph (Facebook, LinkedIn) and Twitter</p>
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
                hint="Falls back to the cover image."
                :file="form.og_image"
                :current-url="form.og_image_current_url"
                :remove-flag="form.og_image_remove"
                :error="form.errors.og_image"
                @update:file="(v) => (form.og_image = v)"
                @update:remove-flag="(v) => (form.og_image_remove = v)"
              />
            </div>

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
              <AlertTriangle class="h-4 w-4" /> Tell search engines NOT to index this post
            </span>
            <span class="mt-0.5 block text-xs text-amber-800">
              Adds <code class="rounded bg-amber-100 px-1">noindex, nofollow</code> meta robots.
              Use for drafts, internal pages, or duplicates. Also excluded from sitemap.
            </span>
          </span>
        </label>
      </section>
    </div>
  </div>
</template>
