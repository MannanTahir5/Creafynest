<script setup>
import Container from '../../Components/Ui/Container.vue'
import Button from '../../Components/Ui/Button.vue'
</script>

<template>
  <section class="py-20">
    <Container>
      <p class="text-sm font-semibold tracking-wide text-slate-500">404</p>
      <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
        Page not found
      </h1>
      <p class="mt-3 max-w-xl text-base leading-relaxed text-slate-600">
        The page you’re looking for doesn’t exist or may have moved.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        <Button href="/">Go home</Button>
        <Button href="/contact" variant="secondary">Contact</Button>
      </div>
    </Container>
  </section>
</template>

