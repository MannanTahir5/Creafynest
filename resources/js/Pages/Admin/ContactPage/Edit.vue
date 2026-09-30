<script setup>
import { computed, ref } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import {
  ArrowDown,
  ArrowUp,
  Bell,
  ExternalLink,
  Eye,
  FileText,
  Mail,
  MapPinned,
  Megaphone,
  MessageCircle,
  Plus,
  Share2,
  Sparkles,
  Trash2,
} from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import { simpleIconSvgUrl } from '../../../utils/simpleIconCdn.js'

const props = defineProps({
  activeTab: { type: String, default: 'hero' },
  tabs: { type: Array, default: () => [] },
  section: { type: Object, required: true },
  previewUrl: { type: String, default: '/contact' },
})

const iconMap = {
  hero: Sparkles,
  methods: MessageCircle,
  info: Mail,
  form: FileText,
  map: MapPinned,
  cta: Megaphone,
  social: Share2,
  newsletter: Bell,
}

const tabItems = computed(() =>
  (props.tabs.length ? props.tabs : []).map((t) => ({
    ...t,
    icon: iconMap[t.key] || Sparkles,
  })),
)

const currentTab = ref(props.activeTab || 'hero')

function cloneChannels(list) {
  return (list || []).map((c) => ({
    id: c.id ?? null,
    name: c.name ?? '',
    handle: c.handle ?? '',
    icon_slug: c.icon_slug ?? '',
    icon_bg: c.icon_bg ?? 'bg-emerald-500',
    qr_data: c.qr_data ?? '',
    is_active: c.is_active === undefined ? true : !!c.is_active,
  }))
}

function cloneSocials(list) {
  return (list || []).map((s) => ({
    id: s.id ?? null,
    name: s.name ?? '',
    slug: s.slug ?? '',
    href: s.href ?? '',
    bg_class: s.bg_class ?? '',
    is_active: s.is_active === undefined ? true : !!s.is_active,
  }))
}

function cloneQuickReplies(list) {
  return [...(list || [])]
}

const form = useForm({
  active_tab: props.activeTab || 'hero',

  hero_heading: props.section.hero_heading ?? '',
  hero_subtitle: props.section.hero_subtitle ?? '',

  methods_eyebrow: props.section.methods_eyebrow ?? '',
  methods_heading: props.section.methods_heading ?? '',
  methods_description: props.section.methods_description ?? '',

  chat_card_heading: props.section.chat_card_heading ?? '',
  chat_card_description: props.section.chat_card_description ?? '',
  chat_card_button_label: props.section.chat_card_button_label ?? '',
  chat_card_button_href: props.section.chat_card_button_href ?? '',
  chat_card_is_active: props.section.chat_card_is_active === undefined ? true : !!props.section.chat_card_is_active,
  chat_badge_label: props.section.chat_badge_label ?? '',
  chat_team_name: props.section.chat_team_name ?? '',
  chat_status_text: props.section.chat_status_text ?? '',
  chat_footer_note: props.section.chat_footer_note ?? '',
  chat_greeting: props.section.chat_greeting ?? '',
  chat_input_placeholder: props.section.chat_input_placeholder ?? '',
  chat_quick_replies: cloneQuickReplies(props.section.chat_quick_replies),

  info_is_active: !!props.section.info_is_active,
  info_heading: props.section.info_heading ?? '',
  info_description: props.section.info_description ?? '',
  contact_email: props.section.contact_email ?? '',
  contact_phone: props.section.contact_phone ?? '',
  contact_address: props.section.contact_address ?? '',
  office_hours: props.section.office_hours ?? '',

  form_is_active: !!props.section.form_is_active,
  form_heading: props.section.form_heading ?? '',
  form_subtitle: props.section.form_subtitle ?? '',
  form_name_label: props.section.form_name_label ?? '',
  form_email_label: props.section.form_email_label ?? '',
  form_message_label: props.section.form_message_label ?? '',
  form_submit_label: props.section.form_submit_label ?? '',
  form_success_message: props.section.form_success_message ?? '',
  form_message_max_length: props.section.form_message_max_length ?? 5000,

  map_is_active: !!props.section.map_is_active,
  map_heading: props.section.map_heading ?? '',
  map_embed_url: props.section.map_embed_url ?? '',
  map_address_label: props.section.map_address_label ?? '',

  cta_heading: props.section.cta_heading ?? '',
  cta_description: props.section.cta_description ?? '',
  cta_button_label: props.section.cta_button_label ?? '',
  cta_button_href: props.section.cta_button_href ?? '',
  cta_is_active: props.section.cta_is_active === undefined ? true : !!props.section.cta_is_active,
  cta_eyebrow: props.section.cta_eyebrow ?? '',
  cta_secondary_label: props.section.cta_secondary_label ?? '',
  cta_secondary_href: props.section.cta_secondary_href ?? '',
  cta_response_note: props.section.cta_response_note ?? '',
  cta_trusted_label: props.section.cta_trusted_label ?? '',
  cta_quick_label: props.section.cta_quick_label ?? '',

  social_heading: props.section.social_heading ?? '',
  social_is_active: props.section.social_is_active === undefined ? true : !!props.section.social_is_active,

  newsletter_is_active: props.section.newsletter_is_active === undefined ? true : !!props.section.newsletter_is_active,
  newsletter_heading: props.section.newsletter_heading ?? '',
  newsletter_subtitle: props.section.newsletter_subtitle ?? '',
  newsletter_placeholder: props.section.newsletter_placeholder ?? '',
  newsletter_button_label: props.section.newsletter_button_label ?? '',

  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,

  messaging_channels: cloneChannels(props.section.messaging_channels),
  social_links: cloneSocials(props.section.social_links),
})

function selectTab(key) {
  currentTab.value = key
  router.get('/admin/contact-page', { tab: key }, { preserveState: true, preserveScroll: true, replace: true })
}

function moveItem(list, index, delta) {
  const target = index + delta
  if (target < 0 || target >= list.length) return
  const [m] = list.splice(index, 1)
  list.splice(target, 0, m)
}

function addChannel() {
  form.messaging_channels.push({
    id: null,
    name: '',
    handle: '',
    icon_slug: '',
    icon_bg: 'bg-emerald-500',
    qr_data: '',
    is_active: true,
  })
}

function removeChannel(i) {
  form.messaging_channels.splice(i, 1)
}

function addSocial() {
  form.social_links.push({
    id: null,
    name: '',
    slug: '',
    href: '',
    bg_class: 'bg-slate-700',
    is_active: true,
  })
}

function removeSocial(i) {
  form.social_links.splice(i, 1)
}

function addQuickReply() {
  form.chat_quick_replies.push('')
}

function removeQuickReply(i) {
  form.chat_quick_replies.splice(i, 1)
}

function brandIcon(slug) {
  return slug ? simpleIconSvgUrl(slug) : null
}

function errorAt(prefix, i, key) {
  return form.errors[`${prefix}.${i}.${key}`]
}

function submit() {
  form.active_tab = currentTab.value
  form.put('/admin/contact-page')
}
</script>

<template>
  <div class="-mx-4 -mt-2 sm:-mx-6">
    <div class="border-b border-slate-200 bg-white px-4 py-5 sm:px-6">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Contact page</h1>
          <p class="mt-1 text-sm text-slate-600">
            Edit every section on the public <code class="rounded bg-slate-100 px-1">/contact</code> page.
          </p>
        </div>
        <a
          :href="previewUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50"
        >
          <Eye class="h-4 w-4" />
          Preview
          <ExternalLink class="h-3.5 w-3.5 opacity-60" />
        </a>
      </div>

      <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-8">
        <button
          v-for="tab in tabItems"
          :key="tab.key"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 text-sm font-semibold transition"
          :class="
            currentTab === tab.key
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

    <form class="px-4 py-6 sm:px-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <!-- Hero -->
      <section v-show="currentTab === 'hero'" class="rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold text-slate-900">Hero banner</h2>
        <p class="mt-1 text-xs text-slate-500">The navy banner at the top of the page.</p>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input
              v-model="form.hero_heading"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!form.errors.hero_heading }"
              placeholder="Contact Creafynest"
            >
            <p v-if="form.errors.hero_heading" class="mt-1 text-xs text-rose-600" role="alert">
              {{ form.errors.hero_heading }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Subtitle</label>
            <textarea
              v-model="form.hero_subtitle"
              rows="2"
              class="form-textarea"
              :class="{ 'form-input-invalid': !!form.errors.hero_subtitle }"
            />
            <p v-if="form.errors.hero_subtitle" class="mt-1 text-xs text-rose-600" role="alert">
              {{ form.errors.hero_subtitle }}
            </p>
          </div>
        </div>
      </section>

      <!-- Methods & chat -->
      <div v-show="currentTab === 'methods'" class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5">
          <h2 class="text-base font-semibold text-slate-900">Preferred contact method</h2>
          <p class="mt-1 text-xs text-slate-500">Section heading above the chat card and QR channels.</p>
          <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <label class="block text-sm font-medium text-slate-700">Eyebrow (optional)</label>
              <input v-model="form.methods_eyebrow" type="text" class="form-input">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-slate-700">Heading</label>
              <input v-model="form.methods_heading" type="text" class="form-input">
            </div>
            <div class="md:col-span-3">
              <label class="block text-sm font-medium text-slate-700">Description</label>
              <textarea v-model="form.methods_description" rows="2" class="form-textarea" />
            </div>
          </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Chat card &amp; mockup</h2>
            <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
              <input v-model="form.chat_card_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
              Visible
            </label>
          </div>
          <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-medium text-slate-600">Badge label</label>
              <input v-model="form.chat_badge_label" type="text" class="form-input" placeholder="Live Chat">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Card heading</label>
              <input v-model="form.chat_card_heading" type="text" class="form-input">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-medium text-slate-600">Card description</label>
              <textarea v-model="form.chat_card_description" rows="2" class="form-textarea" />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Button label</label>
              <input v-model="form.chat_card_button_label" type="text" class="form-input">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Button link / anchor</label>
              <input v-model="form.chat_card_button_href" type="text" class="form-input" placeholder="#contact-channels">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Mockup team name</label>
              <input v-model="form.chat_team_name" type="text" class="form-input">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Mockup status text</label>
              <input v-model="form.chat_status_text" type="text" class="form-input">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-medium text-slate-600">Footer note (under button)</label>
              <input v-model="form.chat_footer_note" type="text" class="form-input">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-medium text-slate-600">Mockup greeting message</label>
              <textarea v-model="form.chat_greeting" rows="2" class="form-textarea" />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600">Input placeholder</label>
              <input v-model="form.chat_input_placeholder" type="text" class="form-input">
            </div>
          </div>

          <div class="mt-6 border-t border-slate-200 pt-4">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-slate-900">Quick reply chips</p>
              <button
                type="button"
                class="inline-flex items-center gap-1 text-sm font-semibold text-violet-700"
                @click="addQuickReply"
              >
                <Plus class="h-4 w-4" />
                Add
              </button>
            </div>
            <ul class="mt-3 space-y-2">
              <li v-for="(reply, i) in form.chat_quick_replies" :key="i" class="flex items-center gap-2">
                <input v-model="form.chat_quick_replies[i]" type="text" class="form-input flex-1">
                <button
                  type="button"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50"
                  aria-label="Remove reply"
                  @click="removeQuickReply(i)"
                >
                  <Trash2 class="h-4 w-4" />
                </button>
              </li>
            </ul>
          </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5">
          <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
              <h2 class="text-base font-semibold text-slate-900">Messaging channels (QR cards)</h2>
              <p class="mt-1 text-xs text-slate-500">WeChat, WhatsApp, etc. Icons use Simple Icons slugs.</p>
            </div>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
              @click="addChannel"
            >
              <Plus class="h-4 w-4" />
              Add channel
            </button>
          </div>

          <ul class="mt-5 space-y-4">
            <li
              v-for="(channel, i) in form.messaging_channels"
              :key="i"
              class="rounded-lg border border-slate-200 bg-slate-50/40 p-4"
            >
              <div class="flex items-start gap-4">
                <div class="flex flex-1 flex-col gap-3">
                  <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                      <label class="block text-xs font-medium text-slate-600">Name</label>
                      <input
                        v-model="channel.name"
                        type="text"
                        class="form-input"
                        :class="{ 'form-input-invalid': !!errorAt('messaging_channels', i, 'name') }"
                      >
                      <p v-if="errorAt('messaging_channels', i, 'name')" class="mt-1 text-xs text-rose-600" role="alert">
                        {{ errorAt('messaging_channels', i, 'name') }}
                      </p>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600">Handle / phone</label>
                      <input v-model="channel.handle" type="text" class="form-input">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600">Icon slug</label>
                      <div class="flex items-center gap-2">
                        <input v-model="channel.icon_slug" type="text" class="form-input">
                        <span
                          v-if="brandIcon(channel.icon_slug)"
                          :class="['inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full', channel.icon_bg || 'bg-emerald-500']"
                        >
                          <img :src="brandIcon(channel.icon_slug)" alt="" class="h-5 w-5 brightness-0 invert">
                        </span>
                      </div>
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-slate-600">Icon background class</label>
                      <input v-model="channel.icon_bg" type="text" class="form-input">
                    </div>
                    <div class="md:col-span-2">
                      <label class="block text-xs font-medium text-slate-600">QR data</label>
                      <input v-model="channel.qr_data" type="text" class="form-input">
                    </div>
                  </div>
                  <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                    <input v-model="channel.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                    Visible
                  </label>
                </div>
                <div class="flex shrink-0 flex-col gap-1">
                  <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === 0" @click="moveItem(form.messaging_channels, i, -1)">
                    <ArrowUp class="h-4 w-4" />
                  </button>
                  <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === form.messaging_channels.length - 1" @click="moveItem(form.messaging_channels, i, 1)">
                    <ArrowDown class="h-4 w-4" />
                  </button>
                  <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" @click="removeChannel(i)">
                    <Trash2 class="h-4 w-4" />
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </section>
      </div>

      <!-- Info -->
      <section v-show="currentTab === 'info'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Contact information</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.info_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible
          </label>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.info_heading" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.info_description" rows="2" class="form-textarea" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input v-model="form.contact_email" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Phone</label>
            <input v-model="form.contact_phone" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Address</label>
            <textarea v-model="form.contact_address" rows="2" class="form-textarea" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Office hours</label>
            <input v-model="form.office_hours" type="text" class="form-input">
          </div>
        </div>
      </section>

      <!-- Form -->
      <section v-show="currentTab === 'form'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Contact form</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.form_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible
          </label>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.form_heading" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Subtitle</label>
            <textarea v-model="form.form_subtitle" rows="2" class="form-textarea" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Name label</label>
            <input v-model="form.form_name_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Email label</label>
            <input v-model="form.form_email_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Message label</label>
            <input v-model="form.form_message_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Submit label</label>
            <input v-model="form.form_submit_label" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Success message</label>
            <input v-model="form.form_success_message" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Message max length</label>
            <input v-model.number="form.form_message_max_length" type="number" min="500" max="10000" class="form-input">
          </div>
        </div>
      </section>

      <!-- Map -->
      <section v-show="currentTab === 'map'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Map embed</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.map_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible
          </label>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.map_heading" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Address label (optional)</label>
            <input v-model="form.map_address_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Embed URL</label>
            <textarea v-model="form.map_embed_url" rows="3" class="form-textarea font-mono text-sm" placeholder="https://www.google.com/maps/embed?pb=…" />
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section v-show="currentTab === 'cta'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Call-to-action banner</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.cta_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible
          </label>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Eyebrow</label>
            <input v-model="form.cta_eyebrow" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.cta_heading" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.cta_description" rows="2" class="form-textarea" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Primary button label</label>
            <input v-model="form.cta_button_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Primary button link</label>
            <input v-model="form.cta_button_href" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Secondary button label</label>
            <input v-model="form.cta_secondary_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Secondary button link</label>
            <input v-model="form.cta_secondary_href" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Response note</label>
            <input v-model="form.cta_response_note" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Decorative: trusted label</label>
            <input v-model="form.cta_trusted_label" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Decorative: quick reply label</label>
            <input v-model="form.cta_quick_label" type="text" class="form-input">
          </div>
        </div>
      </section>

      <!-- Social -->
      <section v-show="currentTab === 'social'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <div>
            <h2 class="text-base font-semibold text-slate-900">Social links</h2>
            <p class="mt-1 text-xs text-slate-500">Circular icons at the bottom of the page.</p>
          </div>
          <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
              <input v-model="form.social_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
              Visible
            </label>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
              @click="addSocial"
            >
              <Plus class="h-4 w-4" />
              Add link
            </button>
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700">Section heading</label>
          <input v-model="form.social_heading" type="text" class="form-input">
        </div>
        <ul class="mt-5 space-y-4">
          <li v-for="(social, i) in form.social_links" :key="i" class="rounded-lg border border-slate-200 bg-slate-50/40 p-4">
            <div class="flex items-start gap-4">
              <div class="flex flex-1 flex-col gap-3">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Name</label>
                    <input v-model="social.name" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorAt('social_links', i, 'name') }">
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Icon slug</label>
                    <input v-model="social.slug" type="text" class="form-input">
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-600">URL</label>
                    <input v-model="social.href" type="text" class="form-input">
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Background class</label>
                    <input v-model="social.bg_class" type="text" class="form-input">
                  </div>
                </div>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                  <input v-model="social.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                  Visible
                </label>
              </div>
              <div class="flex shrink-0 flex-col gap-1">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === 0" @click="moveItem(form.social_links, i, -1)">
                  <ArrowUp class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === form.social_links.length - 1" @click="moveItem(form.social_links, i, 1)">
                  <ArrowDown class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" @click="removeSocial(i)">
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </li>
        </ul>
      </section>

      <!-- Newsletter -->
      <section v-show="currentTab === 'newsletter'" class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Newsletter band</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.newsletter_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible on contact page
          </label>
        </div>
        <p class="mt-1 text-xs text-slate-500">Also shared sitewide in the footer when the contact page is published.</p>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.newsletter_heading" type="text" class="form-input">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Button label</label>
            <input v-model="form.newsletter_button_label" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Subtitle</label>
            <input v-model="form.newsletter_subtitle" type="text" class="form-input">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Email placeholder</label>
            <input v-model="form.newsletter_placeholder" type="text" class="form-input">
          </div>
        </div>
      </section>

      <div
        class="sticky bottom-0 -mx-4 mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white/90 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6"
      >
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
          Publish contact page
        </label>
        <div class="flex items-center gap-3">
          <Link href="/admin" class="text-sm font-semibold text-slate-600 hover:underline">Back</Link>
          <button
            type="submit"
            class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 disabled:opacity-60"
            :disabled="form.processing"
          >
            Save
          </button>
        </div>
      </div>
    </form>
  </div>
</template>
