<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

/** Page-only JSON-LD; site-wide WebSite/Organization is emitted server-side in app.blade.php */
const pieces = computed(() => page.props.schemas ?? [])
</script>

<template>
  <template v-for="(schema, i) in pieces" :key="i">
    <component :is="'script'" type="application/ld+json">{{ JSON.stringify(schema) }}</component>
  </template>
</template>
