<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  ArrowRight,
  Briefcase,
  FolderKanban,
  Layers,
  MessageSquareQuote,
  Newspaper,
  Phone,
  Settings,
  Sparkles,
} from 'lucide-vue-next'
import AdminPageHeader from '../../Components/Admin/AdminPageHeader.vue'

const props = defineProps({
  counts: { type: Object, required: true },
})

const stats = computed(() => [
  {
    label: 'Services',
    value: props.counts.services,
    href: '/admin/services',
    icon: Briefcase,
    tone: 'violet',
  },
  {
    label: 'Projects',
    value: props.counts.projects,
    href: '/admin/projects',
    icon: FolderKanban,
    tone: 'sky',
  },
  {
    label: 'Blog posts',
    value: props.counts.blogs,
    href: '/admin/blogs',
    icon: Newspaper,
    tone: 'emerald',
  },
  {
    label: 'Testimonials',
    value: props.counts.testimonials,
    href: '/admin/testimonials',
    icon: MessageSquareQuote,
    tone: 'amber',
  },
])

const quickLinks = [
  {
    title: 'Home page',
    description: 'Hero, sections, and homepage content',
    href: '/admin/home-page',
    icon: Layers,
  },
  {
    title: 'Contact page',
    description: 'Contact methods, form, map, and CTA',
    href: '/admin/contact-page',
    icon: Phone,
  },
  {
    title: 'Web settings',
    description: 'Branding, SEO, SMTP, and tracking',
    href: '/admin/web-settings',
    icon: Settings,
  },
]

const toneClasses = {
  violet: 'from-violet-500/10 to-violet-600/5 text-violet-700 ring-violet-200/60',
  sky: 'from-sky-500/10 to-sky-600/5 text-sky-700 ring-sky-200/60',
  emerald: 'from-emerald-500/10 to-emerald-600/5 text-emerald-700 ring-emerald-200/60',
  amber: 'from-amber-500/10 to-amber-600/5 text-amber-700 ring-amber-200/60',
}
</script>

<template>
  <div class="space-y-8">
    <div class="admin-card overflow-hidden">
      <div class="relative bg-gradient-to-br from-violet-600 via-violet-700 to-indigo-800 px-6 py-8 text-white sm:px-8 sm:py-10">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10 blur-2xl" />
        <div class="absolute -bottom-10 left-1/3 h-32 w-32 rounded-full bg-indigo-400/20 blur-2xl" />
        <div class="relative max-w-2xl">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
            <Sparkles class="h-3.5 w-3.5" />
            Welcome back
          </div>
          <h1 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">Manage your site from one place</h1>
          <p class="mt-3 text-sm leading-relaxed text-violet-100/90 sm:text-base">
            Update pages, services, projects, and global settings. Changes go live on the public site after you save.
          </p>
        </div>
      </div>
    </div>

    <AdminPageHeader
      title="Content overview"
      subtitle="Quick counts and shortcuts to the areas you edit most often."
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <Link
        v-for="stat in stats"
        :key="stat.label"
        :href="stat.href"
        class="admin-stat-card"
      >
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-sm font-medium text-slate-500">{{ stat.label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ stat.value }}</p>
          </div>
          <div
            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br ring-1"
            :class="toneClasses[stat.tone]"
          >
            <component :is="stat.icon" class="h-5 w-5" />
          </div>
        </div>
        <p class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-violet-600">
          Manage
          <ArrowRight class="h-4 w-4 transition group-hover:translate-x-0.5" />
        </p>
      </Link>
    </div>

    <AdminPageHeader title="Quick actions" subtitle="Jump straight into common editing tasks." />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
      <Link
        v-for="item in quickLinks"
        :key="item.href"
        :href="item.href"
        class="admin-card group flex items-start gap-4 p-5 transition hover:border-violet-200 hover:shadow-md"
      >
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 ring-1 ring-violet-100">
          <component :is="item.icon" class="h-5 w-5" />
        </div>
        <div class="min-w-0">
          <p class="font-semibold text-slate-900 group-hover:text-violet-700">{{ item.title }}</p>
          <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ item.description }}</p>
        </div>
        <ArrowRight class="ml-auto h-4 w-4 shrink-0 text-slate-300 transition group-hover:text-violet-500" />
      </Link>
    </div>
  </div>
</template>
