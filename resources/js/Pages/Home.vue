<script setup>
import HomePremiumHero from '../Components/HomePremiumHero.vue'
import HomeServiceCategoryTitles from '../Components/HomeServiceCategoryTitles.vue'
import HomeCaseStudiesSection from '../Components/HomeCaseStudiesSection.vue'
import HomeIndustriesSection from '../Components/HomeIndustriesSection.vue'
import HomeTechnologiesSection from '../Components/HomeTechnologiesSection.vue'
import HomeTestimonialsSection from '../Components/HomeTestimonialsSection.vue'
import HomeLetsConnectSection from '../Components/HomeLetsConnectSection.vue'

defineProps({
  hero: { type: Object, default: null },
  deliveryCategories: { type: Array, default: () => [] },
  technologySection: { type: Object, default: null },
  industriesSection: { type: Object, default: null },
  connectSection: { type: Object, default: null },
  caseStudiesSection: { type: Object, default: null },
  testimonialsSection: { type: Object, default: null },
  featuredProjects: { type: Array, default: () => [] },
  testimonials: { type: Array, default: () => [] },
})
</script>

<template>
  <div>
    <HomePremiumHero :hero="hero" />

    <HomeServiceCategoryTitles :categories="deliveryCategories" />

    <HomeTechnologiesSection v-if="technologySection" :section="technologySection" />

    <HomeCaseStudiesSection
      v-if="caseStudiesSection && featuredProjects.length"
      :projects="featuredProjects"
      :section="caseStudiesSection"
    />

    <HomeIndustriesSection v-if="industriesSection" :section="industriesSection" />

    <HomeTestimonialsSection
      v-if="testimonialsSection && testimonials.length"
      :testimonials="testimonials"
      :section="testimonialsSection"
    />

    <HomeLetsConnectSection v-if="connectSection" :section="connectSection" />
  </div>
</template>
