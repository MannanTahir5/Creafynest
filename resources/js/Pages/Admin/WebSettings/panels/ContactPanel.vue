<script setup>
import { useForm } from '@inertiajs/vue3'
import { Phone, Plus, Trash2 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  contact: { type: Object, required: true },
})

function cloneChannels(list) {
  return (list || []).map((c) => ({
    id: c.id ?? null,
    name: c.name ?? '',
    handle: c.handle ?? '',
    icon_slug: c.icon_slug ?? '',
    icon_bg: c.icon_bg ?? 'bg-emerald-500',
    qr_data: c.qr_data ?? '',
    is_active: c.is_active !== false,
  }))
}

function cloneSocials(list) {
  return (list || []).map((s) => ({
    id: s.id ?? null,
    name: s.name ?? '',
    slug: s.slug ?? '',
    href: s.href ?? '',
    bg_class: s.bg_class ?? '',
    is_active: s.is_active !== false,
  }))
}

const form = useForm({
  return_to: 'web-settings',
  hero_heading: props.contact.hero_heading ?? '',
  hero_subtitle: props.contact.hero_subtitle ?? '',
  methods_eyebrow: props.contact.methods_eyebrow ?? '',
  methods_heading: props.contact.methods_heading ?? '',
  methods_description: props.contact.methods_description ?? '',
  chat_card_heading: props.contact.chat_card_heading ?? '',
  chat_card_description: props.contact.chat_card_description ?? '',
  chat_card_button_label: props.contact.chat_card_button_label ?? '',
  chat_card_button_href: props.contact.chat_card_button_href ?? '',
  chat_card_is_active: props.contact.chat_card_is_active !== false,
  cta_heading: props.contact.cta_heading ?? '',
  cta_description: props.contact.cta_description ?? '',
  cta_button_label: props.contact.cta_button_label ?? '',
  cta_button_href: props.contact.cta_button_href ?? '',
  cta_is_active: props.contact.cta_is_active !== false,
  social_heading: props.contact.social_heading ?? '',
  social_is_active: props.contact.social_is_active !== false,
  is_active: props.contact.is_active !== false,
  messaging_channels: cloneChannels(props.contact.messaging_channels),
  social_links: cloneSocials(props.contact.social_links),
})

function save() {
  form.put('/admin/contact-page', { preserveScroll: true })
}

function addChannel() {
  form.messaging_channels.push({ id: null, name: '', handle: '', icon_slug: '', icon_bg: 'bg-emerald-500', qr_data: '', is_active: true })
}

function removeChannel(i) {
  form.messaging_channels.splice(i, 1)
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <Phone class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">Contact page</h2>
      </div>

      <div class="mt-6 max-w-2xl space-y-4">
        <div>
          <label class="form-label" for="hero">Hero heading</label>
          <input id="hero" v-model="form.hero_heading" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" for="sub">Hero subtitle</label>
          <textarea id="sub" v-model="form.hero_subtitle" rows="2" class="form-textarea" />
        </div>
        <div>
          <label class="form-label" for="methods-h">Methods heading</label>
          <input id="methods-h" v-model="form.methods_heading" type="text" class="form-input">
        </div>
        <div>
          <label class="form-label" for="methods-d">Methods description</label>
          <textarea id="methods-d" v-model="form.methods_description" rows="3" class="form-textarea" />
        </div>

        <div class="border-t border-slate-200 pt-4">
          <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-800">Messaging channels</p>
            <button type="button" class="inline-flex items-center gap-1 text-sm font-semibold text-violet-700" @click="addChannel">
              <Plus class="h-4 w-4" /> Add
            </button>
          </div>
          <div v-for="(c, i) in form.messaging_channels" :key="i" class="mt-3 grid gap-2 rounded-lg border border-slate-200 p-3 md:grid-cols-2">
            <input v-model="c.name" type="text" class="form-input" placeholder="Name" required>
            <input v-model="c.handle" type="text" class="form-input" placeholder="Handle / value">
            <button type="button" class="text-sm font-semibold text-rose-600 md:col-span-2 md:justify-self-end" @click="removeChannel(i)">
              <Trash2 class="inline h-4 w-4" /> Remove
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
