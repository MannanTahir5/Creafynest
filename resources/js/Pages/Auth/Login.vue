<script setup>
import { useForm } from '@inertiajs/vue3'
import { LockKeyhole, Sparkles } from 'lucide-vue-next'
import AdminFormErrorBanner from '../../Components/Admin/AdminFormErrorBanner.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post('/login')
}
</script>

<template>
  <div class="admin-card overflow-hidden shadow-lg shadow-slate-200/60">
    <div class="bg-gradient-to-br from-violet-600 to-indigo-700 px-6 py-8 text-white">
      <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
        <Sparkles class="h-3.5 w-3.5" />
        Admin access
      </div>
      <h1 class="mt-4 text-2xl font-bold tracking-tight">Sign in</h1>
      <p class="mt-2 text-sm text-violet-100/90">Use your credentials to open the control panel.</p>
    </div>

    <form class="space-y-5 p-6" @submit.prevent="submit">
      <AdminFormErrorBanner :form="form" />

      <div>
        <label for="login-email" class="form-label">Email</label>
        <input
          id="login-email"
          v-model="form.email"
          type="email"
          name="email"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.email }"
          autocomplete="username"
        >
        <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.email }}</p>
      </div>

      <div>
        <label for="login-password" class="form-label">Password</label>
        <input
          id="login-password"
          v-model="form.password"
          type="password"
          name="password"
          class="form-input"
          :class="{ 'form-input-invalid': !!form.errors.password }"
          autocomplete="current-password"
        >
        <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600" role="alert">{{ form.errors.password }}</p>
      </div>

      <label class="flex items-center gap-2.5 text-sm text-slate-700">
        <input
          v-model="form.remember"
          type="checkbox"
          class="rounded border-slate-300 text-violet-600 focus:ring-violet-500/30"
        >
        Remember me
      </label>

      <button type="submit" class="admin-btn-primary w-full" :disabled="form.processing">
        <LockKeyhole class="h-4 w-4" />
        {{ form.processing ? 'Signing in…' : 'Sign in' }}
      </button>
    </form>
  </div>
</template>
