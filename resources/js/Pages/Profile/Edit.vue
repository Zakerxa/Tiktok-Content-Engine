<!--
  HEADS UP:

  1. UpdateProfileInformationForm is no longer used/imported — there's nothing
     left to edit, so the editable-name form is gone. If nothing else in your
     app imports it, you can delete Partials/UpdateProfileInformationForm.vue
     entirely; if something else still uses it, leave it.

  2. This reads `page.props.auth.user` directly and expects the fields from
     your User model: username, email, email_verified_at, google_id, avatar,
     role_name, is_active, recap_limit, total_recap_used, plan_expires_at.
     Make sure your HandleInertiaRequests middleware actually shares all of
     these on the `auth.user` object (Laravel hides fields listed in
     `$hidden` automatically, but anything NOT in $hidden and present on the
     model gets serialized — just confirm none of these are being filtered
     out before they reach Inertia).

  3. Email verification resend still works the normal Breeze way
     (POST to route('verification.send')) — that's a status action, not
     "editing", so I kept it rather than ripping it out with the name field.

  4. avatar is rendered as <img :src="user.avatar">, assuming it's already a
     full/storage URL. If it's a raw filename, you'll want to prefix it with
     your storage path first.
-->
<script setup>
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppSidebar from '@/Components/AppSidebar.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Google-linked accounts don't have a local password, so there's nothing
// for UpdatePasswordForm to update — we show an explainer card instead.
const isGoogleAccount = computed(() => !!user.value?.google_id);

const initials = computed(() => {
    const name = user.value?.username || '';
    return name.slice(0, 2).toUpperCase() || '?';
});

const roleLabel = computed(() => {
    const role = user.value?.role_name;
    if (!role) return 'Member';
    return role.charAt(0).toUpperCase() + role.slice(1);
});

const formattedPlanExpiry = computed(() => {
    const date = user.value?.plan_expires_at;
    if (!date) return 'No expiry';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
});

const usagePct = computed(() => {
    const limit = user.value?.recap_limit;
    const used  = user.value?.total_recap_used ?? 0;
    if (!limit) return 0;
    return Math.min(100, (used / limit) * 100);
});

function resendVerification() {
    router.post(route('verification.send'));
}
</script>

<template>
    <Head title="Profile" />

    <AppSidebar :auth="$page.props.auth">

          <div class="dash-root">
    <!-- Ambient brand orbs -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

        <div class="py-10 px-4 sm:px-8">
            <div class="mx-auto max-w-3xl">

                <!-- Page header -->
                <div class="mb-8 mt-10">
                    <h1 class="text-2xl font-extrabold text-[#F1F5F9] tracking-tight">Account Settings</h1>
                    <p class="text-sm text-[#94A3B8] mt-1">Manage your profile, password, and account.</p>
                </div>

                <div class="flex flex-col gap-6">
                    <!-- Profile information (read-only) -->
                    <div class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-6 sm:p-8">
                        <h2 class="text-lg font-bold text-[#F1F5F9] mb-1">Profile Information</h2>
                        <p class="text-sm text-[#64748B] mb-6">This is what we have on file for your account.</p>

                        <div class="flex items-center gap-4 mb-6">
                            <img
                                v-if="user?.avatar"
                                :src="user.avatar"
                                class="w-16 h-16 rounded-full object-cover border border-[rgba(255,255,255,0.1)]"
                                alt="Avatar"
                            />
                            <div
                                v-else
                                class="w-16 h-16 rounded-full bg-[rgba(124,58,237,0.15)] text-[#A78BFA] flex items-center justify-center text-xl font-bold border border-[rgba(124,58,237,0.3)]"
                            >{{ initials }}</div>
                            <div>
                                <p class="text-base font-bold text-[#F1F5F9]">{{ user?.username }}</p>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#A78BFA] bg-[rgba(124,58,237,0.15)] px-2 py-0.5 rounded-full mt-1">{{ roleLabel }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-xl p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-1">Email</p>
                                <p class="text-[#F1F5F9] font-medium break-all">{{ user?.email }}</p>
                                <span v-if="mustVerifyEmail && !user?.email_verified_at" class="inline-block mt-2 text-xs font-semibold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full">Unverified</span>
                                <span v-else-if="mustVerifyEmail" class="inline-block mt-2 text-xs font-semibold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Verified</span>
                            </div>

                            <div class="bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-xl p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-1">Sign-in Method</p>
                                <p class="text-[#F1F5F9] font-medium">{{ isGoogleAccount ? 'Google' : 'Email & Password' }}</p>
                            </div>

                            <div class="bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-xl p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-1">Account Status</p>
                                <p class="flex items-center gap-2 text-[#F1F5F9] font-medium">
                                    <span :class="['w-2 h-2 rounded-full', user?.is_active ? 'bg-emerald-400' : 'bg-red-400']"></span>
                                    {{ user?.is_active ? 'Active' : 'Inactive' }}
                                </p>
                            </div>

                            <div class="bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-xl p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#64748B] mb-1">Plan Expires</p>
                                <p class="text-[#F1F5F9] font-medium">{{ formattedPlanExpiry }}</p>
                            </div>
                        </div>

                        <div class="mt-4 bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#64748B]">Recap Usage</p>
                                <p class="text-xs font-mono text-[#A78BFA]">{{ user?.total_recap_used ?? 0 }} / {{ user?.recap_limit ?? '∞' }}</p>
                            </div>
                            <div v-if="user?.recap_limit" class="w-full bg-[rgba(255,255,255,0.08)] rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#7C3AED] to-[#06B6D4]" :style="`width:${usagePct}%`"></div>
                            </div>
                        </div>

                        <div v-if="mustVerifyEmail && !user?.email_verified_at" class="mt-5 text-sm text-[#94A3B8]">
                            Need a new verification link?
                            <button type="button" @click="resendVerification" class="text-[#A78BFA] font-semibold bg-transparent border-none cursor-pointer hover:underline">Resend verification email</button>
                        </div>
                        <p v-if="status === 'verification-link-sent'" class="mt-3 text-sm font-medium text-emerald-400">
                            A new verification link has been sent to your email address.
                        </p>
                    </div>

                    <!-- Password -->
                    <div class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-6 sm:p-8">
                        <template v-if="!isGoogleAccount">
                            <UpdatePasswordForm class="max-w-xl" />
                        </template>
                        <template v-else>
                            <div class="flex items-start gap-4">
                                <span class="w-11 h-11 shrink-0 rounded-xl bg-[rgba(124,58,237,0.15)] flex items-center justify-center text-xl">🔐</span>
                                <div>
                                    <h3 class="text-base font-bold text-[#F1F5F9] mb-1">Signed in with Google</h3>
                                    <p class="text-sm text-[#94A3B8] leading-relaxed">
                                        Your account uses Google Sign-In, so there's no Z.A.K.E.R.X.A password to
                                        update here. To change how you sign in, manage it from your
                                        <a href="https://myaccount.google.com/security" target="_blank" rel="noopener" class="text-[#A78BFA] no-underline hover:underline">Google Account settings</a>.
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Delete account -->
                    <div class="bg-[rgba(239,68,68,0.04)] border border-red-500/20 rounded-2xl p-6 sm:p-8">
                        <DeleteUserForm class="max-w-xl" />
                    </div>

                </div>
            </div>
        </div>
    </div>
    </AppSidebar>
</template>