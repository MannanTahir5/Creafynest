<script setup>
import { Link } from '@inertiajs/vue3'
import Container from '../../Components/Ui/Container.vue'
import ContentImage from '../../Components/ContentImage.vue'
import Hero from '../../Components/Hero.vue'
import Button from '../../Components/Ui/Button.vue'
import Card from '../../Components/Ui/Card.vue'

defineProps({
  post: { type: Object, required: true },
  relatedPosts: { type: Array, default: () => [] },
})
</script>

<template>
  <div>
    <Hero>
      <p class="text-sm font-semibold tracking-wide text-slate-500 dark:text-slate-400">Blog</p>
      <h1 class="mt-4 text-4xl font-semibold tracking-tight text-slate-900 dark:text-slate-100 sm:text-5xl">
        {{ post.title }}
      </h1>
      <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600 dark:text-slate-400">
        {{ post.meta_description || 'Article' }}
      </p>
      <div class="mt-8">
        <Button href="/blog" variant="secondary">Back to blog</Button>
      </div>
    </Hero>

    <section class="pb-16">
      <Container>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div class="lg:col-span-2">
            <Card>
              <div
                v-if="post.image_url"
                class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700"
              >
                <ContentImage
                  :src="post.image_url"
                  :webp-src="post.image_webp_url || ''"
                  :alt="post.title"
                  img-class="aspect-video w-full object-cover"
                />
              </div>
              <div
                v-else
                class="flex aspect-video items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-400"
              >
                No cover image
              </div>
              <div
                class="prose prose-slate mt-6 max-w-none dark:prose-invert"
                v-html="post.content_html"
              />
            </Card>
          </div>
          <div>
            <Card>
              <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Meta</p>
              <div class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                <p><span class="font-medium text-slate-700 dark:text-slate-300">Category:</span> {{ post.category || '—' }}</p>
                <p><span class="font-medium text-slate-700 dark:text-slate-300">Published:</span> {{ post.published_at || '—' }}</p>
              </div>
            </Card>

            <div class="mt-6">
              <Card>
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">Related posts</p>
                <ul v-if="relatedPosts.length" class="mt-3 space-y-2 text-sm">
                  <li v-for="r in relatedPosts" :key="r.slug">
                    <Link :href="`/blog/${r.slug}`" class="font-medium text-slate-900 hover:underline dark:text-slate-100">
                      {{ r.title }}
                    </Link>
                    <span class="text-slate-500"> · {{ r.date }}</span>
                  </li>
                </ul>
                <p v-else class="mt-2 text-sm text-slate-600 dark:text-slate-400">No related posts in this category yet.</p>
              </Card>
            </div>
          </div>
        </div>
      </Container>
    </section>
  </div>
</template>
