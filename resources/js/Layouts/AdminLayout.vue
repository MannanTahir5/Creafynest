<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import {
  LayoutDashboard,
  FolderKanban,
  Newspaper,
  Briefcase,
  MessageSquareQuote,
  LogOut,
  Bell,
  Package,
  Layers,
  Phone,
  Info,
  PanelBottom,
  Settings,
  Menu,
  X,
  UserCircle,
  ExternalLink,
  ChevronRight,
} from 'lucide-vue-next'
import AdminFlashAlert from '../Components/Admin/AdminFlashAlert.vue'

const page = usePage()

const navOpen = ref(false)
const dismissedFlash = ref({ success: false, error: false })

const navGroups = [
  {
    label: 'Overview',
    items: [{ href: '/admin', label: 'Dashboard', icon: LayoutDashboard }],
  },
  {
    label: 'Pages',
    items: [
      { href: '/admin/home-page', label: 'Home page', icon: Layers },
      { href: '/admin/about-page', label: 'About page', icon: Info },
      { href: '/admin/contact-page', label: 'Contact page', icon: Phone },
      { href: '/admin/footer', label: 'Footer', icon: PanelBottom },
    ],
  },
  {
    label: 'Content',
    items: [
      { href: '/admin/services', label: 'Services', icon: Briefcase },
      { href: '/admin/delivery-categories', label: 'Categories', icon: Package },
      { href: '/admin/projects', label: 'Projects', icon: FolderKanban },
      { href: '/admin/blogs', label: 'Blogs', icon: Newspaper },
      { href: '/admin/testimonials', label: 'Testimonials', icon: MessageSquareQuote },
    ],
  },
  {
    label: 'Settings',
    items: [
      { href: '/admin/web-settings', label: 'Web settings', icon: Settings },
      { href: '/admin/newsletter', label: 'Newsletter', icon: Bell },
      { href: '/admin/profile', label: 'Profile', icon: UserCircle },
    ],
  },
]

const flatNav = computed(() => navGroups.flatMap((g) => g.items))

const currentPath = computed(() => {
  const raw = page.url || ''
  if (!raw) return ''
  try {
    return new URL(raw, window.location.origin).pathname
  } catch {
    const q = raw.indexOf('?')
    return q === -1 ? raw : raw.slice(0, q)
  }
})

const activeItem = computed(() => {
  const path = (currentPath.value || '/').replace(/\/+$/, '') || '/'
  return (
    flatNav.value.find((item) => {
      const h = (item.href || '').replace(/\/+$/, '') || '/'
      if (h === '/admin') return path === '/admin'
      return path === h || path.startsWith(`${h}/`)
    }) ?? null
  )
})

const pageTitle = computed(() => activeItem.value?.label ?? 'Admin')

const siteName = computed(() => page.props?.branding?.site_name || 'Creafynest')
const userName = computed(() => page.props?.auth?.user?.name || 'Admin')
const userEmail = computed(() => page.props?.auth?.user?.email || '')
const userInitials = computed(() => {
  const parts = userName.value.trim().split(/\s+/).filter(Boolean)
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return (parts[0]?.[0] || 'A').toUpperCase()
})

const flashSuccess = computed(() => page.props?.flash?.success)
const flashError = computed(() => page.props?.flash?.error)

function isNavActive(href) {
  const path = (currentPath.value || '/').replace(/\/+$/, '') || '/'
  const h = (href || '').replace(/\/+$/, '') || '/'
  if (h === '/admin') return path === '/admin'
  return path === h || path.startsWith(`${h}/`)
}

function closeMobileNav() {
  navOpen.value = false
}

watch(navOpen, (open) => {
  if (typeof document === 'undefined') return
  document.body.style.overflow = open ? 'hidden' : ''
})

watch(
  () => page.url,
  () => {
    navOpen.value = false
    dismissedFlash.value = { success: false, error: false }
  },
)

function logout() {
  closeMobileNav()
  router.post('/logout')
}
</script>

<template>
  <div class="admin-shell min-h-screen bg-[#f4f6fb] text-slate-900">
    <Head>
      <title>{{ pageTitle }} · Admin</title>
      <meta name="robots" content="noindex, nofollow">
    </Head>

    <div
      v-show="navOpen"
      class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
      aria-hidden="true"
      @click="closeMobileNav"
    />

    <div class="flex min-h-screen">
      <!-- Sidebar -->
      <aside
        class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] shrink-0 flex-col border-r border-slate-800/50 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 transition-transform duration-300 ease-out lg:translate-x-0"
        :class="navOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
      >
        <div class="flex items-center justify-between gap-3 border-b border-white/5 px-5 py-5">
          <Link href="/admin" class="group flex min-w-0 items-center gap-3" @click="closeMobileNav">
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-600 text-sm font-bold text-white shadow-lg shadow-violet-900/40"
            >
              {{ siteName.charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="truncate text-sm font-bold text-white">{{ siteName }}</p>
              <p class="truncate text-xs text-slate-400">Admin panel</p>
            </div>
          </Link>
          <button
            type="button"
            class="rounded-xl p-2 text-slate-400 hover:bg-white/5 hover:text-white lg:hidden"
            aria-label="Close menu"
            @click="closeMobileNav"
          >
            <X class="h-5 w-5" />
          </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Admin navigation">
          <div v-for="group in navGroups" :key="group.label">
            <p class="admin-nav-group-label">{{ group.label }}</p>
            <Link
              v-for="item in group.items"
              :key="item.href"
              :href="item.href"
              class="admin-nav-item"
              :class="isNavActive(item.href) ? 'admin-nav-item-active' : ''"
              @click="closeMobileNav"
            >
              <component :is="item.icon" class="h-4 w-4 shrink-0" stroke-width="2" />
              <span class="truncate">{{ item.label }}</span>
              <ChevronRight
                v-if="isNavActive(item.href)"
                class="ml-auto h-3.5 w-3.5 shrink-0 opacity-70"
              />
            </Link>
          </div>
        </nav>

        <div class="border-t border-white/5 p-4">
          <Link
            href="/admin/profile"
            class="flex items-center gap-3 rounded-xl p-2.5 transition hover:bg-white/5"
            @click="closeMobileNav"
          >
            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-violet-500/20 text-xs font-bold text-violet-200 ring-1 ring-violet-400/30"
            >
              {{ userInitials }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-semibold text-white">{{ userName }}</p>
              <p class="truncate text-xs text-slate-400">{{ userEmail }}</p>
            </div>
          </Link>
        </div>
      </aside>

      <!-- Main -->
      <div class="flex min-w-0 flex-1 flex-col lg:ml-[17.5rem]">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/85 backdrop-blur-md">
          <div class="flex items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
              <button
                type="button"
                class="inline-flex rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 shadow-sm hover:bg-slate-50 lg:hidden"
                aria-label="Open menu"
                @click="navOpen = true"
              >
                <Menu class="h-5 w-5" />
              </button>
              <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Admin</p>
                <h2 class="truncate text-base font-bold text-slate-900 sm:text-lg">{{ pageTitle }}</h2>
              </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
              <Link href="/" class="admin-btn-secondary hidden px-3 py-2 sm:inline-flex" target="_blank">
                <ExternalLink class="h-4 w-4" />
                View site
              </Link>
              <button type="button" class="admin-btn-primary px-3 py-2" @click="logout">
                <LogOut class="h-4 w-4" />
                <span class="hidden sm:inline">Logout</span>
              </button>
            </div>
          </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
          <div class="mx-auto w-full max-w-[90rem] space-y-5">
            <AdminFlashAlert
              v-if="flashSuccess && !dismissedFlash.success"
              type="success"
              :message="flashSuccess"
              @dismiss="dismissedFlash.success = true"
            />
            <AdminFlashAlert
              v-if="flashError && !dismissedFlash.error"
              type="error"
              :message="flashError"
              @dismiss="dismissedFlash.error = true"
            />

            <slot />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
