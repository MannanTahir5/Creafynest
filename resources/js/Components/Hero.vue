<script setup>
import Container from './Ui/Container.vue'

defineProps({
  /** Wider headline column on large screens (e.g. home). */
  wide: { type: Boolean, default: false },
})
</script>

<template>
  <section
    class="relative overflow-hidden border-b border-slate-200/70 bg-gradient-to-b from-slate-50 via-white to-white py-20 sm:py-24 lg:py-28 dark:border-slate-800/80 dark:from-slate-950 dark:via-slate-950 dark:to-slate-950"
  >
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,rgba(124,58,237,0.11),transparent)] dark:bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,rgba(124,58,237,0.14),transparent)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgb(148_163_184/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.06)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:linear-gradient(to_bottom,black,transparent)] dark:bg-[linear-gradient(to_right,rgb(148_163_184/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.05)_1px,transparent_1px)]"
      aria-hidden="true"
    />

    <Container class="relative">
      <div :class="wide ? 'max-w-4xl' : 'max-w-3xl'">
        <slot />
      </div>
    </Container>
  </section>
</template>
