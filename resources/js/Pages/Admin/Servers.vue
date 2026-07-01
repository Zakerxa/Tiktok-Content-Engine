<template>

  <AppSidebar :auth="$page.props.auth">
   <div class="admin-page">
    <!-- Header -->
    <div class="page-header">
      <div>
        <p class="eyebrow">ADMIN PANEL</p>
        <h1 class="title">Servers</h1>
      </div>
      <div class="tabs">
        <Link :href="route('admin.index')" class="tab">Dashboard</Link>
        <Link :href="route('admin.users')" class="tab">Users</Link>
        <Link :href="route('admin.roles')" class="tab">Roles</Link>
        <Link :href="route('admin.servers')" class="tab tab-active">Servers</Link>
      </div>
    </div>

    <!-- Server table -->
    <div class="panel">
      <div class="panel-head">
        <div>
          <p class="eyebrow">SERVER MANAGEMENT</p>
          <h2 class="panel-title">All Servers</h2>
        </div>
        <button class="btn-primary" @click="showAddModal = true">+ Add Server</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>NAME</th>
              <th>URL</th>
              <th>PRIORITY</th>
              <th>ROLE ACCESS</th>
              <th>STATUS</th>
              <th>LOAD</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="server in localServers" :key="server.id">
              <td class="cell-name">{{ server.name }}</td>
              <td class="cell-url">{{ server.url }}</td>
              <td>{{ server.priority }}</td>
              <td>
                <span v-if="!server.role_access?.length" class="badge badge-all">All</span>
                <span v-for="role in server.role_access" :key="role" :class="['badge', roleBadgeClass(role)]">
                  {{ role }}
                </span>
              </td>
              <td>
                <span :class="['badge', server.is_active ? 'badge-active' : 'badge-inactive']">
                  <span class="dot"></span>{{ server.is_active ? 'Active' : 'Maintenance' }}
                </span>
              </td>
              <td>
                <span v-if="server.is_busy" class="badge badge-busy">Busy ({{ server.processing_count }})</span>
                <span v-else class="badge badge-free">Free</span>
                <span v-if="server.is_stuck" class="badge badge-stuck" title="30 min ကျော်နေပြီ">⚠ Stuck</span>
              </td>
              <td class="cell-actions">
                <button class="link-btn" @click="editServer(server)">Edit</button>
                <button class="link-btn link-danger" @click="deleteServer(server.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || editingServer" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h3>{{ editingServer ? 'Server ပြင်မည်' : 'Server အသစ်ထည့်မည်' }}</h3>

        <div class="field">
          <label>Server Name (key)</label>
          <input v-model="form.name" placeholder="v4" />
        </div>

        <div class="field">
          <label>URL</label>
          <input v-model="form.url" placeholder="https://your-server.hf.space" />
        </div>

        <div class="field">
          <label>Priority</label>
          <select v-model.number="form.priority">
            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="field">
          <label>Role Access (ဘာမှ မရွေးရင် Role အားလုံးသုံးလို့ရမည်)</label>
          <div class="checkbox-group">
            <label v-for="role in roleOptions" :key="role" class="checkbox-pill">
              <input type="checkbox" :value="role" v-model="form.role_access" />
              {{ role }}
            </label>
          </div>
        </div>

        <div class="field">
          <label>Status</label>
          <select v-model="form.is_active">
            <option :value="true">Active</option>
            <option :value="false">Maintenance</option>
          </select>
        </div>

        <div class="modal-actions">
          <button class="btn-ghost" @click="closeModal">Cancel</button>
          <button class="btn-primary" @click="submitServer">Save</button>
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


const props = defineProps({ servers: Array ,stats: Object})

const roleOptions = ['tester', 'normal', 'pro', 'admin']
const localServers = reactive(props.servers.map(s => ({ ...s, role_access: s.role_access || [] })))

const showAddModal = ref(false)
const editingServer = ref(null)
const form = reactive({ name: '', url: '', priority: 1, role_access: [], is_active: true })

function roleBadgeClass(role) {
  return { tester: 'badge-tester', normal: 'badge-normal', pro: 'badge-pro', admin: 'badge-admin' }[role] || 'badge-all'
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
.admin-page { padding: 32px; max-width: 1200px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
.eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; color: #A78BFA; text-transform: uppercase; margin-bottom: 4px; }
.title { font-size: 26px; font-weight: 800; color: #F1F5F9; }
.tabs { display: flex; gap: 6px; background: rgba(255,255,255,0.03); padding: 4px; border-radius: 12px; }
.tab { padding: 8px 18px; border-radius: 9px; color: #94A3B8; font-size: 13px; font-weight: 600; text-decoration: none; }
.tab-active { background: #7C3AED; color: #fff; }

.panel { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px; }
.panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
.panel-title { font-size: 19px; font-weight: 700; color: #F1F5F9; margin-top: 2px; }

table { width: 100%; border-collapse: collapse; }
thead th { text-align: left; font-size: 11px; color: #64748B; font-weight: 700; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.08); }
tbody td { padding: 14px 12px; font-size: 13px; color: #E2E8F0; border-bottom: 1px solid rgba(255,255,255,0.05); }
.cell-name { font-weight: 700; }
.cell-url { color: #94A3B8; font-family: monospace; font-size: 12px; }
.cell-actions { display: flex; gap: 12px; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-right: 4px; }
.badge-tester { background: rgba(148,163,184,0.15); color: #CBD5E1; }
.badge-normal { background: rgba(6,182,212,0.15); color: #22D3EE; }
.badge-pro    { background: rgba(124,58,237,0.18); color: #A78BFA; }
.badge-admin  { background: rgba(239,68,68,0.15); color: #F87171; }
.badge-all    { background: rgba(255,255,255,0.06); color: #94A3B8; }
.badge-active { background: rgba(34,197,94,0.15); color: #4ADE80; }
.badge-inactive { background: rgba(148,163,184,0.12); color: #94A3B8; }
.badge-busy   { background: rgba(245,158,11,0.15); color: #FBBF24; }
.badge-free   { background: rgba(34,197,94,0.15); color: #4ADE80; }
.badge-stuck  { background: rgba(239,68,68,0.18); color: #F87171; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.link-btn { background: none; border: none; color: #A78BFA; font-size: 12px; font-weight: 600; cursor: pointer; }
.link-danger { color: #F87171; }

.btn-primary { padding: 9px 18px; border-radius: 10px; border: none; cursor: pointer; background: linear-gradient(135deg, #7C3AED, #06B6D4); color: #fff; font-weight: 700; font-size: 13px; }
.btn-ghost { padding: 9px 18px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94A3B8; cursor: pointer; font-size: 13px; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 50; }
.modal { background: #0F1320; border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 28px; width: 420px; }
.modal h3 { font-size: 17px; font-weight: 700; color: #F1F5F9; margin-bottom: 18px; }
.field { margin-bottom: 14px; display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 12px; font-weight: 600; color: #94A3B8; }
.field input, .field select { padding: 9px 12px; border-radius: 9px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: #F1F5F9; font-size: 13px; }
.checkbox-group { display: flex; gap: 8px; flex-wrap: wrap; }
.checkbox-pill { display: flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 16px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); font-size: 12px; cursor: pointer; }
.modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
</style>