<script setup>
defineProps({
  src: { type: String, required: true },
  webpSrc: { type: String, default: '' },
  alt: { type: String, default: '' },
  lazy: { type: Boolean, default: true },
  highPriority: { type: Boolean, default: false },
  imgClass: { type: String, default: '' },
})
</script>

<template>
  <picture v-if="webpSrc">
    <source type="image/webp" :srcset="webpSrc">
    <img
      :src="src"
      :alt="alt"
      :class="imgClass"
      :loading="highPriority ? 'eager' : lazy ? 'lazy' : 'eager'"
      :fetchpriority="highPriority ? 'high' : undefined"
      decoding="async"
    >
  </picture>
  <img
    v-else
    :src="src"
    :alt="alt"
    :class="imgClass"
    :loading="highPriority ? 'eager' : lazy ? 'lazy' : 'eager'"
    :fetchpriority="highPriority ? 'high' : undefined"
    decoding="async"
  >
</template>
