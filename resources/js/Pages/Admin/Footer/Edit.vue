<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import {
  BookOpen,
  Columns3,
  ExternalLink,
  FileText,
  Image,
  Link2,
  Mail,
  Plus,
  Scale,
  Search,
  Trash2,
} from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import ImageUploadField from '../../../Components/Admin/ImageUploadField.vue'
import { FOOTER_LINK_ICONS, FOOTER_SECTION_ICONS } from '../../../utils/footerIconOptions.js'

const props = defineProps({
  activeTab: { type: String, default: 'branding' },
  tabs: { type: Array, default: () => [] },
  footer: { type: Object, required: true },
})

const COLUMN_KEYS = ['products', 'marketplace', 'consultancy', 'insights', 'solutions']

const COLUMN_LABELS = {
  products: 'Products',
  marketplace: 'Marketplace',
  consultancy: 'Consultancy',
  insights: 'Insights',
  solutions: 'Solutions',
}

const iconMap = {
  branding: Image,
  column1: BookOpen,
  columns: Columns3,
  contact: Mail,
  legal: Scale,
  seo: Search,
}

const tabItems = computed(() =>
  (props.tabs.length ? props.tabs : []).map((t) => ({
    ...t,
    icon: iconMap[t.key] || Link2,
  })),
)

const currentTab = ref(props.activeTab || 'branding')

function cloneRows(list, mapFn) {
  return (list || []).map(mapFn)
}

function cloneColumnBlock(block) {
  return {
    heading: block?.heading ?? '',
    section_icon: block?.section_icon ?? 'Library',
    links: cloneRows(block?.links, (l) => ({
      label: l.label ?? '',
      href: l.href ?? '',
      icon: l.icon ?? 'ChevronRight',
    })),
  }
}

const columnsInit = {}
for (const key of COLUMN_KEYS) {
  columnsInit[key] = cloneColumnBlock(props.footer.columns?.[key])
}

const form = useForm({
  _method: 'put',
  active_tab: props.activeTab || 'branding',
  logo_path: props.footer.logo_path ?? '',
  logo_alt: props.footer.logo_alt ?? '',
  home_aria_label: props.footer.home_aria_label ?? '',
  resources_heading: props.footer.resources_heading ?? 'Resources',
  contact_heading: props.footer.contact_heading ?? 'Contact us',
  studio_label: props.footer.studio_label ?? 'Studio',
  studio_text: props.footer.studio_text ?? '',
  contact_email: props.footer.contact_email ?? '',
  connect_heading: props.footer.connect_heading ?? 'Connect with us',
  copyright_entity: props.footer.copyright_entity ?? '',
  organization_description: props.footer.organization_description ?? '',
  footer_logo: null,
  footer_logo_remove: false,
  company_links: cloneRows(props.footer.company_links, (l) => ({
    label: l.label ?? '',
    href: l.href ?? '',
    icon: l.icon ?? 'ChevronRight',
  })),
  resource_links: cloneRows(props.footer.resource_links, (l) => ({
    label: l.label ?? '',
    href: l.href ?? '',
    external: !!l.external,
    icon: l.icon ?? 'ChevronRight',
  })),
  columns: columnsInit,
  socials: cloneRows(props.footer.socials, (s) => ({ label: s.label ?? '', href: s.href ?? '' })),
  legal_links: cloneRows(props.footer.legal_links, (l) => ({
    label: l.label ?? '',
    href: l.href ?? '',
    icon: l.icon ?? 'ChevronRight',
  })),
})

function selectTab(key) {
  currentTab.value = key
  router.get('/admin/footer', { tab: key }, { preserveState: true, preserveScroll: true, replace: true })
}

function submit() {
  form.active_tab = currentTab.value
  form.post('/admin/footer', { forceFormData: true })
}

function addCompanyLink() {
  form.company_links.push({ label: '', href: '', icon: 'ChevronRight' })
}
function removeCompanyLink(i) {
  form.company_links.splice(i, 1)
}

function addResourceLink() {
  form.resource_links.push({ label: '', href: '', external: false, icon: 'ChevronRight' })
}
function removeResourceLink(i) {
  form.resource_links.splice(i, 1)
}

function addColumnLink(key) {
  form.columns[key].links.push({ label: '', href: '', icon: 'ChevronRight' })
}
function removeColumnLink(key, i) {
  form.columns[key].links.splice(i, 1)
}

function addSocial() {
  form.socials.push({ label: '', href: '' })
}
function removeSocial(i) {
  form.socials.splice(i, 1)
}

function addLegal() {
  form.legal_links.push({ label: '', href: '', icon: 'ChevronRight' })
}
function removeLegal(i) {
  form.legal_links.splice(i, 1)
}
</script>

<template>
  <div class="-mx-4 -mt-2 sm:-mx-6">
    <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-6">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Footer</h1>
          <p class="mt-1 text-sm text-slate-600">
            Manage the site footer — logo, link columns, contact info, social icons, and legal links.
          </p>
        </div>
        <a
          href="/"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50"
        >
          <ExternalLink class="h-4 w-4" />
          Preview site
        </a>
      </div>

      <nav class="mt-6 flex flex-wrap gap-1" aria-label="Footer editor tabs">
        <button
          v-for="tab in tabItems"
          :key="tab.key"
          type="button"
          class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition"
          :class="
            currentTab === tab.key
              ? 'bg-violet-600 text-white shadow-sm'
              : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
          "
          @click="selectTab(tab.key)"
        >
          <component :is="tab.icon" class="h-4 w-4" />
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <form class="space-y-0" @submit.prevent="submit">
      <div class="px-4 py-6 sm:px-6">
        <AdminFormErrorBanner :form="form" />

        <!-- Branding -->
        <section v-show="currentTab === 'branding'" class="space-y-6">
          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-900">Footer logo</h2>
            <p class="mt-1 text-xs text-slate-500">
              Upload a logo or enter a path/URL. Leave empty to use the default site logo.
            </p>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
              <ImageUploadField
                label="Footer logo"
                hint="Shown in the first column of the footer"
                :current-url="footer.logo_url"
                :file="form.footer_logo"
                :remove-flag="form.footer_logo_remove"
                :error="form.errors.footer_logo"
                @update:file="form.footer_logo = $event"
                @update:remove-flag="form.footer_logo_remove = $event"
              />
              <div class="space-y-4">
                <div>
                  <label class="form-label">Or picture path / URL</label>
                  <input
                    v-model="form.logo_path"
                    type="text"
                    class="form-input mt-1"
                    placeholder="/images/your-logo.png"
                  >
                </div>
                <div>
                  <label class="form-label">Logo alt text</label>
                  <input v-model="form.logo_alt" type="text" class="form-input mt-1" placeholder="Your company name">
                </div>
                <div>
                  <label class="form-label">Home link aria label</label>
                  <input v-model="form.home_aria_label" type="text" class="form-input mt-1" placeholder="Company home">
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Column 1 -->
        <section v-show="currentTab === 'column1'" class="space-y-6">
          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
              <div>
                <h2 class="text-base font-semibold text-slate-900">Links under the logo</h2>
                <p class="mt-1 text-xs text-slate-500">Why Azee, Approach, Contact, Careers, etc.</p>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100"
                @click="addCompanyLink"
              >
                <Plus class="h-3.5 w-3.5" />
                Add link
              </button>
            </div>
            <div class="mt-4 space-y-3">
              <div
                v-for="(row, i) in form.company_links"
                :key="'co-' + i"
                class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50/80 p-3"
              >
                <div class="min-w-[7rem] flex-1">
                  <label class="text-xs font-medium text-slate-600">Label</label>
                  <input v-model="row.label" type="text" class="form-input mt-0.5 text-sm" placeholder="Contact">
                </div>
                <div class="min-w-[10rem] flex-[2]">
                  <label class="text-xs font-medium text-slate-600">URL</label>
                  <input v-model="row.href" type="text" class="form-input mt-0.5 text-sm" placeholder="/contact">
                </div>
                <div class="min-w-[8rem]">
                  <label class="text-xs font-medium text-slate-600">Icon</label>
                  <select v-model="row.icon" class="form-input mt-0.5 text-sm">
                    <option v-for="icon in FOOTER_LINK_ICONS" :key="icon" :value="icon">{{ icon }}</option>
                  </select>
                </div>
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                  title="Remove"
                  @click="removeCompanyLink(i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="mb-4">
              <label class="form-label">Resources section heading</label>
              <input v-model="form.resources_heading" type="text" class="form-input mt-1 max-w-md">
            </div>
            <div class="flex flex-wrap items-end justify-between gap-3">
              <div>
                <h2 class="text-base font-semibold text-slate-900">Resource links</h2>
                <p class="mt-1 text-xs text-slate-500">Blog, Sitemap, Robots, etc.</p>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100"
                @click="addResourceLink"
              >
                <Plus class="h-3.5 w-3.5" />
                Add link
              </button>
            </div>
            <div class="mt-4 space-y-3">
              <div
                v-for="(row, i) in form.resource_links"
                :key="'re-' + i"
                class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50/80 p-3"
              >
                <div class="min-w-[7rem] flex-1">
                  <label class="text-xs font-medium text-slate-600">Label</label>
                  <input v-model="row.label" type="text" class="form-input mt-0.5 text-sm">
                </div>
                <div class="min-w-[10rem] flex-[2]">
                  <label class="text-xs font-medium text-slate-600">URL</label>
                  <input v-model="row.href" type="text" class="form-input mt-0.5 text-sm">
                </div>
                <div class="min-w-[8rem]">
                  <label class="text-xs font-medium text-slate-600">Icon</label>
                  <select v-model="row.icon" class="form-input mt-0.5 text-sm">
                    <option v-for="icon in FOOTER_LINK_ICONS" :key="icon" :value="icon">{{ icon }}</option>
                  </select>
                </div>
                <label class="flex shrink-0 items-center gap-2 pb-2 text-xs text-slate-600">
                  <input v-model="row.external" type="checkbox" class="rounded border-slate-300">
                  New tab
                </label>
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                  title="Remove"
                  @click="removeResourceLink(i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Link columns -->
        <section v-show="currentTab === 'columns'" class="space-y-6">
          <div
            v-for="key in COLUMN_KEYS"
            :key="key"
            class="rounded-xl border border-slate-200 bg-white p-5"
          >
            <div class="flex flex-wrap items-end justify-between gap-3">
              <div>
                <h2 class="text-base font-semibold text-slate-900">{{ COLUMN_LABELS[key] }}</h2>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100"
                @click="addColumnLink(key)"
              >
                <Plus class="h-3.5 w-3.5" />
                Add link
              </button>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="form-label">Section heading</label>
                <input v-model="form.columns[key].heading" type="text" class="form-input mt-1">
              </div>
              <div>
                <label class="form-label">Section icon</label>
                <select v-model="form.columns[key].section_icon" class="form-input mt-1">
                  <option v-for="icon in FOOTER_SECTION_ICONS" :key="icon" :value="icon">{{ icon }}</option>
                </select>
              </div>
            </div>
            <div class="mt-4 space-y-3">
              <div
                v-for="(row, i) in form.columns[key].links"
                :key="key + '-' + i"
                class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50/80 p-3"
              >
                <div class="min-w-[7rem] flex-1">
                  <label class="text-xs font-medium text-slate-600">Label</label>
                  <input v-model="row.label" type="text" class="form-input mt-0.5 text-sm">
                </div>
                <div class="min-w-[10rem] flex-[2]">
                  <label class="text-xs font-medium text-slate-600">URL</label>
                  <input v-model="row.href" type="text" class="form-input mt-0.5 text-sm" placeholder="/services">
                </div>
                <div class="min-w-[8rem]">
                  <label class="text-xs font-medium text-slate-600">Icon</label>
                  <select v-model="row.icon" class="form-input mt-0.5 text-sm">
                    <option v-for="icon in FOOTER_LINK_ICONS" :key="icon" :value="icon">{{ icon }}</option>
                  </select>
                </div>
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                  title="Remove"
                  @click="removeColumnLink(key, i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Contact & social -->
        <section v-show="currentTab === 'contact'" class="space-y-6">
          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-900">Contact block</h2>
            <p class="mt-1 text-xs text-slate-500">Studio info and email in the right footer column.</p>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <label class="form-label">Contact heading</label>
                <input v-model="form.contact_heading" type="text" class="form-input mt-1">
              </div>
              <div>
                <label class="form-label">Studio label</label>
                <input v-model="form.studio_label" type="text" class="form-input mt-1" placeholder="Studio">
              </div>
              <div class="md:col-span-2">
                <label class="form-label">Studio description</label>
                <textarea v-model="form.studio_text" rows="2" class="form-textarea mt-1" />
              </div>
              <div>
                <label class="form-label">Contact email</label>
                <input v-model="form.contact_email" type="email" class="form-input mt-1" placeholder="hello@example.com">
              </div>
              <div>
                <label class="form-label">Social heading</label>
                <input v-model="form.connect_heading" type="text" class="form-input mt-1">
              </div>
            </div>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
              <div>
                <h2 class="text-base font-semibold text-slate-900">Social profiles</h2>
                <p class="mt-1 text-xs text-slate-500">
                  Use names <strong>X</strong>, <strong>LinkedIn</strong>, <strong>Facebook</strong>, or
                  <strong>YouTube</strong> for built-in icons.
                </p>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100"
                @click="addSocial"
              >
                <Plus class="h-3.5 w-3.5" />
                Add profile
              </button>
            </div>
            <div class="mt-4 space-y-3">
              <div
                v-for="(row, i) in form.socials"
                :key="'so-' + i"
                class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50/80 p-3"
              >
                <div class="min-w-[8rem] flex-1">
                  <label class="text-xs font-medium text-slate-600">Name</label>
                  <input v-model="row.label" type="text" class="form-input mt-0.5 text-sm" placeholder="LinkedIn">
                </div>
                <div class="min-w-[12rem] flex-[2]">
                  <label class="text-xs font-medium text-slate-600">URL</label>
                  <input v-model="row.href" type="text" class="form-input mt-0.5 text-sm" placeholder="https://">
                </div>
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                  title="Remove"
                  @click="removeSocial(i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Legal -->
        <section v-show="currentTab === 'legal'" class="space-y-6">
          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <div class="mb-4">
              <label class="form-label">Copyright entity (after ©)</label>
              <input v-model="form.copyright_entity" type="text" class="form-input mt-1 max-w-md">
            </div>
            <div class="flex flex-wrap items-end justify-between gap-3">
              <div>
                <h2 class="text-base font-semibold text-slate-900">Legal links</h2>
                <p class="mt-1 text-xs text-slate-500">Terms, privacy, accessibility, etc.</p>
              </div>
              <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-100"
                @click="addLegal"
              >
                <Plus class="h-3.5 w-3.5" />
                Add link
              </button>
            </div>
            <div class="mt-4 space-y-3">
              <div
                v-for="(row, i) in form.legal_links"
                :key="'le-' + i"
                class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50/80 p-3"
              >
                <div class="min-w-[7rem] flex-1">
                  <label class="text-xs font-medium text-slate-600">Label</label>
                  <input v-model="row.label" type="text" class="form-input mt-0.5 text-sm">
                </div>
                <div class="min-w-[10rem] flex-[2]">
                  <label class="text-xs font-medium text-slate-600">URL</label>
                  <input v-model="row.href" type="text" class="form-input mt-0.5 text-sm">
                </div>
                <div class="min-w-[8rem]">
                  <label class="text-xs font-medium text-slate-600">Icon</label>
                  <select v-model="row.icon" class="form-input mt-0.5 text-sm">
                    <option v-for="icon in FOOTER_LINK_ICONS" :key="icon" :value="icon">{{ icon }}</option>
                  </select>
                </div>
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600"
                  title="Remove"
                  @click="removeLegal(i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- SEO -->
        <section v-show="currentTab === 'seo'" class="space-y-6">
          <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-base font-semibold text-slate-900">Organization description</h2>
            <p class="mt-1 text-xs text-slate-500">
              Short business description for search engines. Social URLs above are also shared in structured data.
            </p>
            <textarea v-model="form.organization_description" rows="4" class="form-textarea mt-4" />
          </div>
        </section>
      </div>

      <div
        class="sticky bottom-0 z-10 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6"
      >
        <button
          type="submit"
          class="inline-flex items-center rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700 disabled:opacity-50"
          :disabled="form.processing"
        >
          Save footer
        </button>
      </div>
    </form>
  </div>
</template>
