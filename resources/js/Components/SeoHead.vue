<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const seo = computed(() => page.props.seo ?? {})
const title = computed(() => seo.value.title ?? page.props.site?.name ?? 'Azee')
const description = computed(() => seo.value.description ?? '')
const canonical = computed(() => seo.value.canonical ?? '')
const ogType = computed(() => seo.value.og_type ?? 'website')
const image = computed(() => seo.value.image ?? null)
const robots = computed(() => seo.value.robots ?? null)
const keywords = computed(() => seo.value.keywords ?? null)
const ogTitle = computed(() => seo.value.og_title ?? title.value)
const ogDescription = computed(() => seo.value.og_description ?? description.value)
const twitterCard = computed(() => (image.value ? 'summary_large_image' : 'summary'))
const ogLocale = computed(() => seo.value.og_locale ?? null)
const ogImageAlt = computed(() => seo.value.og_image_alt ?? null)
const twitterSite = computed(() => seo.value.twitter_site ?? null)
const twitterCreator = computed(() => seo.value.twitter_creator ?? null)
const themeColor = computed(() => seo.value.theme_color ?? null)
const articlePublished = computed(() => seo.value.article_published_time ?? null)
const articleModified = computed(() => seo.value.article_modified_time ?? null)
const articleSection = computed(() => seo.value.article_section ?? null)

const twitterSiteAttr = computed(() => (twitterSite.value ? `@${twitterSite.value}` : null))
const twitterCreatorAttr = computed(() => (twitterCreator.value ? `@${twitterCreator.value}` : null))
</script>

<template>
  <Head :title="title">
    <meta v-if="description" head-key="meta-description" name="description" :content="description">
    <meta v-if="keywords" head-key="meta-keywords" name="keywords" :content="keywords">
    <link v-if="canonical" head-key="canonical" rel="canonical" :href="canonical">
    <meta v-if="robots" head-key="robots" name="robots" :content="robots">

    <meta head-key="og-type" property="og:type" :content="ogType">
    <meta head-key="og-title" property="og:title" :content="ogTitle">
    <meta v-if="ogDescription" head-key="og-description" property="og:description" :content="ogDescription">
    <meta v-if="canonical" head-key="og-url" property="og:url" :content="canonical">
    <meta v-if="image" head-key="og-image" property="og:image" :content="image">
    <meta v-if="image && ogImageAlt" head-key="og-image-alt" property="og:image:alt" :content="ogImageAlt">
    <meta v-if="ogLocale" head-key="og-locale" property="og:locale" :content="ogLocale">

    <meta head-key="tw-card" name="twitter:card" :content="twitterCard">
    <meta head-key="tw-title" name="twitter:title" :content="ogTitle">
    <meta v-if="twitterSiteAttr" head-key="tw-site" name="twitter:site" :content="twitterSiteAttr">
    <meta v-if="twitterCreatorAttr" head-key="tw-creator" name="twitter:creator" :content="twitterCreatorAttr">
    <meta v-if="ogDescription" head-key="tw-description" name="twitter:description" :content="ogDescription">
    <meta v-if="image" head-key="tw-image" name="twitter:image" :content="image">

    <meta v-if="articlePublished" property="article:published_time" :content="articlePublished">
    <meta v-if="articleModified" property="article:modified_time" :content="articleModified">
    <meta v-if="articleSection" property="article:section" :content="articleSection">

    <meta v-if="themeColor" name="theme-color" :content="themeColor">
  </Head>
</template>
