<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ContactNewsletterBand from '../Components/ContactNewsletterBand.vue'

const page = usePage()

const defaults = {
  heading: 'Newsletter',
  subtitle: 'Get Global Updates. Subscribe Now.',
  placeholder: 'Enter your email',
  buttonLabel: 'Submit',
}

const bandProps = computed(() => {
  const cn = page.props?.contactNewsletter
  if (cn && typeof cn === 'object') {
    return {
      heading: cn.heading || defaults.heading,
      subtitle: cn.subtitle || defaults.subtitle,
      placeholder: cn.placeholder || defaults.placeholder,
      buttonLabel: cn.buttonLabel || defaults.buttonLabel,
    }
  }
  return defaults
})
</script>

<template>
  <ContactNewsletterBand v-bind="bandProps" />
</template>
