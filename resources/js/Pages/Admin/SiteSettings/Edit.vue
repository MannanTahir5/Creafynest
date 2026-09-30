<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  settings: { type: Object, required: true },
  effective: { type: Object, required: true },
})

const form = useForm({
  app_name: props.settings.app_name ?? '',
  app_url: props.settings.app_url ?? '',
  timezone: props.settings.timezone ?? '',
  locale: props.settings.locale ?? '',
  ga4_measurement_id: props.settings.ga4_measurement_id ?? '',
})

function submit() {
  form.put('/admin/site-settings', { preserveScroll: true })
}

function err(path) {
  return form.errors[path]
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-slate-100">Site settings</h1>
      <Link href="/admin" class="text-sm font-semibold text-slate-700 hover:underline dark:text-slate-300">Back</Link>
    </div>

    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 dark:text-slate-400">
      Override values that normally come from your environment (<code class="rounded bg-slate-100 px-1 py-0.5 text-xs dark:bg-slate-800">.env</code>
      / config). Leave a field empty to fall back to the environment default. These settings apply on the next request after saving.
    </p>

    <form class="mt-8 space-y-8" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Application</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Public name and base URL are used for titles, canonical links, JSON-LD, sitemap URLs, and shared Inertia props.
        </p>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="app-name">Application name</label>
            <input id="app-name" v-model="form.app_name" type="text" class="form-input mt-1" placeholder="" autocomplete="organization">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              Effective: <span class="font-medium text-slate-700 dark:text-slate-300">{{ effective.app_name }}</span>
            </p>
            <p v-if="err('app_name')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('app_name') }}</p>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="app-url">Application URL</label>
            <input id="app-url" v-model="form.app_url" type="url" class="form-input mt-1" placeholder="https://example.com" autocomplete="url">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              No trailing slash. Effective:
              <span class="font-medium text-slate-700 dark:text-slate-300">{{ effective.app_url }}</span>
            </p>
            <p v-if="err('app_url')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('app_url') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="tz">Timezone</label>
            <input id="tz" v-model="form.timezone" type="text" class="form-input mt-1" placeholder="UTC" autocomplete="off">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              IANA name (e.g. <code class="rounded bg-slate-100 px-1 text-[11px] dark:bg-slate-800">America/New_York</code>). Effective:
              <span class="font-medium text-slate-700 dark:text-slate-300">{{ effective.timezone }}</span>
            </p>
            <p v-if="err('timezone')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('timezone') }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="locale">Locale</label>
            <input id="locale" v-model="form.locale" type="text" class="form-input mt-1" placeholder="en" autocomplete="off">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              Effective: <span class="font-medium text-slate-700 dark:text-slate-300">{{ effective.locale }}</span>
            </p>
            <p v-if="err('locale')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('locale') }}</p>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Analytics</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          When set, the GA4 snippet is injected from the root Blade layout (same as <code class="rounded bg-slate-100 px-1 text-xs dark:bg-slate-800">GA4_MEASUREMENT_ID</code>).
        </p>
        <div class="mt-5 max-w-xl">
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="ga4">GA4 measurement ID</label>
          <input id="ga4" v-model="form.ga4_measurement_id" type="text" class="form-input mt-1" placeholder="G-XXXXXXXXXX" autocomplete="off">
          <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            Effective: <span class="font-medium text-slate-700 dark:text-slate-300">{{ effective.ga4_measurement_id || '—' }}</span>
          </p>
          <p v-if="err('ga4_measurement_id')" class="mt-1 text-xs text-rose-600" role="alert">{{ err('ga4_measurement_id') }}</p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
          :disabled="form.processing"
        >
          Save site settings
        </button>
        <p v-if="form.recentlySuccessful" class="text-sm text-emerald-700 dark:text-emerald-400">Saved.</p>
      </div>
    </form>
  </div>
</template>
