<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AdminFormErrorBanner from '../../../Components/Admin/AdminFormErrorBanner.vue'
import AdminPageHeader from '../../../Components/Admin/AdminPageHeader.vue'
import AdminSectionCard from '../../../Components/Admin/AdminSectionCard.vue'

const props = defineProps({
  profile: { type: Object, required: true },
})

const profileForm = useForm({
  name: props.profile.name ?? '',
  email: props.profile.email ?? '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

function submitProfile() {
  profileForm.put('/admin/profile', { preserveScroll: true })
}

function submitPassword() {
  passwordForm.put('/admin/profile/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

function err(form, path) {
  return form.errors[path]
}
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Profile"
      subtitle="Update your account name, email, and password. These apply only to your admin login."
    >
      <template #actions>
        <Link href="/admin" class="admin-btn-secondary">Back to dashboard</Link>
      </template>
    </AdminPageHeader>

    <form class="space-y-6" @submit.prevent="submitProfile">
      <AdminFormErrorBanner :form="profileForm" />

      <AdminSectionCard title="Account" description="Shown in the admin header and used to sign in.">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="form-label" for="profile-name">Name</label>
            <input
              id="profile-name"
              v-model="profileForm.name"
              type="text"
              class="form-input"
              :class="{ 'form-input-invalid': !!err(profileForm, 'name') }"
              autocomplete="name"
            >
            <p v-if="err(profileForm, 'name')" class="mt-1 text-xs text-rose-600" role="alert">
              {{ err(profileForm, 'name') }}
            </p>
          </div>
          <div>
            <label class="form-label" for="profile-email">Email</label>
            <input
              id="profile-email"
              v-model="profileForm.email"
              type="email"
              class="form-input"
              :class="{ 'form-input-invalid': !!err(profileForm, 'email') }"
              autocomplete="email"
            >
            <p v-if="err(profileForm, 'email')" class="mt-1 text-xs text-rose-600" role="alert">
              {{ err(profileForm, 'email') }}
            </p>
          </div>
        </div>
        <div class="mt-5">
          <button type="submit" class="admin-btn-primary" :disabled="profileForm.processing">
            Save account
          </button>
        </div>
      </AdminSectionCard>
    </form>

    <form class="space-y-6" @submit.prevent="submitPassword">
      <AdminFormErrorBanner :form="passwordForm" />

      <AdminSectionCard title="Password" description="Choose a strong password you do not use elsewhere.">
        <div class="grid max-w-xl grid-cols-1 gap-4">
          <div>
            <label class="form-label" for="current-password">Current password</label>
            <input
              id="current-password"
              v-model="passwordForm.current_password"
              type="password"
              class="form-input"
              :class="{ 'form-input-invalid': !!err(passwordForm, 'current_password') }"
              autocomplete="current-password"
            >
            <p v-if="err(passwordForm, 'current_password')" class="mt-1 text-xs text-rose-600" role="alert">
              {{ err(passwordForm, 'current_password') }}
            </p>
          </div>
          <div>
            <label class="form-label" for="new-password">New password</label>
            <input
              id="new-password"
              v-model="passwordForm.password"
              type="password"
              class="form-input"
              :class="{ 'form-input-invalid': !!err(passwordForm, 'password') }"
              autocomplete="new-password"
            >
            <p v-if="err(passwordForm, 'password')" class="mt-1 text-xs text-rose-600" role="alert">
              {{ err(passwordForm, 'password') }}
            </p>
          </div>
          <div>
            <label class="form-label" for="confirm-password">Confirm new password</label>
            <input
              id="confirm-password"
              v-model="passwordForm.password_confirmation"
              type="password"
              class="form-input"
              autocomplete="new-password"
            >
          </div>
        </div>
        <div class="mt-5">
          <button type="submit" class="admin-btn-primary" :disabled="passwordForm.processing">
            Update password
          </button>
        </div>
      </AdminSectionCard>
    </form>
  </div>
</template>
