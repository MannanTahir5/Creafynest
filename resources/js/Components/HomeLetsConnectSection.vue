<script setup>
import { computed, nextTick, ref } from 'vue'
import { Sparkles } from 'lucide-vue-next'
import { useForm, usePage } from '@inertiajs/vue3'
import Container from './Ui/Container.vue'

const page = usePage()
const flashSuccess = computed(() => page.props?.flash?.success)

const props = defineProps({
  section: { type: Object, default: null },
})

const sectionEyebrow = computed(() => props.section?.eyebrow || 'Get in touch')
const sectionHeading = computed(() => props.section?.heading || "Let's connect")
const sectionDescription = computed(() => props.section?.description || "We're just a message away. Contact us now and let's make something great together!")
const submitLabel = computed(() => props.section?.submit_label || "Let's work together")

const defaultTimelineOptions = [
  { id: 'short', label: 'Short: up to 3 months' },
  { id: 'medium', label: 'Medium: 3–9 months' },
  { id: 'long', label: 'Long: 9+ months' },
  { id: 'unsure', label: 'Not sure' },
]

const defaultServiceOptions = [
  { id: 'ai', label: 'AI based' },
  { id: 'web', label: 'Web development' },
  { id: 'mobile', label: 'Mobile apps' },
  { id: 'design', label: 'Web designs' },
  { id: 'branding', label: 'Logos & branding' },
  { id: 'cms', label: 'WordPress & Shopify' },
]

const timelineOptions = computed(() => {
  const opts = props.section?.timeline_options
  return Array.isArray(opts) && opts.length ? opts : defaultTimelineOptions
})

const serviceOptions = computed(() => {
  const opts = props.section?.service_options
  return Array.isArray(opts) && opts.length ? opts : defaultServiceOptions
})

const howFound = ref('')
const timeline = ref('short')
const serviceType = ref('ai')
const idea = ref('')
const localIdeaError = ref('')
const justSent = ref(false)

const form = useForm({
  name: '',
  email: '',
  message: '',
})

function timelineLabel(id) {
  return timelineOptions.value.find((o) => o.id === id)?.label ?? id
}

function serviceLabel(id) {
  return serviceOptions.value.find((o) => o.id === id)?.label ?? id
}

function composeMessage() {
  const lines = [
    `How did you find us: ${howFound.value.trim() || '—'}`,
    `Expected timeline: ${timelineLabel(timeline.value)}`,
    `Type of services: ${serviceLabel(serviceType.value)}`,
    '',
    idea.value.trim(),
  ]
  return lines.join('\n')
}

const nameErrId = 'home-connect-error-name'
const emailErrId = 'home-connect-error-email'
const messageErrId = 'home-connect-error-message'

function scrollToFirstError() {
  nextTick(() => {
    for (const id of ['home-connect-name', 'home-connect-email', 'home-connect-idea']) {
      const el = document.getElementById(id)
      if (el?.getAttribute('aria-invalid') === 'true') {
        el.focus()
        el.scrollIntoView({ block: 'center', behavior: 'smooth' })
        break
      }
    }
  })
}

function submit() {
  justSent.value = false
  localIdeaError.value = ''
  if (!idea.value.trim()) {
    localIdeaError.value = 'Please tell us more about your idea.'
    nextTick(() => {
      document.getElementById('home-connect-idea')?.focus()
      document.getElementById('home-connect-idea')?.scrollIntoView({ block: 'center', behavior: 'smooth' })
    })
    return
  }

  form.message = composeMessage()
  form.post('/contact', {
    preserveScroll: true,
    onSuccess: () => {
      justSent.value = true
      form.reset()
      howFound.value = ''
      timeline.value = 'short'
      serviceType.value = 'ai'
      idea.value = ''
    },
    onError: () => scrollToFirstError(),
  })
}

const hasFieldErrors = computed(() => form.hasErrors)
const primary = 'bg-[#4D73FF] text-white border-[#4D73FF] shadow-sm hover:bg-[#3d62ee] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4D73FF]/50 focus-visible:ring-offset-2'
const pillInactive =
  'border border-slate-200 bg-white text-[#4D73FF] hover:border-[#4D73FF]/40 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-900 dark:text-blue-300 dark:hover:border-blue-400/40'
const pillActive = `${primary} dark:ring-offset-slate-900`
</script>

<template>
  <section
    class="bg-[#f4f5f7] py-14 dark:bg-slate-950 sm:py-16 md:py-20"
    aria-labelledby="lets-connect-heading"
  >
    <Container class="max-w-3xl">
      <div class="text-center">
        <div
          class="mx-auto inline-flex items-center gap-2 rounded-full bg-[#0B0E1E] px-4 py-2 text-xs font-semibold tracking-wide text-white shadow-sm dark:bg-slate-900"
        >
          <Sparkles class="h-3.5 w-3.5 shrink-0 text-white" stroke-width="2" aria-hidden="true" />
          {{ sectionEyebrow }}
        </div>

        <h2
          id="lets-connect-heading"
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white"
        >
          {{ sectionHeading }}
        </h2>

        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-slate-400">
          {{ sectionDescription }}
        </p>
      </div>

      <div
        class="mt-10 rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:mt-12 sm:p-8 md:p-10"
      >
        <div
          v-if="flashSuccess || justSent"
          class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200"
          role="status"
        >
          {{ flashSuccess || 'Thanks! Your message has been sent.' }}
        </div>

        <div
          v-if="hasFieldErrors"
          class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200"
          role="alert"
          tabindex="-1"
        >
          <p class="font-semibold">There was a problem with your submission.</p>
          <p class="mt-1 text-rose-800 dark:text-rose-300">Please correct the fields below and try again.</p>
        </div>

        <form class="space-y-8" novalidate @submit.prevent="submit">
          <div class="grid gap-6 sm:grid-cols-2">
            <div>
              <label for="home-connect-name" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
                Your name
              </label>
              <input
                id="home-connect-name"
                v-model="form.name"
                type="text"
                name="name"
                placeholder="Enter your name"
                class="form-input mt-2 rounded-lg border-slate-200 focus-visible:border-[#4D73FF]/60 focus-visible:ring-[#4D73FF]/35 dark:border-slate-600"
                :class="{ 'form-input-invalid': !!form.errors.name }"
                :aria-invalid="form.errors.name ? 'true' : 'false'"
                :aria-describedby="form.errors.name ? nameErrId : undefined"
                autocomplete="name"
                maxlength="120"
                required
              >
              <p v-if="form.errors.name" :id="nameErrId" class="mt-1 text-xs text-rose-600 dark:text-rose-400" role="alert">
                {{ form.errors.name }}
              </p>
            </div>
            <div>
              <label for="home-connect-email" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
                Email
              </label>
              <input
                id="home-connect-email"
                v-model="form.email"
                type="email"
                name="email"
                placeholder="Your email"
                class="form-input mt-2 rounded-lg border-slate-200 focus-visible:border-[#4D73FF]/60 focus-visible:ring-[#4D73FF]/35 dark:border-slate-600"
                :class="{ 'form-input-invalid': !!form.errors.email }"
                :aria-invalid="form.errors.email ? 'true' : 'false'"
                :aria-describedby="form.errors.email ? emailErrId : undefined"
                autocomplete="email"
                required
              >
              <p v-if="form.errors.email" :id="emailErrId" class="mt-1 text-xs text-rose-600 dark:text-rose-400" role="alert">
                {{ form.errors.email }}
              </p>
            </div>
          </div>

          <div>
            <label for="home-connect-found" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
              How did you find us
            </label>
            <input
              id="home-connect-found"
              v-model="howFound"
              type="text"
              name="how_found"
              placeholder="How did you find us"
              class="form-input mt-2 rounded-lg border-slate-200 focus-visible:border-[#4D73FF]/60 focus-visible:ring-[#4D73FF]/35 dark:border-slate-600"
              maxlength="500"
              autocomplete="off"
            >
          </div>

          <div>
            <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
              Expected timelines
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
              <button
                v-for="opt in timelineOptions"
                :key="opt.id"
                type="button"
                class="rounded-full px-4 py-2.5 text-sm font-semibold transition"
                :class="timeline === opt.id ? pillActive : pillInactive"
                :aria-pressed="timeline === opt.id"
                @click="timeline = opt.id"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>

          <div>
            <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
              Type of services
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
              <button
                v-for="opt in serviceOptions"
                :key="opt.id"
                type="button"
                class="rounded-full px-4 py-2.5 text-sm font-semibold transition"
                :class="serviceType === opt.id ? pillActive : pillInactive"
                :aria-pressed="serviceType === opt.id"
                @click="serviceType = opt.id"
              >
                {{ opt.label }}
              </button>
            </div>
          </div>

          <div>
            <label for="home-connect-idea" class="block text-sm font-medium text-slate-800 dark:text-slate-200">
              Tell us more about your idea
            </label>
            <textarea
              id="home-connect-idea"
              v-model="idea"
              name="idea"
              rows="6"
              placeholder="Describe your goals, timeline, and anything else we should know."
              class="form-textarea mt-2 resize-y rounded-lg border-slate-200 focus-visible:border-[#4D73FF]/60 focus-visible:ring-[#4D73FF]/35 dark:border-slate-600"
              :class="{ 'form-input-invalid': !!localIdeaError || !!form.errors.message }"
              :aria-invalid="localIdeaError || form.errors.message ? 'true' : 'false'"
              :aria-describedby="localIdeaError || form.errors.message ? messageErrId : undefined"
              maxlength="4500"
              required
            />
            <p v-if="localIdeaError || form.errors.message" :id="messageErrId" class="mt-1 text-xs text-rose-600 dark:text-rose-400" role="alert">
              {{ localIdeaError || form.errors.message }}
            </p>
          </div>

          <div class="flex justify-start pt-1">
            <button
              type="submit"
              class="inline-flex min-h-[2.75rem] items-center justify-center rounded-lg px-8 py-3 text-sm font-bold text-white transition disabled:cursor-not-allowed disabled:opacity-60"
              :class="primary"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Sending…</span>
              <span v-else>{{ submitLabel }}</span>
            </button>
          </div>
        </form>
      </div>
    </Container>
  </section>
</template>
