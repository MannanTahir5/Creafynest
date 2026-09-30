<script setup>
import { useForm } from '@inertiajs/vue3'
import { Plus, Share2, Trash2 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  social: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  connect_heading: props.social.connect_heading ?? '',
  twitter_site: props.social.twitter_site ?? '',
  twitter_creator: props.social.twitter_creator ?? '',
  socials: (props.social.socials ?? []).map((s) => ({ label: s.label ?? '', href: s.href ?? '' })),
})

function save() {
  form.put('/admin/web-settings/social', { preserveScroll: true })
}

function addSocial() {
  form.socials.push({ label: '', href: '' })
}

function removeSocial(i) {
  form.socials.splice(i, 1)
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <Share2 class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Social links</h2>
      </div>

      <div class="mt-6 max-w-2xl space-y-4">
        <div>
          <label class="form-label" for="heading">Footer social heading</label>
          <input id="heading" v-model="form.connect_heading" type="text" class="form-input">
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="form-label" for="tw-site">Twitter / X site</label>
            <input id="tw-site" v-model="form.twitter_site" type="text" class="form-input" placeholder="yourbrand">
          </div>
          <div>
            <label class="form-label" for="tw-creator">Twitter / X creator</label>
            <input id="tw-creator" v-model="form.twitter_creator" type="text" class="form-input">
          </div>
        </div>

        <div class="border-t border-slate-200 pt-4">
          <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-800">Footer social icons</p>
            <button type="button" class="inline-flex items-center gap-1 text-sm font-semibold text-violet-700" @click="addSocial">
              <Plus class="h-4 w-4" /> Add
            </button>
          </div>
          <div v-for="(s, i) in form.socials" :key="i" class="mt-3 grid gap-2 rounded-lg border border-slate-200 p-3 md:grid-cols-[1fr_1fr_auto]">
            <input v-model="s.label" type="text" class="form-input" placeholder="Label">
            <input v-model="s.href" type="url" class="form-input" placeholder="https://">
            <button type="button" class="rounded-lg p-2 text-rose-600 hover:bg-rose-50" @click="removeSocial(i)">
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
