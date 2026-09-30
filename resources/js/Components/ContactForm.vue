<script setup>
import { computed, nextTick, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import Button from './Ui/Button.vue'

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({
      nameLabel: 'Name',
      emailLabel: 'Email',
      messageLabel: 'Message',
      submitLabel: 'Send message',
      successMessage: 'Thanks! Your message has been sent.',
      messageMaxLength: 5000,
    }),
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props?.flash?.success)

const form = useForm({
  name: '',
  email: '',
  message: '',
})

const justSent = ref(false)

const nameErrId = 'contact-error-name'
const emailErrId = 'contact-error-email'
const messageErrId = 'contact-error-message'

const nameLabel = computed(() => props.settings.nameLabel || 'Name')
const emailLabel = computed(() => props.settings.emailLabel || 'Email')
const messageLabel = computed(() => props.settings.messageLabel || 'Message')
const submitLabel = computed(() => props.settings.submitLabel || 'Send message')
const successMessage = computed(() => props.settings.successMessage || 'Thanks! Your message has been sent.')
const messageMaxLength = computed(() => props.settings.messageMaxLength || 5000)

function scrollToFirstError() {
  nextTick(() => {
    for (const id of ['contact-name', 'contact-email', 'contact-message']) {
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

  const name = (form.name || '').trim()
  const email = (form.email || '').trim()
  const message = (form.message || '').trim()

  if (!name || !email || !message) {
    form.setError('name', !name ? 'The name field is required.' : '')
    form.setError('email', !email ? 'The email field is required.' : '')
    form.setError('message', !message ? 'The message field is required.' : '')
    scrollToFirstError()
    return
  }

  const subject = encodeURIComponent('New contact form message')
  const body = encodeURIComponent(
    `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}`,
  )

  window.location.href = `mailto:Creafynest@gmail.com?subject=${subject}&body=${body}`

  justSent.value = true
  form.reset()
}

const formErrorSummaryId = 'contact-form-errors'
const hasFieldErrors = computed(() => form.hasErrors)
</script>

<template>
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div
      v-if="flashSuccess || justSent"
      class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
      role="status"
    >
      {{ flashSuccess || successMessage }}
    </div>

    <div
      v-if="hasFieldErrors"
      :id="formErrorSummaryId"
      class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900"
      role="alert"
      tabindex="-1"
    >
      <p class="font-semibold">There was a problem with your submission.</p>
      <p class="mt-1 text-rose-800">Please correct the fields below and try again.</p>
    </div>

    <form class="space-y-4" novalidate @submit.prevent="submit">
      <div>
        <label for="contact-name" class="block text-sm font-medium text-slate-700">{{ nameLabel }}</label>
        <input
          id="contact-name"
          v-model="form.name"
          type="text"
          name="name"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.name }"
          :aria-invalid="form.errors.name ? 'true' : 'false'"
          :aria-describedby="form.errors.name ? nameErrId : undefined"
          autocomplete="name"
          maxlength="120"
          required
        >
        <p v-if="form.errors.name" :id="nameErrId" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.name }}</p>
      </div>

      <div>
        <label for="contact-email" class="block text-sm font-medium text-slate-700">{{ emailLabel }}</label>
        <input
          id="contact-email"
          v-model="form.email"
          type="email"
          name="email"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.email }"
          :aria-invalid="form.errors.email ? 'true' : 'false'"
          :aria-describedby="form.errors.email ? emailErrId : undefined"
          autocomplete="email"
          required
        >
        <p v-if="form.errors.email" :id="emailErrId" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.email }}</p>
      </div>

      <div>
        <label for="contact-message" class="block text-sm font-medium text-slate-700">{{ messageLabel }}</label>
        <textarea
          id="contact-message"
          v-model="form.message"
          name="message"
          rows="6"
          class="form-textarea resize-y"
          :class="{ 'form-input-invalid': !!form.errors.message }"
          :aria-invalid="form.errors.message ? 'true' : 'false'"
          :aria-describedby="form.errors.message ? messageErrId : undefined"
          :maxlength="messageMaxLength"
          required
        />
        <p v-if="form.errors.message" :id="messageErrId" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.message }}</p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <Button type="submit">
          <span v-if="form.processing">Sending…</span>
          <span v-else>{{ submitLabel }}</span>
        </Button>
      </div>
    </form>
  </div>
</template>
