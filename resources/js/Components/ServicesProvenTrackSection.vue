<script setup>
import Container from './Ui/Container.vue'
</script>

<template>
  <section
    class="relative isolate overflow-hidden bg-[#060d18] py-16 sm:py-20 lg:py-24"
    aria-labelledby="proven-track-heading"
  >
    <!-- Navy atmosphere: darker edges, softer center -->
    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_120%_80%_at_50%_45%,rgba(30,58,95,0.55)_0%,rgba(10,22,40,0.92)_45%,#030712_100%)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 opacity-[0.09]"
      style="background-image: linear-gradient(rgba(148,163,184,0.35) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,0.35) 1px, transparent 1px); background-size: 56px 56px;"
      aria-hidden="true"
    />

    <Container class="relative z-[1]">
      <div class="relative grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0 lg:items-center">
        <!-- Glowing center rule (desktop) -->
        <div
          class="pointer-events-none absolute left-1/2 top-8 bottom-8 hidden w-px -translate-x-1/2 lg:block"
          aria-hidden="true"
        >
          <div
            class="h-full w-full rounded-full bg-gradient-to-b from-transparent via-sky-400 to-transparent opacity-95 shadow-[0_0_18px_5px_rgba(56,189,248,0.42),0_0_42px_8px_rgba(56,189,248,0.18)]"
          />
        </div>

        <!-- Left column -->
        <div class="max-w-xl lg:pr-10 xl:pr-14">
          <h2
            id="proven-track-heading"
            class="font-sans text-2xl font-bold leading-tight tracking-tight text-white sm:text-3xl lg:text-[1.85rem] xl:text-4xl"
          >
            We’ve got a proven track record in AI / ML
          </h2>
          <p class="mt-5 text-sm leading-relaxed text-white/75 sm:text-base">
            Partner with Azee for an exceptional team with deep technical expertise, flexibility, and a proven track
            record building AI-powered applications.
          </p>
        </div>

        <!-- Mobile / tablet divider -->
        <div
          class="h-px w-full bg-gradient-to-r from-transparent via-sky-400/90 to-transparent shadow-[0_0_16px_3px_rgba(56,189,248,0.35)] lg:hidden"
          aria-hidden="true"
        />

        <!-- Right column -->
        <div class="flex gap-6 sm:gap-8 lg:pl-10 xl:pl-14">
          <div class="shrink-0 pt-1" aria-hidden="true">
            <svg
              class="h-[4.5rem] w-[4.5rem] text-white sm:h-[5.25rem] sm:w-[5.25rem]"
              viewBox="0 0 100 100"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <circle
                cx="50"
                cy="50"
                r="42"
                stroke="currentColor"
                stroke-opacity="0.85"
                stroke-width="1"
              />
              <g transform="translate(50 50)">
                <rect
                  x="-22"
                  y="-22"
                  width="44"
                  height="44"
                  rx="2"
                  transform="rotate(-18)"
                  stroke="currentColor"
                  stroke-opacity="0.9"
                  stroke-width="1"
                />
                <rect
                  x="-18"
                  y="-18"
                  width="36"
                  height="36"
                  rx="2"
                  transform="rotate(12)"
                  stroke="currentColor"
                  stroke-opacity="0.85"
                  stroke-width="1"
                  stroke-dasharray="5 4"
                />
                <rect
                  x="-14"
                  y="-14"
                  width="28"
                  height="28"
                  rx="1.5"
                  transform="rotate(-8)"
                  stroke="currentColor"
                  stroke-opacity="0.8"
                  stroke-width="1"
                  stroke-dasharray="2 5"
                />
                <rect x="-6" y="-6" width="12" height="12" rx="1" fill="currentColor" />
              </g>
            </svg>
          </div>

          <div class="min-w-0 flex-1">
            <h3 class="font-sans text-2xl font-bold leading-tight tracking-tight text-white sm:text-3xl lg:text-[1.85rem] xl:text-4xl">
              Python engineering
            </h3>
            <p class="mt-5 text-sm leading-relaxed text-white/75 sm:text-base">
              Our team transforms objectives into solutions—with agility and clear communication.
            </p>
          </div>
        </div>
      </div>
    </Container>
  </section>
</template>
