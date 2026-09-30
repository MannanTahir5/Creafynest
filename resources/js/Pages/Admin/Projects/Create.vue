<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import RichTextEditor from '../../../Components/Admin/RichTextEditor.vue'

const form = useForm({
  title: '',
  slug: '',
  category: '',
  description: '',
  client_name: '',
  industry: '',
  services: '',
  how_it_started_title: '',
  how_it_started_text: '',
  how_it_started_image: null,
  challenge_title: '',
  challenge_text: '',
  challenge_image: null,
  approach_title: '',
  approach_text: '',
  approach_image: null,
  results_title: '',
  results_text: '',
  results_image: null,
  logo_image: null,
  logo_is_light: false,
  video_files: [],
  video_urls: '',
  live_url: '',
  github_url: '',
  image: null,
  gallery: [],
})

function submit() {
  form.post('/admin/projects', { forceFormData: true })
}

function selectGalleryMedia(event) {
  const files = Array.from(event.target.files || [])

  form.gallery = files.filter((file) => file.type.startsWith('image/'))
  form.video_files = files.filter((file) => file.type.startsWith('video/'))
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">New project</h1>
        <p class="mt-2 text-sm text-slate-600">Slug is optional; it will be generated from the title if empty.</p>
      </div>
      <Link
        href="/admin/projects"
        class="text-sm font-semibold text-slate-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
      >
        Back
      </Link>
    </div>

    <form class="mt-8 max-w-3xl space-y-4" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div>
        <label for="project-title" class="block text-sm font-medium text-slate-700">Title</label>
        <input
          id="project-title"
          v-model="form.title"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.title }"
          :aria-invalid="!!form.errors.title"
        >
        <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.title }}</p>
      </div>
      <div>
        <label for="project-slug" class="block text-sm font-medium text-slate-700">Slug (optional)</label>
        <input
          id="project-slug"
          v-model="form.slug"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.slug }"
          :aria-invalid="!!form.errors.slug"
        >
        <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
      </div>
      <div>
        <label for="project-category" class="block text-sm font-medium text-slate-700">Category</label>
        <input
          id="project-category"
          v-model="form.category"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.category }"
          :aria-invalid="!!form.errors.category"
        >
        <p v-if="form.errors.category" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.category }}</p>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="project-client-name" class="block text-sm font-medium text-slate-700">Client</label>
          <input id="project-client-name" v-model="form.client_name" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.client_name }">
          <p v-if="form.errors.client_name" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.client_name }}</p>
        </div>
        <div>
          <label for="project-industry" class="block text-sm font-medium text-slate-700">Industry</label>
          <input id="project-industry" v-model="form.industry" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.industry }">
          <p v-if="form.errors.industry" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.industry }}</p>
        </div>
      </div>
      <div>
        <label for="project-services" class="block text-sm font-medium text-slate-700">Services (comma-separated)</label>
        <input id="project-services" v-model="form.services" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.services }" placeholder="Web Design, Branding, SEO">
        <p v-if="form.errors.services" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.services }}</p>
      </div>
      <div>
        <label for="project-description" class="block text-sm font-medium text-slate-700">Overview / Description</label>
        <textarea
          id="project-description"
          v-model="form.description"
          rows="4"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.description }"
          placeholder="Enter project summary overview..."
        />
        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
      </div>
      <div>
        <label for="project-how-it-started-title" class="block text-sm font-medium text-slate-700">How it started title</label>
        <input id="project-how-it-started-title" v-model="form.how_it_started_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.how_it_started_title }">
        <p v-if="form.errors.how_it_started_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">How it started text</label>
        <RichTextEditor v-model="form.how_it_started_text" :invalid="!!form.errors.how_it_started_text" placeholder="Describe how the project started..." />
        <p v-if="form.errors.how_it_started_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_text }}</p>
      </div>
      <div>
        <label for="project-how-it-started-image" class="block text-sm font-medium text-slate-700">How it started image</label>
        <input id="project-how-it-started-image" type="file" class="form-file" accept="image/*" @change="form.how_it_started_image = $event.target.files[0]">
        <p v-if="form.errors.how_it_started_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_image }}</p>
      </div>
      <div>
        <label for="project-challenge-title" class="block text-sm font-medium text-slate-700">Challenge title</label>
        <input id="project-challenge-title" v-model="form.challenge_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.challenge_title }">
        <p v-if="form.errors.challenge_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Challenge text</label>
        <RichTextEditor v-model="form.challenge_text" :invalid="!!form.errors.challenge_text" placeholder="Describe the challenge..." />
        <p v-if="form.errors.challenge_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_text }}</p>
      </div>
      <div>
        <label for="project-challenge-image" class="block text-sm font-medium text-slate-700">Challenge image</label>
        <input id="project-challenge-image" type="file" class="form-file" accept="image/*" @change="form.challenge_image = $event.target.files[0]">
        <p v-if="form.errors.challenge_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_image }}</p>
      </div>
      <div>
        <label for="project-approach-title" class="block text-sm font-medium text-slate-700">Our approach title</label>
        <input id="project-approach-title" v-model="form.approach_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.approach_title }">
        <p v-if="form.errors.approach_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Our approach text</label>
        <RichTextEditor v-model="form.approach_text" :invalid="!!form.errors.approach_text" placeholder="Describe your approach..." />
        <p v-if="form.errors.approach_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_text }}</p>
      </div>
      <div>
        <label for="project-approach-image" class="block text-sm font-medium text-slate-700">Our approach image</label>
        <input id="project-approach-image" type="file" class="form-file" accept="image/*" @change="form.approach_image = $event.target.files[0]">
        <p v-if="form.errors.approach_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_image }}</p>
      </div>
      <div>
        <label for="project-results-title" class="block text-sm font-medium text-slate-700">Results title</label>
        <input id="project-results-title" v-model="form.results_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.results_title }">
        <p v-if="form.errors.results_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Results text</label>
        <RichTextEditor v-model="form.results_text" :invalid="!!form.errors.results_text" placeholder="Describe the results and outcomes..." />
        <p v-if="form.errors.results_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_text }}</p>
      </div>
      <div>
        <label for="project-results-image" class="block text-sm font-medium text-slate-700">Results image</label>
        <input id="project-results-image" type="file" class="form-file" accept="image/*" @change="form.results_image = $event.target.files[0]">
        <p v-if="form.errors.results_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_image }}</p>
      </div>

      <div>
        <label for="project-logo" class="block text-sm font-medium text-slate-700">Logo image</label>
        <input
          id="project-logo"
          type="file"
          class="form-file"
          accept="image/*"
          @change="form.logo_image = $event.target.files[0]"
        >
        <p v-if="form.errors.logo_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.logo_image }}</p>
        <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
          <input v-model="form.logo_is_light" type="checkbox" class="rounded border-slate-300 text-slate-900 focus:ring-slate-400">
          This logo is light/white
        </label>
      </div>
      <!-- Video URLs (YouTube, Vimeo, or external MP4) -->
      <div>
        <label for="project-video-urls" class="block text-sm font-medium text-slate-700">Video URLs (YouTube, Vimeo, or cloud video links)</label>
        <textarea
          id="project-video-urls"
          v-model="form.video_urls"
          rows="2"
          class="form-input font-mono text-xs"
          placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/... (one URL per line)"
        />
        <p class="mt-1 text-xs text-slate-500">Paste YouTube, Vimeo, or direct video links (one per line). Ideal for high-definition and long videos.</p>
        <p v-if="form.errors.video_urls" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.video_urls }}</p>
      </div>

      <!-- Upload videos -->
      <div>
        <label for="project-video-files" class="block text-sm font-medium text-slate-700">Upload video files (MP4, WebM, MOV)</label>
        <input
          id="project-video-files"
          type="file"
          class="form-file"
          accept="video/mp4,video/webm,video/quicktime,video/*"
          multiple
          @change="form.video_files = Array.from($event.target.files || [])"
        >
        <p class="mt-1 text-xs text-slate-500">Upload video files up to 100MB. Ensure server upload limits (upload_max_filesize and post_max_size) are configured accordingly.</p>
        <p v-if="form.errors.video_files" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.video_files }}</p>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="project-live" class="block text-sm font-medium text-slate-700">Live URL</label>
          <input
            id="project-live"
            v-model="form.live_url"
            type="url"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.live_url }"
            placeholder="https://example.com"
          >
          <p v-if="form.errors.live_url" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.live_url }}</p>
        </div>
        <div>
          <label for="project-github" class="block text-sm font-medium text-slate-700">GitHub URL</label>
          <input
            id="project-github"
            v-model="form.github_url"
            type="url"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.github_url }"
            placeholder="https://github.com/username/project"
          >
          <p v-if="form.errors.github_url" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.github_url }}</p>
        </div>
      </div>
      <div>
        <label for="project-hero" class="block text-sm font-medium text-slate-700">Hero image</label>
        <input
          id="project-hero"
          type="file"
          class="form-file"
          accept="image/*"
          @change="form.image = $event.target.files[0]"
        >
        <p v-if="form.errors.image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.image }}</p>
      </div>
      <div>
        <label for="project-gallery" class="block text-sm font-medium text-slate-700">Gallery media</label>
        <input
          id="project-gallery"
          type="file"
          class="form-file"
          accept="image/*,video/*"
          multiple
          @change="selectGalleryMedia"
        >
        <p class="mt-1 text-xs text-slate-500">Select images and videos from your gallery. Images are added to the project gallery and videos to the project videos.</p>
        <p v-if="form.errors.gallery || form.errors.video_files" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.gallery || form.errors.video_files }}</p>
      </div>

      <div class="flex gap-3">
        <button
          type="submit"
          class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60"
          :disabled="form.processing"
        >
          Create
        </button>
      </div>
    </form>
  </div>
</template>
