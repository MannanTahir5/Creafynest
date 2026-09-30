<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import RichTextEditor from '../../../Components/Admin/RichTextEditor.vue'

const props = defineProps({
  project: { type: Object, required: true },
})

const form = useForm({
  _method: 'PUT',
  title: props.project.title,
  slug: props.project.slug,
  category: props.project.category,
  description: props.project.description || '',
  client_name: props.project.client_name || '',
  industry: props.project.industry || '',
  services: props.project.services || '',
  how_it_started_title: props.project.how_it_started_title || '',
  how_it_started_text: props.project.how_it_started_text || '',
  how_it_started_image: null,
  challenge_title: props.project.challenge_title || '',
  challenge_text: props.project.challenge_text || '',
  challenge_image: null,
  approach_title: props.project.approach_title || '',
  approach_text: props.project.approach_text || '',
  approach_image: null,
  results_title: props.project.results_title || '',
  results_text: props.project.results_text || '',
  results_image: null,
  logo_image: null,
  logo_is_light: Boolean(props.project.logo_is_light),
  video_files: [],
  video_urls: '',
  keep_videos: [...(props.project.video_urls || [])],
  live_url: props.project.live_url || '',
  github_url: props.project.github_url || '',
  image: null,
  gallery: [],
  keep_gallery: [...(props.project.gallery || [])],
})

function submit() {
  form.post(`/admin/projects/${props.project.id}`, {
    forceFormData: true,
  })
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
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Edit project</h1>
        <p class="mt-2 text-sm text-slate-600">Update details and manage gallery images.</p>
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
        <label for="edit-project-title" class="block text-sm font-medium text-slate-700">Title</label>
        <input
          id="edit-project-title"
          v-model="form.title"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.title }"
        >
        <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.title }}</p>
      </div>
      <div>
        <label for="edit-project-slug" class="block text-sm font-medium text-slate-700">Slug</label>
        <input
          id="edit-project-slug"
          v-model="form.slug"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.slug }"
        >
        <p v-if="form.errors.slug" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.slug }}</p>
      </div>
      <div>
        <label for="edit-project-category" class="block text-sm font-medium text-slate-700">Category</label>
        <input
          id="edit-project-category"
          v-model="form.category"
          type="text"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.category }"
        >
        <p v-if="form.errors.category" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.category }}</p>
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="edit-project-client-name" class="block text-sm font-medium text-slate-700">Client</label>
          <input id="edit-project-client-name" v-model="form.client_name" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.client_name }">
          <p v-if="form.errors.client_name" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.client_name }}</p>
        </div>
        <div>
          <label for="edit-project-industry" class="block text-sm font-medium text-slate-700">Industry</label>
          <input id="edit-project-industry" v-model="form.industry" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.industry }">
          <p v-if="form.errors.industry" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.industry }}</p>
        </div>
      </div>
      <div>
        <label for="edit-project-services" class="block text-sm font-medium text-slate-700">Services (comma-separated)</label>
        <input id="edit-project-services" v-model="form.services" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.services }" placeholder="Web Design, Branding, SEO">
        <p v-if="form.errors.services" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.services }}</p>
      </div>
      <div>
        <label for="edit-project-description" class="block text-sm font-medium text-slate-700">Overview / Description</label>
        <textarea
          id="edit-project-description"
          v-model="form.description"
          rows="4"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.description }"
          placeholder="Enter project summary overview..."
        />
        <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.description }}</p>
      </div>
      <div>
        <label for="edit-project-how-it-started-title" class="block text-sm font-medium text-slate-700">How it started title</label>
        <input id="edit-project-how-it-started-title" v-model="form.how_it_started_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.how_it_started_title }">
        <p v-if="form.errors.how_it_started_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">How it started text</label>
        <RichTextEditor v-model="form.how_it_started_text" :invalid="!!form.errors.how_it_started_text" placeholder="Describe how the project started..." />
        <p v-if="form.errors.how_it_started_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_text }}</p>
      </div>
      <div>
        <label for="edit-project-how-it-started-image" class="block text-sm font-medium text-slate-700">How it started image</label>
        <div v-if="project.how_it_started_image_url" class="mt-1 mb-2 flex items-center gap-3">
          <img :src="project.how_it_started_image_url" alt="Current how it started image" class="h-20 w-32 rounded-lg border border-slate-200 object-cover shadow-sm">
          <span class="text-xs text-slate-500">Current image — pick a new file below to replace it</span>
        </div>
        <input id="edit-project-how-it-started-image" type="file" class="form-file" accept="image/*" @change="form.how_it_started_image = $event.target.files[0]">
        <p v-if="form.errors.how_it_started_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.how_it_started_image }}</p>
      </div>
      <div>
        <label for="edit-project-challenge-title" class="block text-sm font-medium text-slate-700">Challenge title</label>
        <input id="edit-project-challenge-title" v-model="form.challenge_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.challenge_title }">
        <p v-if="form.errors.challenge_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Challenge text</label>
        <RichTextEditor v-model="form.challenge_text" :invalid="!!form.errors.challenge_text" placeholder="Describe the challenge..." />
        <p v-if="form.errors.challenge_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_text }}</p>
      </div>
      <div>
        <label for="edit-project-challenge-image" class="block text-sm font-medium text-slate-700">Challenge image</label>
        <div v-if="project.challenge_image_url" class="mt-1 mb-2 flex items-center gap-3">
          <img :src="project.challenge_image_url" alt="Current challenge image" class="h-20 w-32 rounded-lg border border-slate-200 object-cover shadow-sm">
          <span class="text-xs text-slate-500">Current image — pick a new file below to replace it</span>
        </div>
        <input id="edit-project-challenge-image" type="file" class="form-file" accept="image/*" @change="form.challenge_image = $event.target.files[0]">
        <p v-if="form.errors.challenge_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.challenge_image }}</p>
      </div>
      <div>
        <label for="edit-project-approach-title" class="block text-sm font-medium text-slate-700">Our approach title</label>
        <input id="edit-project-approach-title" v-model="form.approach_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.approach_title }">
        <p v-if="form.errors.approach_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Our approach text</label>
        <RichTextEditor v-model="form.approach_text" :invalid="!!form.errors.approach_text" placeholder="Describe your approach..." />
        <p v-if="form.errors.approach_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_text }}</p>
      </div>
      <div>
        <label for="edit-project-approach-image" class="block text-sm font-medium text-slate-700">Our approach image</label>
        <div v-if="project.approach_image_url" class="mt-1 mb-2 flex items-center gap-3">
          <img :src="project.approach_image_url" alt="Current approach image" class="h-20 w-32 rounded-lg border border-slate-200 object-cover shadow-sm">
          <span class="text-xs text-slate-500">Current image — pick a new file below to replace it</span>
        </div>
        <input id="edit-project-approach-image" type="file" class="form-file" accept="image/*" @change="form.approach_image = $event.target.files[0]">
        <p v-if="form.errors.approach_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.approach_image }}</p>
      </div>
      <div>
        <label for="edit-project-results-title" class="block text-sm font-medium text-slate-700">Results title</label>
        <input id="edit-project-results-title" v-model="form.results_title" type="text" class="form-input" :class="{ 'form-input-invalid': !!form.errors.results_title }">
        <p v-if="form.errors.results_title" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_title }}</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Results text</label>
        <RichTextEditor v-model="form.results_text" :invalid="!!form.errors.results_text" placeholder="Describe the results and outcomes..." />
        <p v-if="form.errors.results_text" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_text }}</p>
      </div>
      <div>
        <label for="edit-project-results-image" class="block text-sm font-medium text-slate-700">Results image</label>
        <div v-if="project.results_image_url" class="mt-1 mb-2 flex items-center gap-3">
          <img :src="project.results_image_url" alt="Current results image" class="h-20 w-32 rounded-lg border border-slate-200 object-cover shadow-sm">
          <span class="text-xs text-slate-500">Current image — pick a new file below to replace it</span>
        </div>
        <input id="edit-project-results-image" type="file" class="form-file" accept="image/*" @change="form.results_image = $event.target.files[0]">
        <p v-if="form.errors.results_image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.results_image }}</p>
      </div>

      <div>
        <label for="edit-project-logo" class="block text-sm font-medium text-slate-700">Logo image</label>
        <div v-if="project.logo_preview_url" class="mt-1 mb-2 flex items-center gap-3">
          <img
            :src="project.logo_preview_url"
            alt="Current logo"
            class="h-16 max-w-[160px] rounded-lg border border-slate-200 object-contain p-1 shadow-sm"
            :class="project.logo_is_light ? 'bg-slate-800' : 'bg-white'"
          >
          <span class="text-xs text-slate-500">Current logo — pick a new file below to replace it</span>
        </div>
        <input
          id="edit-project-logo"
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
      <!-- Current Videos (if any) -->
      <div v-if="(project.video_previews || []).length" class="rounded-lg border border-slate-200 bg-white p-4">
        <p class="text-sm font-semibold text-slate-900">Current project videos</p>
        <p class="mt-1 text-xs text-slate-600">Uncheck to remove any video from this case study on save.</p>
        <div class="mt-3 space-y-2">
          <label
            v-for="item in project.video_previews"
            :key="item.path"
            class="flex items-center gap-3 rounded-lg border border-slate-200 p-2 text-sm text-slate-700 hover:bg-slate-50 cursor-pointer"
          >
            <input
              v-model="form.keep_videos"
              type="checkbox"
              :value="item.path"
              class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
            >
            <span class="truncate text-xs font-mono text-slate-600">{{ item.path }}</span>
          </label>
        </div>
      </div>

      <!-- Video URLs (YouTube, Vimeo, or external MP4) -->
      <div>
        <label for="edit-project-video-urls" class="block text-sm font-medium text-slate-700">Video URLs (YouTube, Vimeo, or cloud video links)</label>
        <textarea
          id="edit-project-video-urls"
          v-model="form.video_urls"
          rows="2"
          class="form-input font-mono text-xs"
          placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/... (one URL per line)"
        />
        <p class="mt-1 text-xs text-slate-500">Paste YouTube, Vimeo, or direct video links (one per line). Ideal for high-definition and long videos.</p>
        <p v-if="form.errors.video_urls" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.video_urls }}</p>
      </div>

      <!-- Upload video files -->
      <div>
        <label for="edit-project-video-files" class="block text-sm font-medium text-slate-700">Upload video files (MP4, WebM, MOV)</label>
        <input
          id="edit-project-video-files"
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
          <label for="edit-project-live" class="block text-sm font-medium text-slate-700">Live URL</label>
          <input
            id="edit-project-live"
            v-model="form.live_url"
            type="url"
            class="form-input"
            :class="{ 'form-input-invalid': !!form.errors.live_url }"
            placeholder="https://example.com"
          >
          <p v-if="form.errors.live_url" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.live_url }}</p>
        </div>
        <div>
          <label for="edit-project-github" class="block text-sm font-medium text-slate-700">GitHub URL</label>
          <input
            id="edit-project-github"
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
        <label for="edit-project-hero" class="block text-sm font-medium text-slate-700">Replace hero image</label>
        <div v-if="project.image_url" class="mt-1 mb-2 flex items-center gap-3">
          <img :src="project.image_url" alt="Current hero image" class="h-20 w-32 rounded-lg border border-slate-200 object-cover shadow-sm">
          <span class="text-xs text-slate-500">Current hero image — pick a new file below to replace it</span>
        </div>
        <input
          id="edit-project-hero"
          type="file"
          class="form-file"
          accept="image/*"
          @change="form.image = $event.target.files[0]"
        >
        <p v-if="form.errors.image" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.image }}</p>
      </div>

      <div v-if="(project.gallery_previews || []).length" class="rounded-lg border border-slate-200 bg-white p-4">
        <p class="text-sm font-semibold text-slate-900">Keep gallery images</p>
        <p class="mt-1 text-xs text-slate-600">Uncheck to delete from storage on save.</p>
        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
          <label
            v-for="item in project.gallery_previews"
            :key="item.path"
            class="group relative cursor-pointer overflow-hidden rounded-lg border border-slate-200 bg-slate-50 shadow-sm transition hover:border-slate-400"
          >
            <input
              v-model="form.keep_gallery"
              type="checkbox"
              :value="item.path"
              class="absolute right-2 top-2 z-10 rounded border-slate-300 text-slate-900 focus:ring-2 focus:ring-slate-400"
            >
            <img
              :src="item.url"
              :alt="item.path"
              class="h-28 w-full object-cover transition group-hover:opacity-90"
            >
            <p class="truncate px-2 py-1 text-[10px] text-slate-500">{{ item.path.split('/').pop() }}</p>
          </label>
        </div>
      </div>

      <div>
        <label for="edit-project-gallery" class="block text-sm font-medium text-slate-700">Add gallery media</label>
        <input
          id="edit-project-gallery"
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
          Save
        </button>
      </div>
    </form>
  </div>
</template>
