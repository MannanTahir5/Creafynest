<script setup>
import SiteNavbar from '../Shared/SiteNavbar.vue'
import SiteFooter from '../Shared/SiteFooter.vue'
import SeoHead from '../Components/SeoHead.vue'
import JsonLd from '../Components/JsonLd.vue'
</script>

<template>
  <div class="min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <SeoHead />
    <JsonLd />

    <SiteNavbar />

    <main class="flex-1">
      <slot />
    </main>

    <SiteFooter />
  </div>
</template>

