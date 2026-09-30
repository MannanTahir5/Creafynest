<script setup>
import { computed } from 'vue'
import {
  ArrowRight,
  CalendarDays,
  Clock,
  Mail,
  MapPin,
  MessageCircle,
  Phone,
  Send,
  Sparkles,
  Star,
  Zap,
} from 'lucide-vue-next'
import ContactForm from '../Components/ContactForm.vue'
import Container from '../Components/Ui/Container.vue'
import { simpleIconSvgUrl } from '../utils/simpleIconCdn.js'

const props = defineProps({
  contactPage: { type: Object, default: null },
})

const defaultMessagingChannels = [
  {
    name: 'WeChat',
    handle: '+86 150 5326 2325',
    icon_slug: 'wechat',
    icon_bg: 'bg-emerald-500',
    qr_data: 'wechat://creafynest',
  },
  {
    name: 'WhatsApp',
    handle: '+44 7537 132205',
    icon_slug: 'whatsapp',
    icon_bg: 'bg-green-500',
    qr_data: 'https://wa.me/447537132205',
  },
]

const defaultSocialLinks = [
  { name: 'Facebook', slug: 'facebook', href: 'https://facebook.com/', bg_class: 'bg-[#1877F2]' },
  {
    name: 'Instagram',
    slug: 'instagram',
    href: 'https://instagram.com/',
    bg_class: 'bg-gradient-to-tr from-[#feda75] via-[#d62976] to-[#4f5bd5]',
  },
  { name: 'WhatsApp', slug: 'whatsapp', href: 'https://wa.me/447537132205', bg_class: 'bg-[#25D366]' },
  { name: 'LinkedIn', slug: 'linkedin', href: 'https://linkedin.com/', bg_class: 'bg-[#0A66C2]' },
  { name: 'YouTube', slug: 'youtube', href: 'https://youtube.com/', bg_class: 'bg-[#FF0033]' },
  { name: 'WeChat', slug: 'wechat', href: '#', bg_class: 'bg-[#07C160]' },
]

const defaultQuickReplies = ['Get a quote', 'Book a call', 'WhatsApp', 'WeChat']

function pick(value, fallback) {
  return (value ?? '').toString().trim() !== '' ? value : fallback
}

const p = computed(() => props.contactPage ?? {})

const heroHeading = computed(() => pick(p.value.hero_heading, 'Contact Creafynest'))
const heroSubtitle = computed(() =>
  pick(
    p.value.hero_subtitle,
    "Get in touch with our team — we'd love to hear about your project, timeline, and goals.",
  ),
)

const methodsEyebrow = computed(() => pick(p.value.methods_eyebrow, ''))
const methodsHeading = computed(() => pick(p.value.methods_heading, 'Choose Your Preferred Contact Method'))
const methodsDescription = computed(() =>
  pick(p.value.methods_description, "We're here to help with your project, timeline, and goals."),
)

const chatCardActive = computed(() => p.value.chat_card_is_active !== false)
const chatBadgeLabel = computed(() => pick(p.value.chat_badge_label, 'Live Chat'))
const chatCardHeading = computed(() => pick(p.value.chat_card_heading, 'Chat with Us Now'))
const chatCardDescription = computed(() =>
  pick(p.value.chat_card_description, 'Get instant answers to your questions through our live chat.'),
)
const chatCardLabel = computed(() => pick(p.value.chat_card_button_label, 'Open Live Chat'))
const chatCardHref = computed(() => pick(p.value.chat_card_button_href, '#contact-channels'))
const chatTeamName = computed(() => pick(p.value.chat_team_name, 'Creafynest Team'))
const chatStatusText = computed(() => pick(p.value.chat_status_text, 'Online · Replies fast'))
const chatFooterNote = computed(() =>
  pick(p.value.chat_footer_note, 'Real humans · Replies usually within minutes'),
)
const chatGreeting = computed(() =>
  pick(p.value.chat_greeting, 'Hi! How can we help with your project today?'),
)
const chatInputPlaceholder = computed(() => pick(p.value.chat_input_placeholder, 'Type a message…'))
const chatQuickReplies = computed(() => {
  const list = p.value.chat_quick_replies ?? []
  return list.length ? list : defaultQuickReplies
})
const chatQuickReplyHref = computed(() => pick(p.value.cta_secondary_href, '#contact-channels'))

const infoActive = computed(() => !!p.value.info_is_active)
const infoHeading = computed(() => pick(p.value.info_heading, 'Contact information'))
const infoDescription = computed(() => pick(p.value.info_description, ''))
const contactEmail = computed(() => pick(p.value.contact_email, ''))
const contactPhone = computed(() => pick(p.value.contact_phone, ''))
const contactAddress = computed(() => pick(p.value.contact_address, ''))
const officeHours = computed(() => pick(p.value.office_hours, ''))

const formActive = computed(() => !!p.value.form_is_active)
const formHeading = computed(() => pick(p.value.form_heading, 'Send us a message'))
const formSubtitle = computed(() => pick(p.value.form_subtitle, ''))
const formSettings = computed(() => ({
  nameLabel: pick(p.value.form_name_label, 'Name'),
  emailLabel: pick(p.value.form_email_label, 'Email'),
  messageLabel: pick(p.value.form_message_label, 'Message'),
  submitLabel: pick(p.value.form_submit_label, 'Send message'),
  successMessage: pick(p.value.form_success_message, 'Thanks! Your message has been sent.'),
  messageMaxLength: p.value.form_message_max_length || 5000,
}))

const mapActive = computed(() => !!p.value.map_is_active && !!pick(p.value.map_embed_url, ''))
const mapHeading = computed(() => pick(p.value.map_heading, 'Find us'))
const mapEmbedUrl = computed(() => pick(p.value.map_embed_url, ''))
const mapAddressLabel = computed(() => pick(p.value.map_address_label, ''))

const ctaActive = computed(() => p.value.cta_is_active !== false)
const ctaEyebrow = computed(() => pick(p.value.cta_eyebrow, "Let's build together"))
const ctaHeading = computed(() => pick(p.value.cta_heading, 'Ready to Get Started?'))
const ctaDescription = computed(() =>
  pick(
    p.value.cta_description,
    'Tell us about your project, timeline, and goals — our team will get back to you within one business day.',
  ),
)
const ctaLabel = computed(() => pick(p.value.cta_button_label, 'Get Started'))
const ctaHref = computed(() => pick(p.value.cta_button_href, '#contact-channels'))
const ctaSecondaryLabel = computed(() => pick(p.value.cta_secondary_label, 'Chat with us'))
const ctaSecondaryHref = computed(() => pick(p.value.cta_secondary_href, '#contact-channels'))
const ctaResponseNote = computed(() => pick(p.value.cta_response_note, 'Average response within 24 hours'))
const ctaTrustedLabel = computed(() => pick(p.value.cta_trusted_label, 'Trusted partner'))
const ctaQuickLabel = computed(() => pick(p.value.cta_quick_label, 'Quick reply'))

const socialActive = computed(() => p.value.social_is_active !== false)
const socialHeading = computed(() => pick(p.value.social_heading, 'Follow us for the latest updates'))

const messagingChannels = computed(() => {
  const list = p.value.messaging_channels ?? []
  return list.length ? list : defaultMessagingChannels
})

const socialLinks = computed(() => {
  const list = p.value.social_links ?? []
  return list.length ? list : defaultSocialLinks
})

function qrSrc(data) {
  return `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=4&data=${encodeURIComponent(data || '')}`
}

function brandIcon(slug) {
  return simpleIconSvgUrl(slug)
}
</script>

<template>
  <div>
    <section
      class="relative overflow-hidden bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] py-16 text-white sm:py-20 md:py-24"
      aria-label="Contact hero"
    >
      <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_70%_55%_at_50%_-10%,rgba(99,102,241,0.35),transparent)]"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:linear-gradient(to_bottom,black,transparent)]"
        aria-hidden="true"
      />
      <Container class="relative text-center">
        <h1 class="font-sans text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-[3.25rem]">
          {{ heroHeading }}
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-200/90 sm:text-base">
          {{ heroSubtitle }}
        </p>
      </Container>
    </section>

    <section class="bg-slate-50/60 py-16 sm:py-20" aria-labelledby="contact-direct-heading">
      <Container>
        <div class="mx-auto grid max-w-5xl gap-10 md:grid-cols-[0.9fr_1.1fr] md:items-start">
          <div class="pt-2">
            <h2
              id="contact-direct-heading"
              class="font-sans text-2xl font-bold tracking-tight text-[#0f2a52] sm:text-3xl"
            >
              Reach out directly
            </h2>
            <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600 sm:text-base">
              Send us a message by email or connect instantly on WhatsApp. We usually reply within a few hours.
            </p>

            <div class="mt-6 space-y-4">
              <a
                href="mailto:Creafynest@gmail.com"
                class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
              >
                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#eef2ff] text-[#2d3fa3]">
                    <Mail class="h-5 w-5" />
                  </span>
                  <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Email</p>
                    <p class="mt-1 text-sm font-semibold text-[#0f2a52]">Creafynest@gmail.com</p>
                  </div>
                </div>
                <ArrowRight class="h-4 w-4 text-slate-500 transition group-hover:translate-x-0.5" />
              </a>

              <a
                href="https://wa.me/923093788999"
                target="_blank"
                rel="noopener noreferrer"
                class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
              >
                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#eafaf2] text-[#0f9d58]">
                    <MessageCircle class="h-5 w-5" />
                  </span>
                  <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">WhatsApp</p>
                    <p class="mt-1 text-sm font-semibold text-[#0f2a52]">+923 093 788 999</p>
                  </div>
                </div>
                <ArrowRight class="h-4 w-4 text-slate-500 transition group-hover:translate-x-0.5" />
              </a>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_20px_50px_-25px_rgba(15,42,82,0.25)]">
            <ContactForm :settings="formSettings" />
          </div>
        </div>
      </Container>
    </section>

    <section
      v-if="infoActive"
      class="border-t border-slate-200/80 bg-white py-16 sm:py-20"
      aria-labelledby="contact-info-heading"
    >
      <Container>
        <div class="mx-auto max-w-2xl text-center">
          <h2 id="contact-info-heading" class="font-sans text-2xl font-bold tracking-tight text-[#0f2a52] sm:text-3xl">
            {{ infoHeading }}
          </h2>
          <p v-if="infoDescription" class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
            {{ infoDescription }}
          </p>
        </div>
        <ul class="mx-auto mt-10 grid max-w-3xl gap-6 sm:grid-cols-2">
          <li v-if="contactEmail" class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#0f2a52]/10 text-[#0f2a52]">
              <Mail class="h-5 w-5" />
            </span>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</p>
              <a :href="`mailto:${contactEmail}`" class="mt-1 block text-sm font-semibold text-[#0f2a52] hover:underline">
                {{ contactEmail }}
              </a>
            </div>
          </li>
          <li v-if="contactPhone" class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#0f2a52]/10 text-[#0f2a52]">
              <Phone class="h-5 w-5" />
            </span>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Phone</p>
              <a :href="`tel:${contactPhone.replace(/\s+/g, '')}`" class="mt-1 block text-sm font-semibold text-[#0f2a52] hover:underline">
                {{ contactPhone }}
              </a>
            </div>
          </li>
          <li v-if="contactAddress" class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:col-span-2">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#0f2a52]/10 text-[#0f2a52]">
              <MapPin class="h-5 w-5" />
            </span>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Address</p>
              <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ contactAddress }}</p>
            </div>
          </li>
          <li v-if="officeHours" class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-5 sm:col-span-2">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#0f2a52]/10 text-[#0f2a52]">
              <Clock class="h-5 w-5" />
            </span>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Office hours</p>
              <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ officeHours }}</p>
            </div>
          </li>
        </ul>
      </Container>
    </section>

    <!-- <section v-if="formActive" class="bg-slate-50/60 py-16 sm:py-20" aria-labelledby="contact-form-heading">
      <Container>
        <div class="mx-auto max-w-2xl text-center">
          <h2 id="contact-form-heading" class="font-sans text-2xl font-bold tracking-tight text-[#0f2a52] sm:text-3xl">
            {{ formHeading }}
          </h2>
          <p v-if="formSubtitle" class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
            {{ formSubtitle }}
          </p>
        </div>
        <div class="mx-auto mt-10 max-w-xl">
          <ContactForm :settings="formSettings" />
        </div>
      </Container>
    </section> -->

    <!-- <section v-if="mapActive" class="border-t border-slate-200/80 bg-white py-16 sm:py-20" aria-labelledby="contact-map-heading">
      <Container>
        <div class="mx-auto max-w-2xl text-center">
          <h2 id="contact-map-heading" class="font-sans text-2xl font-bold tracking-tight text-[#0f2a52] sm:text-3xl">
            {{ mapHeading }}
          </h2>
          <p v-if="mapAddressLabel" class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
            {{ mapAddressLabel }}
          </p>
        </div>
        <div class="mx-auto mt-10 max-w-5xl overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
          <iframe
            :src="mapEmbedUrl"
            title="Location map"
            class="aspect-[16/9] w-full border-0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen
          />
        </div>
      </Container>
    </section> -->

    <section v-if="ctaActive" class="pb-16" aria-labelledby="contact-cta-heading">
      <Container>
        <div class="relative overflow-hidden rounded-3xl bg-[#0b2347] shadow-[0_30px_80px_-30px_rgba(15,42,82,0.6)]">
          <div
            class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347]"
            aria-hidden="true"
          />
          <div
            class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[radial-gradient(circle,rgba(225,29,42,0.32),transparent_70%)] blur-2xl"
            aria-hidden="true"
          />
          <div
            class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(99,102,241,0.38),transparent_70%)] blur-2xl"
            aria-hidden="true"
          />
          <div
            class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:radial-gradient(ellipse_70%_60%_at_50%_50%,black,transparent_85%)]"
            aria-hidden="true"
          />

          <div
            class="relative grid grid-cols-1 gap-10 px-6 py-12 md:grid-cols-[1.4fr_1fr] md:items-center md:px-12 md:py-14"
          >
            <div class="text-white">
              <span
                v-if="ctaEyebrow"
                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/80 ring-1 ring-white/15 backdrop-blur-sm"
              >
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true" />
                {{ ctaEyebrow }}
              </span>
              <h3
                id="contact-cta-heading"
                class="mt-5 text-3xl font-bold leading-tight tracking-tight sm:text-4xl"
              >
                {{ ctaHeading }}
              </h3>
              <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-200/90 sm:text-base">
                {{ ctaDescription }}
              </p>

              <div class="mt-7 flex flex-wrap items-center gap-3">
                <a
                  :href="ctaHref"
                  class="group inline-flex items-center justify-center gap-2 rounded-lg bg-[#e11d2a] px-7 py-3 text-sm font-semibold text-white shadow-[0_10px_30px_-10px_rgba(225,29,42,0.7)] transition hover:bg-[#c81825] focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2a52]"
                >
                  {{ ctaLabel }}
                  <ArrowRight class="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
                <a
                  v-if="ctaSecondaryLabel"
                  :href="ctaSecondaryHref"
                  class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:border-white/60 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0f2a52]"
                >
                  <MessageCircle class="h-4 w-4" />
                  {{ ctaSecondaryLabel }}
                </a>
              </div>

              <p v-if="ctaResponseNote" class="mt-6 inline-flex items-center gap-2 text-xs text-slate-300/80">
                <Clock class="h-3.5 w-3.5" aria-hidden="true" />
                {{ ctaResponseNote }}
              </p>
            </div>

            <div class="relative hidden md:block" aria-hidden="true">
              <div class="relative mx-auto aspect-square w-full max-w-sm">
                <div
                  class="absolute inset-0 rounded-full bg-gradient-to-tr from-[#1d4ed8]/45 via-[#7c3aed]/30 to-[#e11d2a]/25 blur-2xl"
                />
                <div class="absolute inset-4 rounded-full border border-white/15" />
                <div class="absolute inset-12 rounded-full border border-white/10" />
                <div class="absolute inset-20 rounded-full border border-white/5" />

                <div
                  class="absolute left-1/2 top-1/2 flex h-20 w-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/25 backdrop-blur-md"
                >
                  <Sparkles class="h-9 w-9 text-white" />
                </div>

                <div
                  v-if="ctaTrustedLabel"
                  class="absolute right-0 top-4 flex items-center gap-2 rounded-xl bg-white px-3 py-2 text-xs font-semibold text-[#0f2a52] shadow-lg ring-1 ring-black/5"
                >
                  <Star class="h-3.5 w-3.5 fill-amber-400 text-amber-400" />
                  {{ ctaTrustedLabel }}
                </div>

                <div
                  v-if="ctaQuickLabel"
                  class="absolute bottom-4 left-0 flex items-center gap-2 rounded-xl bg-emerald-500 px-3 py-2 text-xs font-semibold text-white shadow-lg ring-1 ring-emerald-600/40"
                >
                  <Zap class="h-3.5 w-3.5" />
                  {{ ctaQuickLabel }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>

    <section
      v-if="socialActive && socialLinks.length"
      class="bg-slate-50/60 py-12 sm:py-16"
      aria-labelledby="contact-social-heading"
    >
      <Container>
        <div class="text-center">
          <h3 id="contact-social-heading" class="text-lg font-bold tracking-tight text-[#0f2a52] sm:text-xl">
            {{ socialHeading }}
          </h3>
          <ul class="mt-6 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
            <li v-for="social in socialLinks" :key="social.name + (social.href || '')">
              <a
                :href="social.href || '#'"
                target="_blank"
                rel="noopener noreferrer"
                :aria-label="`Follow us on ${social.name}`"
                :class="[
                  'inline-flex h-11 w-11 items-center justify-center rounded-full text-white shadow-sm transition hover:scale-105 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#0f2a52]',
                  social.bg_class || 'bg-slate-700',
                ]"
              >
                <img
                  v-if="social.slug"
                  :src="brandIcon(social.slug)"
                  :alt="`${social.name} icon`"
                  class="h-5 w-5 brightness-0 invert"
                  loading="lazy"
                >
              </a>
            </li>
          </ul>
        </div>
      </Container>
    </section>
  </div>
</template>
