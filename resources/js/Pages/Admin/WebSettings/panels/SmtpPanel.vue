<script setup>
import { useForm } from '@inertiajs/vue3'
import { Mail } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../../../Components/Admin/AdminFormErrorBanner.vue'

const props = defineProps({
  smtp: { type: Object, required: true },
})

const form = useForm({
  return_to: 'web-settings',
  smtp_host: props.smtp.smtp_host ?? '',
  smtp_port: props.smtp.smtp_port ?? '',
  smtp_username: props.smtp.smtp_username ?? '',
  smtp_password: '',
  smtp_encryption: props.smtp.smtp_encryption ?? 'tls',
  mail_from_address: props.smtp.mail_from_address ?? '',
  mail_from_name: props.smtp.mail_from_name ?? '',
})

function save() {
  form.put('/admin/web-settings/smtp', { preserveScroll: true })
}

defineExpose({ save })
</script>

<template>
  <form class="space-y-6" @submit.prevent="save">
    <AdminFormErrorBanner :form="form" />

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex items-center gap-2">
        <Mail class="h-5 w-5 text-violet-600" />
        <h2 class="text-lg font-semibold text-slate-900">SMTP &amp; email</h2>
      </div>
      <p class="mt-2 text-sm text-slate-600">
        Overrides Laravel mail config when a host is set. Leave password blank to keep the current value.
        <span v-if="smtp.has_password" class="font-medium text-emerald-700">Password is stored.</span>
      </p>

      <div class="mt-6 grid max-w-2xl grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <label class="form-label" for="host">SMTP host</label>
          <input id="host" v-model="form.smtp_host" type="text" class="form-input" placeholder="smtp.mailgun.org">
        </div>
        <div>
          <label class="form-label" for="port">Port</label>
          <input id="port" v-model="form.smtp_port" type="number" class="form-input" placeholder="587">
        </div>
        <div>
          <label class="form-label" for="enc">Encryption</label>
          <select id="enc" v-model="form.smtp_encryption" class="form-select">
            <option value="">None</option>
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
          </select>
        </div>
        <div>
          <label class="form-label" for="user">Username</label>
          <input id="user" v-model="form.smtp_username" type="text" class="form-input" autocomplete="off">
        </div>
        <div>
          <label class="form-label" for="pass">Password</label>
          <input id="pass" v-model="form.smtp_password" type="password" class="form-input" autocomplete="new-password" placeholder="••••••••">
        </div>
        <div>
          <label class="form-label" for="from-email">From address</label>
          <input id="from-email" v-model="form.mail_from_address" type="email" class="form-input">
        </div>
        <div>
          <label class="form-label" for="from-name">From name</label>
          <input id="from-name" v-model="form.mail_from_name" type="text" class="form-input">
        </div>
      </div>
    </div>
  </form>
</template>
