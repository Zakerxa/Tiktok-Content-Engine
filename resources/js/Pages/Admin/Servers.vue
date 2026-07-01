<template>
  <AppSidebar :auth="$page.props.auth">
    <div class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
        <div>
          <p class="text-[11px] font-bold tracking-widest text-violet-400 uppercase mb-1">Admin Panel</p>
          <h1 class="text-2xl sm:text-[26px] font-extrabold text-slate-100">Servers</h1>
        </div>

        <!-- Tabs: scrollable on mobile -->
        <div class="flex gap-1.5 bg-white/[0.03] p-1 rounded-xl overflow-x-auto no-scrollbar">
          <Link :href="route('admin.index')" class="shrink-0 px-4 py-2 rounded-lg text-slate-400 text-[13px] font-semibold whitespace-nowrap">Dashboard</Link>
          <Link :href="route('admin.users')" class="shrink-0 px-4 py-2 rounded-lg text-slate-400 text-[13px] font-semibold whitespace-nowrap">Users</Link>
          <Link :href="route('admin.roles')" class="shrink-0 px-4 py-2 rounded-lg text-slate-400 text-[13px] font-semibold whitespace-nowrap">Roles</Link>
          <Link :href="route('admin.servers')" class="shrink-0 px-4 py-2 rounded-lg bg-violet-600 text-white text-[13px] font-semibold whitespace-nowrap">Servers</Link>
        </div>
      </div>

      <!-- Panel -->
      <div class="bg-white/[0.03] border border-white/[0.08] rounded-2xl p-4 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
          <div>
            <p class="text-[11px] font-bold tracking-widest text-violet-400 uppercase mb-1">Server Management</p>
            <h2 class="text-[17px] sm:text-[19px] font-bold text-slate-100">All Servers</h2>
          </div>
          <button
            @click="showAddModal = true"
            class="w-full sm:w-auto px-4 py-2.5 rounded-xl border-none cursor-pointer bg-gradient-to-br from-violet-600 to-cyan-500 text-white font-bold text-[13px]"
          >
            + Add Server
          </button>
        </div>

        <!-- ─── Desktop table (md and up) ─── -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Name</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">URL</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Priority</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Role Access</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Status</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Load</th>
                <th class="text-left text-[11px] text-slate-500 font-bold tracking-wide px-3 py-2.5 border-b border-white/[0.08]">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="server in localServers" :key="server.id">
                <td class="px-3 py-3.5 text-[13px] text-slate-200 font-bold border-b border-white/[0.05]">{{ server.name }}</td>
                <td class="px-3 py-3.5 text-[12px] text-slate-400 font-mono border-b border-white/[0.05] max-w-[220px] truncate">{{ server.url }}</td>
                <td class="px-3 py-3.5 text-[13px] text-slate-200 border-b border-white/[0.05]">{{ server.priority }}</td>
                <td class="px-3 py-3.5 border-b border-white/[0.05]">
                  <span v-if="!server.role_access?.length" :class="badgeClass('all')">All</span>
                  <span v-for="role in server.role_access" :key="role" :class="badgeClass(role)">{{ role }}</span>
                </td>
                <td class="px-3 py-3.5 border-b border-white/[0.05]">
                  <span :class="badgeClass(server.is_active ? 'active' : 'inactive')">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ server.is_active ? 'Active' : 'Maintenance' }}
                  </span>
                </td>
                <td class="px-3 py-3.5 border-b border-white/[0.05]">
                  <span v-if="server.is_busy" :class="badgeClass('busy')">Busy ({{ server.processing_count }})</span>
                  <span v-else :class="badgeClass('free')">Free</span>
                  <span v-if="server.is_stuck" :class="badgeClass('stuck')" title="30 min ကျော်နေပြီ">⚠ Stuck</span>
                </td>
                <td class="px-3 py-3.5 border-b border-white/[0.05]">
                  <div class="flex gap-3">
                    <button @click="editServer(server)" class="bg-transparent border-none text-violet-400 text-[12px] font-semibold cursor-pointer">Edit</button>
                    <button @click="deleteServer(server.id)" class="bg-transparent border-none text-red-400 text-[12px] font-semibold cursor-pointer">Delete</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ─── Mobile cards (below md) ─── -->
        <div class="md:hidden flex flex-col gap-3">
          <div
            v-for="server in localServers"
            :key="server.id"
            class="bg-white/[0.03] border border-white/[0.08] rounded-xl p-4"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="text-[15px] font-bold text-slate-100">{{ server.name }}</span>
              <span :class="badgeClass(server.is_active ? 'active' : 'inactive')">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                {{ server.is_active ? 'Active' : 'Maintenance' }}
              </span>
            </div>

            <p class="text-[12px] text-slate-400 font-mono mb-3 break-all">{{ server.url }}</p>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[12px] text-slate-400 mb-3">
              <span>Priority: <span class="text-slate-200 font-semibold">{{ server.priority }}</span></span>
              <span v-if="server.is_busy" :class="badgeClass('busy')">Busy ({{ server.processing_count }})</span>
              <span v-else :class="badgeClass('free')">Free</span>
              <span v-if="server.is_stuck" :class="badgeClass('stuck')">⚠ Stuck</span>
            </div>

            <div class="flex flex-wrap gap-1.5 mb-4">
              <span v-if="!server.role_access?.length" :class="badgeClass('all')">All</span>
              <span v-for="role in server.role_access" :key="role" :class="badgeClass(role)">{{ role }}</span>
            </div>

            <div class="flex gap-3 pt-3 border-t border-white/[0.06]">
              <button @click="editServer(server)" class="flex-1 py-2 rounded-lg bg-white/[0.05] text-violet-400 text-[13px] font-semibold">Edit</button>
              <button @click="deleteServer(server.id)" class="flex-1 py-2 rounded-lg bg-white/[0.05] text-red-400 text-[13px] font-semibold">Delete</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <div
        v-if="showAddModal || editingServer"
        @click.self="closeModal"
        class="fixed inset-0 bg-black/60 flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
      >
        <div class="bg-[#0F1320] border border-white/10 rounded-t-2xl sm:rounded-2xl p-6 w-full sm:w-[420px] max-h-[90vh] overflow-y-auto">
          <h3 class="text-[17px] font-bold text-slate-100 mb-4">
            {{ editingServer ? 'Server ပြင်မည်' : 'Server အသစ်ထည့်မည်' }}
          </h3>

          <div class="flex flex-col gap-1.5 mb-3.5">
            <label class="text-[12px] font-semibold text-slate-400">Server Name (key)</label>
            <input v-model="form.name" placeholder="v4" class="px-3 py-2.5 rounded-lg border border-white/10 bg-white/[0.04] text-slate-100 text-[13px]" />
          </div>

          <div class="flex flex-col gap-1.5 mb-3.5">
            <label class="text-[12px] font-semibold text-slate-400">URL</label>
            <input v-model="form.url" placeholder="https://your-server.hf.space" class="px-3 py-2.5 rounded-lg border border-white/10 bg-white/[0.04] text-slate-100 text-[13px]" />
          </div>

          <div class="flex flex-col gap-1.5 mb-3.5">
            <label class="text-[12px] font-semibold text-slate-400">Priority</label>
            <select v-model.number="form.priority" class="px-3 py-2.5 rounded-lg border border-white/10 bg-white/[0.04] text-slate-100 text-[13px]">
              <option v-for="n in 5" :key="n" :value="n" class="bg-[#0F1320] text-slate-100">{{ n }}</option>
            </select>
          </div>

          <div class="flex flex-col gap-1.5 mb-3.5">
            <label class="text-[12px] font-semibold text-slate-400">Role Access (ဘာမှ မရွေးရင် Role အားလုံးသုံးလို့ရမည်)</label>
            <div class="flex gap-2 flex-wrap">
              <label
                v-for="role in roleOptions"
                :key="role"
                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-white/[0.04] border border-white/[0.08] text-[12px] cursor-pointer text-slate-200"
              >
                <input type="checkbox" :value="role" v-model="form.role_access" class="accent-violet-500" />
                {{ role }}
              </label>
            </div>
          </div>

          <div class="flex flex-col gap-1.5 mb-3.5">
            <label class="text-[12px] font-semibold text-slate-400">Status</label>
            <select v-model="form.is_active" class="px-3 py-2.5 rounded-lg border border-white/10 bg-white/[0.04] text-slate-100 text-[13px]">
              <option :value="true" class="bg-[#0F1320] text-slate-100">Active</option>
              <option :value="false" class="bg-[#0F1320] text-slate-100">Maintenance</option>
            </select>
          </div>

          <div class="flex flex-col-reverse sm:flex-row justify-end gap-2.5 mt-5">
            <button @click="closeModal"
              class="px-4 py-2.5 rounded-lg border border-white/10 bg-transparent text-slate-400 text-[13px]">
              Cancel
            </button>
            <button @click="submitServer"
              class="px-4 py-2.5 rounded-lg border-none bg-gradient-to-br from-violet-600 to-cyan-500 text-white font-bold text-[13px]">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppSidebar>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppSidebar from '@/Components/AppSidebar.vue'

const props = defineProps({ servers: Array, stats: Object })

const roleOptions = ['tester', 'normal', 'pro', 'admin']
const localServers = reactive(props.servers.map(s => ({ ...s, role_access: s.role_access || [] })))

const showAddModal = ref(false)
const editingServer = ref(null)
const form = reactive({ name: '', url: '', priority: 1, role_access: [], is_active: true })

const badgeStyles = {
  tester:   'bg-slate-400/15 text-slate-300',
  normal:   'bg-cyan-500/15 text-cyan-400',
  pro:      'bg-violet-600/[0.18] text-violet-400',
  admin:    'bg-red-500/15 text-red-400',
  all:      'bg-white/[0.06] text-slate-400',
  active:   'bg-green-500/15 text-green-400',
  inactive: 'bg-slate-400/[0.12] text-slate-400',
  busy:     'bg-amber-500/15 text-amber-400',
  free:     'bg-green-500/15 text-green-400',
  stuck:    'bg-red-500/[0.18] text-red-400',
}

function badgeClass(key) {
  return `inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold mr-1 ${badgeStyles[key] || badgeStyles.all}`
}

function editServer(server) {
  editingServer.value = server
  Object.assign(form, { ...server })
}

function closeModal() {
  showAddModal.value = false
  editingServer.value = null
  Object.assign(form, { name: '', url: '', priority: 1, role_access: [], is_active: true })
}

function submitServer() {
  if (editingServer.value) {
    useForm(form).post(`/admin/servers/${editingServer.value.id}`, { onSuccess: closeModal })
  } else {
    useForm(form).post('/admin/servers', { onSuccess: closeModal })
  }
}

function deleteServer(id) {
  if (confirm('ဒီ server ကို ဖျက်ပါမလား?')) {
    useForm({}).delete(`/admin/servers/${id}`)
  }
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>