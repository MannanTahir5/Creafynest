<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  Menu,
  X,
  Home,
  User,
  FolderKanban,
  Briefcase,
  ChevronDown,
  Newspaper,
  Mail,
  MessageCircle,
} from 'lucide-vue-next'
import ThemeToggle from '../Components/ThemeToggle.vue'
import ServicesMegaMenu from '../Components/ServicesMegaMenu.vue'

const isOpen = ref(false)
const servicesOpen = ref(false)
const servicesAccordionOpen = ref(false)
const servicesTriggerRef = ref(null)
const servicesMegaPanelRef = ref(null)
let servicesCloseTimer = null

const page = usePage()

/** Order matters: Services sits 3rd, immediately after About */
const navItems = [
  { type: 'link', href: '/', label: 'Home', icon: Home },
  { type: 'link', href: '/about', label: 'About', icon: User },
  { type: 'services' },
  { type: 'link', href: '/portfolio', label: 'Case studies', icon: FolderKanban },
  { type: 'link', href: '/blog', label: 'Blog', icon: Newspaper },
  { type: 'link', href: '/contact', label: 'Contact', icon: Mail },
]

const currentPath = computed(() => {
  const url = page?.url ?? ''
  return url.split('?')[0] || '/'
})

const user = computed(() => page.props?.auth?.user ?? null)

const branding = computed(() => page.props?.branding ?? {})
const headerLogoUrl = computed(() => branding.value.header_logo_url || '/images/creafynest-logo.png')
const headerLogoAlt = computed(() => branding.value.header_logo_alt || branding.value.site_name || 'Home')
const homeAriaLabel = computed(() => `${branding.value.site_name || 'Site'} home`)

const isServicesPath = computed(() => currentPath.value === '/services' || currentPath.value.startsWith('/services'))

function clearServicesTimer() {
  if (servicesCloseTimer) {
    clearTimeout(servicesCloseTimer)
    servicesCloseTimer = null
  }
}

function openServices() {
  clearServicesTimer()
  servicesOpen.value = true
}

function scheduleCloseServices() {
  clearServicesTimer()
  servicesCloseTimer = setTimeout(() => {
    servicesOpen.value = false
  }, 180)
}

function onServicesFocusOut(e) {
  const next = e.relatedTarget
  if (next instanceof Node) {
    if (servicesTriggerRef.value?.contains(next)) {
      return
    }
    if (servicesMegaPanelRef.value?.contains(next)) {
      return
    }
  }
  scheduleCloseServices()
}

function toggleServicesAccordion() {
  servicesAccordionOpen.value = !servicesAccordionOpen.value
}

function closeMenu() {
  isOpen.value = false
  servicesAccordionOpen.value = false
}

function onGlobalKeydown(e) {
  if (e.key === 'Escape') {
    servicesOpen.value = false
    servicesAccordionOpen.value = false
    isOpen.value = false
  }
}

watch(
  () => page.url,
  () => {
    closeMenu()
  },
)

onMounted(() => window.addEventListener('keydown', onGlobalKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', onGlobalKeydown)
  clearServicesTimer()
})
</script>

<template>
  <header class="sticky top-0 z-50 bg-slate-950/95 text-slate-100 shadow-sm backdrop-blur">
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between gap-4">
        <Link
          href="/"
          class="inline-flex min-w-0 items-center rounded-md px-2 py-1 hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
          :aria-label="homeAriaLabel"
          @click="closeMenu"
        >
          <img
            :src="headerLogoUrl"
            :alt="headerLogoAlt"
            class="h-9 w-auto sm:h-10"
            width="200"
            height="78"
          />
        </Link>

        <nav class="hidden items-center gap-0.5 md:flex" aria-label="Primary">
          <template v-for="item in navItems" :key="item.type === 'link' ? item.href : 'services'">
            <Link
              v-if="item.type === 'link'"
              :href="item.href"
              class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800/80 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
              :class="currentPath === item.href ? 'text-white' : ''"
            >
              <component :is="item.icon" class="h-4 w-4 shrink-0 opacity-80" stroke-width="1.75" />
              {{ item.label }}
            </Link>

            <div
              v-else
              ref="servicesTriggerRef"
              @mouseenter="openServices"
              @mouseleave="scheduleCloseServices"
              @focusin="openServices"
              @focusout="onServicesFocusOut"
            >
              <Link
                href="/services"
                class="inline-flex items-center gap-1 rounded-md px-2.5 py-2 text-sm font-medium text-slate-300 transition hover:bg-slate-800/80 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                :class="isServicesPath ? 'text-white' : ''"
                aria-haspopup="true"
                :aria-expanded="servicesOpen"
              >
                <Briefcase class="h-4 w-4 shrink-0 opacity-80" stroke-width="1.75" />
                Services
                <ChevronDown
                  class="h-3.5 w-3.5 shrink-0 opacity-70 transition"
                  :class="servicesOpen ? 'rotate-180' : ''"
                  stroke-width="2"
                />
              </Link>
            </div>
          </template>
        </nav>

        <div class="hidden shrink-0 items-center gap-2 md:flex">
          <ThemeToggle variant="nav" />
          <Link
            href="/contact"
            class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
          >
            <MessageCircle class="h-4 w-4" stroke-width="2" />
            Free consultation
          </Link>
          <Link
            v-if="user"
            href="/admin"
            class="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
          >
            Admin
          </Link>
          <Link
            v-else
            href="/login"
            class="rounded-md px-2.5 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
          >
            Login
          </Link>
        </div>

        <div class="flex items-center gap-1 md:hidden">
          <ThemeToggle variant="nav" />
          <button
            type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-slate-200 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
            :aria-expanded="isOpen"
            aria-label="Toggle navigation"
            @click="isOpen = !isOpen"
          >
            <Menu v-if="!isOpen" class="h-5 w-5" />
            <X v-else class="h-5 w-5" />
          </button>
        </div>
      </div>

      <!-- Full navbar-container width (max-w-7xl), not tied to the Services link column -->
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div
          v-show="servicesOpen"
          ref="servicesMegaPanelRef"
          class="absolute left-0 right-0 top-full z-[60] max-md:hidden pt-2"
          role="region"
          aria-label="Services overview"
          @mouseenter="openServices"
          @mouseleave="scheduleCloseServices"
          @focusout="onServicesFocusOut"
        >
          <ServicesMegaMenu class="w-full" />
        </div>
      </Transition>
    </div>

    <div v-if="isOpen" class="border-t border-slate-800 bg-slate-950 md:hidden">
      <div class="mx-auto max-w-7xl space-y-1 px-4 py-3 sm:px-6 lg:px-8">
        <template v-for="item in navItems" :key="item.type === 'link' ? item.href : 'services'">
          <Link
            v-if="item.type === 'link'"
            :href="item.href"
            class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm font-medium text-slate-200 hover:bg-slate-800 hover:text-white"
            :class="currentPath === item.href ? 'bg-slate-800 text-white' : ''"
            @click="closeMenu"
          >
            <component :is="item.icon" class="h-4 w-4 opacity-80" stroke-width="1.75" />
            {{ item.label }}
          </Link>

          <div v-else class="rounded-lg border border-slate-800 bg-slate-900/50">
            <button
              type="button"
              class="flex w-full items-center justify-between gap-2 px-3 py-2.5 text-left text-sm font-medium text-slate-200 hover:bg-slate-800/80"
              :aria-expanded="servicesAccordionOpen"
              @click="toggleServicesAccordion"
            >
              <span class="flex items-center gap-2">
                <Briefcase class="h-4 w-4 opacity-80" stroke-width="1.75" />
                Services
              </span>
              <ChevronDown class="h-4 w-4 transition" :class="servicesAccordionOpen ? 'rotate-180' : ''" />
            </button>
            <div v-show="servicesAccordionOpen" class="border-t border-slate-800 px-2 pb-3 pt-1">
              <ServicesMegaMenu @navigate="closeMenu" />
            </div>
          </div>
        </template>

        <Link
          href="/contact"
          class="mt-2 flex items-center justify-center gap-2 rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-500"
          @click="closeMenu"
        >
          <MessageCircle class="h-4 w-4" stroke-width="2" />
          Free consultation
        </Link>

        <Link
          v-if="user"
          href="/admin"
          class="block rounded-md px-3 py-2 text-center text-sm font-semibold text-slate-200 hover:bg-slate-800"
          @click="closeMenu"
        >
          Admin
        </Link>
        <Link
          v-else
          href="/login"
          class="block rounded-md px-3 py-2 text-center text-sm font-semibold text-slate-200 hover:bg-slate-800"
          @click="closeMenu"
        >
          Login
        </Link>
      </div>
    </div>
  </header>
</template>
