<script setup>
import { computed, ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { ArrowUpRight, BookOpen, ChevronLeft, ChevronRight, Clock, Newspaper, Search, Sparkles, Tags, X } from 'lucide-vue-next'
import Container from '../../Components/Ui/Container.vue'
import EmptyState from '../../Components/Ui/EmptyState.vue'
import BlogFeedCard from '../../Components/BlogFeedCard.vue'
import ContentImage from '../../Components/ContentImage.vue'

const props = defineProps({
  filters: { type: Object, default: () => ({ q: '', category: '' }) },
  categories: { type: Array, default: () => [] },
  posts: { type: Object, required: true },
  featured: { type: Object, default: null },
  totals: { type: Object, default: () => ({ posts: 0, categories: 0 }) },
  latestPosts: { type: Array, default: () => [] },
  blogByline: { type: String, default: 'By Creafynest' },
})

const isEmpty = computed(() => props.posts.total === 0 && !props.featured)
const hasFilter = computed(
  () => !!(String(props.filters.q ?? '').trim() || props.filters.category),
)
const wrongPage = computed(
  () => props.posts.total > 0 && props.posts.data.length === 0,
)

const q = ref(props.filters.q ?? '')
const categorySlug = ref(props.filters.category ?? '')

watch(
  () => props.filters,
  (f) => {
    q.value = f.q ?? ''
    categorySlug.value = f.category ?? ''
  },
  { deep: true }
)

let debounceTimer = null

function fetchBlogIndex(extra = {}) {
  router.get(
    '/blog',
    {
      q: q.value.trim() || undefined,
      category: categorySlug.value || undefined,
      page: extra.page ?? 1,
    },
    { preserveState: true, preserveScroll: true, replace: true }
  )
}

watch(categorySlug, () => {
  fetchBlogIndex({ page: 1 })
})

watch(q, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchBlogIndex({ page: 1 }), 350)
})

function setCategory(slug) {
  categorySlug.value = slug
}

function clearFilters() {
  q.value = ''
  categorySlug.value = ''
  fetchBlogIndex({ page: 1 })
}

function submitSidebarSearch() {
  clearTimeout(debounceTimer)
  fetchBlogIndex({ page: 1 })
}

const totalsLabel = computed(() => {
  const posts = props.totals?.posts ?? 0
  const cats = props.totals?.categories ?? 0
  return `${posts} ${posts === 1 ? 'article' : 'articles'} · ${cats} ${cats === 1 ? 'category' : 'categories'}`
})

const showFeatured = computed(
  () => !!props.featured && !hasFilter.value && props.posts.current_page === 1,
)
</script>

<template>
  <div class="bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <!-- Hero (matches Contact / About navy treatment) -->
    <section
      class="relative overflow-hidden bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] py-16 text-white sm:py-20 md:py-24"
      aria-label="Blog hero"
    >
      <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_70%_55%_at_50%_-10%,rgba(99,102,241,0.35),transparent)]"
        aria-hidden="true"
      />
      <div
        class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.05)_1px,transparent_1px)] bg-[size:3rem_3rem] [mask-image:linear-gradient(to_bottom,black,transparent)]"
        aria-hidden="true"
      />

      <Container class="relative z-10 text-center">
        <span
          class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-200 backdrop-blur-sm"
        >
          <Newspaper class="h-3.5 w-3.5" stroke-width="2.25" aria-hidden="true" />
          Insights
        </span>
        <h1
          class="mt-5 font-sans text-3xl font-bold tracking-tight text-white sm:text-4xl md:text-5xl lg:text-[3rem]"
        >
          Notes from the
          <span
            class="bg-gradient-to-r from-sky-300 via-indigo-200 to-violet-200 bg-clip-text text-transparent"
          >
            studio
          </span>
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-relaxed text-slate-200/90 sm:text-base">
          Field notes on Laravel, Vue, Inertia, AI features, and shipping maintainable
          products — without the buzzwords.
        </p>

        <p class="mt-6 inline-flex items-center gap-2 text-xs font-medium text-slate-300/90">
          <Sparkles class="h-3.5 w-3.5 text-amber-300" stroke-width="2" aria-hidden="true" />
          {{ totalsLabel }}
        </p>
      </Container>
    </section>

    <!-- Filter bar -->
    <section class="border-b border-slate-200/80 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/70">
      <Container class="py-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
              <Tags class="h-3 w-3" stroke-width="2" aria-hidden="true" />
              Topics
            </span>
            <button
              type="button"
              class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2"
              :class="categorySlug === ''
                ? 'bg-[#0f2a52] text-white shadow-sm dark:bg-white dark:text-[#0f2a52]'
                : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'"
              @click="setCategory('')"
            >
              All
            </button>
            <button
              v-for="c in categories"
              :key="c.slug"
              type="button"
              class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2"
              :class="categorySlug === c.slug
                ? 'bg-[#0f2a52] text-white shadow-sm dark:bg-white dark:text-[#0f2a52]'
                : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'"
              @click="setCategory(c.slug)"
            >
              {{ c.name }}
            </button>
          </div>

          <div class="flex w-full items-center gap-3 lg:w-auto lg:shrink-0 lg:hidden">
            <div class="relative w-full lg:w-80">
              <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-slate-400" aria-hidden="true">
                <Search class="h-4 w-4" stroke-width="2" />
              </span>
              <label class="sr-only" for="blog-search">Search posts</label>
              <input
                id="blog-search"
                v-model="q"
                type="search"
                placeholder="Search articles…"
                class="w-full rounded-full border border-slate-200 bg-white py-2.5 pl-9 pr-9 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-[#0f2a52] focus:outline-none focus:ring-2 focus:ring-[#0f2a52]/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500"
              >
              <button
                v-if="q"
                type="button"
                class="absolute inset-y-0 right-2 my-auto inline-flex h-7 w-7 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] dark:hover:bg-slate-800 dark:hover:text-slate-200"
                aria-label="Clear search"
                @click="q = ''"
              >
                <X class="h-4 w-4" stroke-width="2" />
              </button>
            </div>

            <button
              v-if="hasFilter"
              type="button"
              class="hidden text-xs font-semibold text-slate-600 underline-offset-4 transition hover:text-[#0f2a52] hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] dark:text-slate-300 dark:hover:text-white sm:inline-flex"
              @click="clearFilters"
            >
              Clear filters
            </button>
          </div>
        </div>
      </Container>
    </section>

    <!-- Feed + sidebar -->
    <section class="py-12 sm:py-16">
      <Container>
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
          <div class="lg:col-span-8 xl:col-span-9">
            <!-- Featured -->
            <div v-if="showFeatured" class="mb-10">
              <div class="mb-6 flex items-center gap-3">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:ring-amber-400/30">
                  <Sparkles class="h-4 w-4" stroke-width="2" aria-hidden="true" />
                </span>
                <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-700 dark:text-slate-200">
                  Featured article
                </h2>
              </div>

              <Link
                :href="`/blog/${featured.slug}`"
                class="group grid grid-cols-1 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_24px_60px_-30px_rgba(15,42,82,0.35)] transition hover:-translate-y-0.5 hover:shadow-[0_30px_70px_-30px_rgba(15,42,82,0.45)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 lg:grid-cols-2"
              >
                <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100 dark:bg-slate-800 lg:aspect-auto lg:h-full">
                  <ContentImage
                    v-if="featured.image_url"
                    :src="featured.image_url"
                    :webp-src="featured.image_webp_url || ''"
                    :alt="featured.title"
                    high-priority
                    img-class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
                  />
                  <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] text-xs font-semibold uppercase tracking-[0.2em] text-white/70"
                  >
                    Creafynest blog
                  </div>

                  <span
                    v-if="featured.category"
                    class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-[#0f2a52] shadow-sm ring-1 ring-black/5 backdrop-blur-sm"
                  >
                    {{ featured.category }}
                  </span>
                </div>

                <div class="flex flex-col justify-center gap-4 p-6 sm:p-8 lg:p-10">
                  <div class="flex items-center gap-3 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span v-if="featured.date">{{ featured.date }}</span>
                    <span v-if="featured.date && featured.reading_minutes" aria-hidden="true">·</span>
                    <span v-if="featured.reading_minutes" class="inline-flex items-center gap-1">
                      <Clock class="h-3.5 w-3.5" stroke-width="2" aria-hidden="true" />
                      {{ featured.reading_minutes }} min read
                    </span>
                  </div>

                  <h3 class="text-2xl font-bold leading-tight tracking-tight text-slate-900 transition group-hover:text-[#0f2a52] dark:text-white sm:text-3xl">
                    {{ featured.title }}
                  </h3>

                  <p
                    v-if="featured.excerpt"
                    class="line-clamp-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base"
                  >
                    {{ featured.excerpt }}
                  </p>

                  <div class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-[#0f2a52] dark:text-slate-100">
                    Read full article
                    <span
                      class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#0f2a52] text-white transition group-hover:bg-[#15366a] dark:bg-white dark:text-[#0f2a52]"
                      aria-hidden="true"
                    >
                      <ArrowUpRight class="h-4 w-4" stroke-width="2" />
                    </span>
                  </div>
                </div>
              </Link>
            </div>

            <div v-if="!isEmpty && !wrongPage" class="mb-6 flex items-center gap-3">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700">
                <BookOpen class="h-4 w-4" stroke-width="2" aria-hidden="true" />
              </span>
              <h2 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-700 dark:text-slate-200">
                <span v-if="hasFilter">Matching articles</span>
                <span v-else-if="showFeatured">More from the studio</span>
                <span v-else>Latest articles</span>
              </h2>
            </div>

            <div v-if="wrongPage" class="mt-2">
              <EmptyState
                title="No posts on this page"
                description="Try returning to the first page of results."
              >
                <Link
                  href="/blog"
                  class="inline-flex items-center justify-center rounded-lg bg-[#0f2a52] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#15366a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2"
                >
                  Back to blog
                </Link>
              </EmptyState>
            </div>

            <div v-else-if="isEmpty && hasFilter" class="mt-2">
              <EmptyState
                title="No posts match your filters"
                description="Try clearing search or choosing a different category."
              >
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
                  @click="clearFilters"
                >
                  Clear filters
                </button>
              </EmptyState>
            </div>

            <div v-else-if="isEmpty" class="mt-2">
              <EmptyState
                title="No blog posts yet"
                description="New articles will show up here once they are published."
              >
                <Link
                  href="/contact"
                  class="inline-flex items-center justify-center rounded-lg bg-[#0f2a52] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#15366a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2"
                >
                  Say hello
                </Link>
              </EmptyState>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <BlogFeedCard
                v-for="p in posts.data"
                :key="p.slug"
                :title="p.title"
                :slug="p.slug"
                :date="p.date"
                :excerpt="p.excerpt"
                :image-url="p.image_url || ''"
                :image-webp-url="p.image_webp_url || ''"
                :byline="blogByline"
              />
            </div>

            <div v-if="posts.last_page > 1" class="mt-12 flex flex-col items-center justify-center gap-3 sm:flex-row">
              <Link
                v-if="posts.prev_page_url"
                :href="posts.prev_page_url"
                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800"
              >
                <ChevronLeft class="h-4 w-4" stroke-width="2" aria-hidden="true" />
                Previous
              </Link>
              <p class="text-sm text-slate-600 dark:text-slate-400">
                Page <span class="font-bold text-slate-900 dark:text-white">{{ posts.current_page }}</span>
                of
                <span class="font-bold text-slate-900 dark:text-white">{{ posts.last_page }}</span>
              </p>
              <Link
                v-if="posts.next_page_url"
                :href="posts.next_page_url"
                class="inline-flex items-center gap-1.5 rounded-full bg-[#0f2a52] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#15366a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2 dark:bg-white dark:text-[#0f2a52] dark:hover:bg-slate-100"
              >
                Next
                <ChevronRight class="h-4 w-4" stroke-width="2" aria-hidden="true" />
              </Link>
            </div>
          </div>

          <aside class="lg:col-span-4 xl:col-span-3">
            <div class="lg:sticky lg:top-24 lg:space-y-8">
              <form
                class="flex items-center overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm focus-within:border-[#0f2a52] focus-within:ring-2 focus-within:ring-[#0f2a52]/20 dark:border-slate-700 dark:bg-slate-900"
                role="search"
                @submit.prevent="submitSidebarSearch"
              >
                <label class="sr-only" for="blog-sidebar-search">Search posts</label>
                <input
                  id="blog-sidebar-search"
                  v-model="q"
                  type="search"
                  placeholder="Search here..."
                  class="min-w-0 flex-1 border-0 bg-transparent px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0 dark:text-slate-100 dark:placeholder:text-slate-500"
                >
                <button
                  type="submit"
                  class="flex h-12 w-12 shrink-0 items-center justify-center bg-slate-100 text-slate-600 transition hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                  aria-label="Search"
                >
                  <Search class="h-4 w-4" stroke-width="2.25" />
                </button>
              </form>

              <div class="mt-8 lg:mt-0">
                <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                  Latest posts
                </h3>

                <ul v-if="latestPosts.length" class="mt-5 space-y-5">
                  <li
                    v-for="lp in latestPosts"
                    :key="lp.slug"
                  >
                    <Link
                      :href="`/blog/${lp.slug}`"
                      class="group flex items-start gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0f2a52] focus-visible:ring-offset-2"
                    >
                      <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-full bg-slate-200 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                        <ContentImage
                          v-if="lp.image_url"
                          :src="lp.image_url"
                          :webp-src="lp.image_webp_url || ''"
                          :alt="lp.title"
                          img-class="h-full w-full object-cover"
                        />
                        <div
                          v-else
                          class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#0f2a52] via-[#15366a] to-[#0b2347] text-[10px] font-bold uppercase tracking-wider text-white/80"
                        >
                          Blog
                        </div>
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-orange-500">
                          Blog
                        </p>
                        <p class="mt-1 line-clamp-3 text-sm font-semibold leading-snug text-slate-800 transition group-hover:text-[#0f2a52] dark:text-slate-100 dark:group-hover:text-white">
                          {{ lp.title }}
                        </p>
                      </div>
                    </Link>
                  </li>
                </ul>

                <p v-else class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                  No posts yet.
                </p>
              </div>
            </div>
          </aside>
        </div>
      </Container>
    </section>
  </div>
</template>
