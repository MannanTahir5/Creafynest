<script setup>
import SeoHead from '../Components/SeoHead.vue'
</script>

<template>
  <div class="admin-shell min-h-screen bg-[#f4f6fb]">
    <SeoHead />
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
      <div class="w-full max-w-md">
        <slot />
      </div>
    </div>
  </div>
</template>
