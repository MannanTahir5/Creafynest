<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  section: { type: Object, required: true },
})

function clonePillars(list) {
  return (list || []).map((p) => ({
    id: p.id ?? null,
    title: p.title ?? '',
    description: p.description ?? '',
    icon_class: p.icon_class ?? '',
    is_active: p.is_active === undefined ? true : !!p.is_active,
  }))
}

function cloneValueItems(list) {
  return (list || []).map((v) => ({
    id: v.id ?? null,
    title: v.title ?? '',
    description: v.description ?? '',
    icon: v.icon ?? '',
    is_active: v.is_active === undefined ? true : !!v.is_active,
  }))
}

const form = useForm({
  _method: 'put',

  hero_eyebrow: props.section.hero_eyebrow ?? '',
  hero_heading_lead: props.section.hero_heading_lead ?? '',
  hero_heading_accent: props.section.hero_heading_accent ?? '',
  hero_description: props.section.hero_description ?? '',
  hero_primary_label: props.section.hero_primary_label ?? '',
  hero_primary_href: props.section.hero_primary_href ?? '',
  hero_secondary_label: props.section.hero_secondary_label ?? '',
  hero_secondary_href: props.section.hero_secondary_href ?? '',
  hero_tertiary_label: props.section.hero_tertiary_label ?? '',
  hero_tertiary_href: props.section.hero_tertiary_href ?? '',

  panel_eyebrow: props.section.panel_eyebrow ?? '',
  panel_description: props.section.panel_description ?? '',
  panel_node_one_label: props.section.panel_node_one_label ?? '',
  panel_node_two_label: props.section.panel_node_two_label ?? '',
  panel_node_three_label: props.section.panel_node_three_label ?? '',
  panel_center_label: props.section.panel_center_label ?? '',
  panel_footer_label: props.section.panel_footer_label ?? '',

  who_heading: props.section.who_heading ?? '',
  who_description: props.section.who_description ?? '',

  why_heading: props.section.why_heading ?? '',
  why_description: props.section.why_description ?? '',

  cta_heading: props.section.cta_heading ?? '',
  cta_description: props.section.cta_description ?? '',
  cta_button_label: props.section.cta_button_label ?? '',
  cta_button_href: props.section.cta_button_href ?? '',
  cta_is_active: props.section.cta_is_active === undefined ? true : !!props.section.cta_is_active,

  is_active: props.section.is_active === undefined ? true : !!props.section.is_active,

  pillars: clonePillars(props.section.pillars),
  value_items: cloneValueItems(props.section.value_items),
})

function moveItem(list, index, delta) {
  const target = index + delta
  if (target < 0 || target >= list.length) return
  const [m] = list.splice(index, 1)
  list.splice(target, 0, m)
}

function addPillar() {
  form.pillars.push({
    id: null,
    title: '',
    description: '',
    icon_class: 'from-blue-500 via-indigo-500 to-violet-600',
    is_active: true,
  })
}

function removePillar(i) {
  form.pillars.splice(i, 1)
}

function addValueItem() {
  form.value_items.push({
    id: null,
    title: '',
    description: '',
    icon: 'Sparkles',
    is_active: true,
  })
}

function removeValueItem(i) {
  form.value_items.splice(i, 1)
}

function errorAt(prefix, i, key) {
  return form.errors[`${prefix}.${i}.${key}`]
}

function submit() {
  form.put('/admin/about-page')
}
</script>

<template>
  <div>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold tracking-tight text-slate-900">About page</h1>
      <Link href="/admin" class="text-sm font-semibold text-slate-700 hover:underline">Back</Link>
    </div>
    <p class="mt-1 max-w-2xl text-sm text-slate-500">
      Edit every section that appears on the public <code class="rounded bg-slate-100 px-1">/about</code> page —
      the navy hero, the &ldquo;How we work&rdquo; panel, the pillars grid, the &ldquo;Why work with us&rdquo; values,
      and the closing call-to-action.
    </p>

    <form class="mt-8 space-y-8" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <!-- Hero -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold text-slate-900">Hero banner</h2>
        <p class="mt-1 text-xs text-slate-500">
          The navy banner at the top. The heading is split into two parts so the second part can be styled with a
          gradient.
        </p>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Eyebrow</label>
            <input v-model="form.hero_eyebrow" type="text" class="form-input" placeholder="About Creafynest">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.hero_description" rows="3" class="form-textarea" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading (primary)</label>
            <input v-model="form.hero_heading_lead" type="text" class="form-input" placeholder="We build software">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading (gradient accent)</label>
            <input v-model="form.hero_heading_accent" type="text" class="form-input" placeholder="you can grow into">
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
          <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Primary CTA</p>
            <div class="mt-3 space-y-2">
              <input v-model="form.hero_primary_label" type="text" class="form-input" placeholder="Get in touch">
              <input v-model="form.hero_primary_href" type="text" class="form-input" placeholder="/contact">
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Secondary CTA</p>
            <div class="mt-3 space-y-2">
              <input v-model="form.hero_secondary_label" type="text" class="form-input" placeholder="See case studies">
              <input v-model="form.hero_secondary_href" type="text" class="form-input" placeholder="/portfolio">
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Tertiary text link</p>
            <div class="mt-3 space-y-2">
              <input v-model="form.hero_tertiary_label" type="text" class="form-input" placeholder="View services">
              <input v-model="form.hero_tertiary_href" type="text" class="form-input" placeholder="/services">
            </div>
          </div>
        </div>
      </section>

      <!-- How we work panel -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold text-slate-900">&ldquo;How we work&rdquo; glass panel</h2>
        <p class="mt-1 text-xs text-slate-500">The decorative card on the right side of the hero with three nodes and a center badge.</p>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Eyebrow</label>
            <input v-model="form.panel_eyebrow" type="text" class="form-input" placeholder="How we work">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.panel_description" rows="2" class="form-textarea" />
          </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-5">
          <div>
            <label class="block text-xs font-medium text-slate-600">Node 1 (top)</label>
            <input v-model="form.panel_node_one_label" type="text" class="form-input" placeholder="Craft">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600">Node 2 (left)</label>
            <input v-model="form.panel_node_two_label" type="text" class="form-input" placeholder="Strategy">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600">Node 3 (right)</label>
            <input v-model="form.panel_node_three_label" type="text" class="form-input" placeholder="Engineering">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600">Center badge</label>
            <input v-model="form.panel_center_label" type="text" class="form-input" placeholder="Creafynest">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-600">Footer label</label>
            <input v-model="form.panel_footer_label" type="text" class="form-input" placeholder="One studio · long arcs">
          </div>
        </div>
      </section>

      <!-- Who we are -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold text-slate-900">&ldquo;Who we are&rdquo; section</h2>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.who_heading" type="text" class="form-input" placeholder="Who we are">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.who_description" rows="4" class="form-textarea" />
          </div>
        </div>
      </section>

      <!-- Pillars repeater -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <div>
            <h2 class="text-base font-semibold text-slate-900">Pillars (cards next to &ldquo;Who we are&rdquo;)</h2>
            <p class="mt-1 text-xs text-slate-500">
              The icon class accepts Tailwind gradient stops, e.g.
              <code>from-blue-500 via-indigo-500 to-violet-600</code>.
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50" @click="addPillar">
            <Plus class="h-4 w-4" /> Add pillar
          </button>
        </div>

        <p v-if="!form.pillars.length" class="mt-4 text-sm italic text-slate-500">
          No pillars yet — click &ldquo;Add pillar&rdquo;.
        </p>

        <ul class="mt-5 space-y-4">
          <li v-for="(pillar, i) in form.pillars" :key="i" class="rounded-lg border border-slate-200 bg-slate-50/40 p-4">
            <div class="flex items-start gap-4">
              <div class="flex flex-1 flex-col gap-3">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Title</label>
                    <input v-model="pillar.title" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorAt('pillars', i, 'title') }" placeholder="Full-stack delivery">
                    <p v-if="errorAt('pillars', i, 'title')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorAt('pillars', i, 'title') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Icon gradient class</label>
                    <div class="flex items-center gap-2">
                      <input v-model="pillar.icon_class" type="text" class="form-input" placeholder="from-blue-500 via-indigo-500 to-violet-600">
                      <span
                        :class="['inline-flex h-9 w-9 shrink-0 rounded-lg bg-gradient-to-br ring-1 ring-black/5', pillar.icon_class || 'from-slate-300 to-slate-500']"
                        aria-hidden="true"
                      />
                    </div>
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600">Description</label>
                    <textarea v-model="pillar.description" rows="2" class="form-textarea" />
                  </div>
                </div>

                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                  <input v-model="pillar.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                  Visible on the page
                </label>
              </div>

              <div class="flex shrink-0 flex-col gap-1">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === 0" aria-label="Move up" @click="moveItem(form.pillars, i, -1)">
                  <ArrowUp class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === form.pillars.length - 1" aria-label="Move down" @click="moveItem(form.pillars, i, 1)">
                  <ArrowDown class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove pillar" @click="removePillar(i)">
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </li>
        </ul>
      </section>

      <!-- Why work with us -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold text-slate-900">&ldquo;Why work with us&rdquo; section</h2>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.why_heading" type="text" class="form-input" placeholder="Why work with us">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.why_description" rows="3" class="form-textarea" />
          </div>
        </div>
      </section>

      <!-- Value items repeater -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-end justify-between gap-3">
          <div>
            <h2 class="text-base font-semibold text-slate-900">Value items</h2>
            <p class="mt-1 text-xs text-slate-500">
              The three cards under &ldquo;Why work with us&rdquo;. Icon accepts a Lucide PascalCase name (e.g.
              <code>Brain</code>, <code>Heart</code>, <code>FileBarChart</code>, <code>Sparkles</code>).
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50" @click="addValueItem">
            <Plus class="h-4 w-4" /> Add item
          </button>
        </div>

        <p v-if="!form.value_items.length" class="mt-4 text-sm italic text-slate-500">
          No items yet — click &ldquo;Add item&rdquo;.
        </p>

        <ul class="mt-5 space-y-4">
          <li v-for="(item, i) in form.value_items" :key="i" class="rounded-lg border border-slate-200 bg-slate-50/40 p-4">
            <div class="flex items-start gap-4">
              <div class="flex flex-1 flex-col gap-3">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Title</label>
                    <input v-model="item.title" type="text" class="form-input" :class="{ 'form-input-invalid': !!errorAt('value_items', i, 'title') }" placeholder="Thoughtful AI">
                    <p v-if="errorAt('value_items', i, 'title')" class="mt-1 text-xs text-rose-600" role="alert">{{ errorAt('value_items', i, 'title') }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-slate-600">Lucide icon</label>
                    <input v-model="item.icon" type="text" class="form-input" placeholder="Brain">
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-slate-600">Description</label>
                    <textarea v-model="item.description" rows="2" class="form-textarea" />
                  </div>
                </div>

                <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                  <input v-model="item.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
                  Visible on the page
                </label>
              </div>

              <div class="flex shrink-0 flex-col gap-1">
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === 0" aria-label="Move up" @click="moveItem(form.value_items, i, -1)">
                  <ArrowUp class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 disabled:opacity-40" :disabled="i === form.value_items.length - 1" aria-label="Move down" @click="moveItem(form.value_items, i, 1)">
                  <ArrowDown class="h-4 w-4" />
                </button>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-rose-600 hover:bg-rose-50" aria-label="Remove item" @click="removeValueItem(i)">
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </div>
          </li>
        </ul>
      </section>

      <!-- Closing CTA -->
      <section class="rounded-xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-semibold text-slate-900">Closing CTA</h2>
          <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
            <input v-model="form.cta_is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
            Visible
          </label>
        </div>
        <p class="mt-1 text-xs text-slate-500">The navy box at the bottom of the page with a red button.</p>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-slate-700">Heading</label>
            <input v-model="form.cta_heading" type="text" class="form-input" placeholder="Ready to talk about your next build?">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Button label</label>
            <input v-model="form.cta_button_label" type="text" class="form-input" placeholder="Contact Creafynest">
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea v-model="form.cta_description" rows="2" class="form-textarea" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Button link / anchor</label>
            <input v-model="form.cta_button_href" type="text" class="form-input" placeholder="/contact">
          </div>
        </div>
      </section>

      <div class="sticky bottom-0 -mx-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white/90 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-400">
          Publish about page (uncheck to fall back to the static defaults)
        </label>
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:opacity-60" :disabled="form.processing">
          Save
        </button>
      </div>
    </form>
  </div>
</template>
