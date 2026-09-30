<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  definitions: { type: Object, required: true },
  site: { type: Object, required: true },
  pages: { type: Object, required: true },
})

const pageKeys = Object.keys(props.definitions)

const pageOgImages = {}
const pageOgRemove = {}
for (const k of pageKeys) {
  pageOgImages[k] = null
  pageOgRemove[k] = false
}

const form = useForm({
  site: {
    default_og_image_alt: props.site.default_og_image_alt ?? '',
    twitter_site: props.site.twitter_site ?? '',
    twitter_creator: props.site.twitter_creator ?? '',
    og_locale: props.site.og_locale ?? 'en_US',
    default_robots: props.site.default_robots ?? '',
    theme_color: props.site.theme_color ?? '',
    enable_blog_search_action: !!props.site.enable_blog_search_action,
  },
  pages: JSON.parse(JSON.stringify(props.pages)),
  default_og_image: null,
  default_og_image_remove: false,
  page_og_images: pageOgImages,
  page_og_remove: pageOgRemove,
})

function submit() {
  form.put('/admin/seo-settings', {
    forceFormData: true,
    preserveScroll: true,
  })
}

function err(path) {
  return form.errors[path]
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">SEO</h1>
      <Link href="/admin" class="text-sm font-semibold text-slate-700 hover:underline dark:text-slate-300">Back</Link>
    </div>

    <p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-slate-400">
      Site-wide defaults apply to every public page unless overridden below or on individual services/blog posts. Use concise titles (under ~60
      characters with the site name) and meta descriptions around 150–160 characters for best snippets.
    </p>

    <form class="mt-8 space-y-10" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Site defaults</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Twitter handles (without @), default Open Graph locale, optional sitewide robots hint, theme color, and fallback social image.
        </p>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="tw-site">Twitter / X site</label>
            <input
              id="tw-site"
              v-model="form.site.twitter_site"
              type="text"
              class="form-input mt-1"
              placeholder="yourbrand"
              autocomplete="off"
            >
            <p v-if="err('site.twitter_site')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.twitter_site') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="tw-creator">Twitter / X creator</label>
            <input
              id="tw-creator"
              v-model="form.site.twitter_creator"
              type="text"
              class="form-input mt-1"
              placeholder="founder_handle"
              autocomplete="off"
            >
            <p v-if="err('site.twitter_creator')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.twitter_creator') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="og-locale">Open Graph locale</label>
            <input id="og-locale" v-model="form.site.og_locale" type="text" class="form-input mt-1" placeholder="en_US">
            <p v-if="err('site.og_locale')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.og_locale') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="theme-color">Theme color</label>
            <input id="theme-color" v-model="form.site.theme_color" type="text" class="form-input mt-1" placeholder="#0f172a">
            <p v-if="err('site.theme_color')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.theme_color') }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="default-robots">Default robots (when a page does not set its own)</label>
            <input
              id="default-robots"
              v-model="form.site.default_robots"
              type="text"
              class="form-input mt-1 font-mono text-sm"
              placeholder="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
            >
            <p v-if="err('site.default_robots')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.default_robots') }}</p>
          </div>
          <div class="md:col-span-2 flex flex-wrap items-center gap-3">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
              <input v-model="form.site.enable_blog_search_action" type="checkbox" class="rounded border-slate-300">
              Enable blog search in sitewide JSON-LD (WebSite SearchAction)
            </label>
          </div>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-6 dark:border-slate-700">
          <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Default Open Graph image</p>
          <p class="mt-1 text-xs text-slate-500">Used when a page has no specific image (1200×630 recommended).</p>
          <div v-if="site.default_og_image_url" class="mt-3">
            <img :src="site.default_og_image_url" alt="" class="max-h-32 rounded-lg border border-slate-200 dark:border-slate-600">
          </div>
          <div class="mt-3 flex flex-wrap items-center gap-3">
            <input type="file" accept="image/*" class="text-sm" @change="form.default_og_image = $event.target.files?.[0] ?? null">
            <label v-if="site.default_og_image_url" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
              <input v-model="form.default_og_image_remove" type="checkbox" class="rounded border-slate-300">
              Remove current image
            </label>
          </div>
          <div class="mt-3">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="default-og-alt">Image alt text</label>
            <input id="default-og-alt" v-model="form.site.default_og_image_alt" type="text" class="form-input mt-1" placeholder="Company name or product">
            <p v-if="err('site.default_og_image_alt')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('site.default_og_image_alt') }}</p>
          </div>
          <p v-if="err('default_og_image')" class="mt-2 text-xs text-rose-600" role="alert">{{ err('default_og_image') }}</p>
        </div>
      </div>

      <div v-for="key in pageKeys" :key="key" class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
            {{ definitions[key].label }}
          </h2>
          <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ definitions[key].path }}</code>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`title-${key}`">Title stem</label>
            <input
              :id="`title-${key}`"
              v-model="form.pages[key].title_stem"
              type="text"
              class="form-input mt-1"
              :placeholder="definitions[key].label"
            >
            <p class="mt-1 text-xs text-slate-500">Becomes “{{ form.pages[key].title_stem || '…' }} | {{ $page.props.site?.name }}” in the &lt;title&gt;.</p>
            <p v-if="err(`pages.${key}.title_stem`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.title_stem`) }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`desc-${key}`">Meta description</label>
            <textarea
              :id="`desc-${key}`"
              v-model="form.pages[key].meta_description"
              rows="3"
              class="form-input mt-1"
            />
            <p v-if="err(`pages.${key}.meta_description`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.meta_description`) }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`kw-${key}`">Meta keywords</label>
            <input :id="`kw-${key}`" v-model="form.pages[key].meta_keywords" type="text" class="form-input mt-1" placeholder="comma, separated">
            <p v-if="err(`pages.${key}.meta_keywords`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.meta_keywords`) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`ogt-${key}`">OG title override</label>
            <input :id="`ogt-${key}`" v-model="form.pages[key].og_title" type="text" class="form-input mt-1">
            <p v-if="err(`pages.${key}.og_title`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.og_title`) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`ogd-${key}`">OG description override</label>
            <textarea :id="`ogd-${key}`" v-model="form.pages[key].og_description" rows="2" class="form-input mt-1" />
            <p v-if="err(`pages.${key}.og_description`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.og_description`) }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`canon-${key}`">Canonical URL override</label>
            <input :id="`canon-${key}`" v-model="form.pages[key].canonical_url" type="url" class="form-input mt-1" placeholder="https://…">
            <p v-if="err(`pages.${key}.canonical_url`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.canonical_url`) }}</p>
          </div>
          <div class="md:col-span-2 flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
              <input v-model="form.pages[key].noindex" type="checkbox" class="rounded border-slate-300">
              Noindex this page
            </label>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`robots-${key}`">Page robots (optional)</label>
            <input :id="`robots-${key}`" v-model="form.pages[key].robots" type="text" class="form-input mt-1 font-mono text-sm" placeholder="Overrides default when set and page is indexable">
            <p v-if="err(`pages.${key}.robots`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.robots`) }}</p>
          </div>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-6 dark:border-slate-700">
          <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Page Open Graph image</p>
          <div v-if="pages[key].og_image_url" class="mt-3">
            <img :src="pages[key].og_image_url" alt="" class="max-h-28 rounded-lg border border-slate-200 dark:border-slate-600">
          </div>
          <div class="mt-3 flex flex-wrap items-center gap-3">
            <input type="file" accept="image/*" class="text-sm" @change="form.page_og_images[key] = $event.target.files?.[0] ?? null">
            <label v-if="pages[key].og_image_url" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
              <input v-model="form.page_og_remove[key]" type="checkbox" class="rounded border-slate-300">
              Remove
            </label>
          </div>
          <div class="mt-3">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" :for="`ogimgalt-${key}`">OG image alt</label>
            <input :id="`ogimgalt-${key}`" v-model="form.pages[key].og_image_alt" type="text" class="form-input mt-1">
            <p v-if="err(`pages.${key}.og_image_alt`)" class="mt-1 text-xs text-rose-600" role="alert">{{ err(`pages.${key}.og_image_alt`) }}</p>
          </div>
          <p v-if="err(`page_og_images.${key}`)" class="mt-2 text-xs text-rose-600" role="alert">{{ err(`page_og_images.${key}`) }}</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-50 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
          :disabled="form.processing"
        >
          Save SEO settings
        </button>
        <Link href="/admin" class="text-sm font-semibold text-slate-700 hover:underline dark:text-slate-300">Cancel</Link>
      </div>
    </form>
  </div>
</template>
