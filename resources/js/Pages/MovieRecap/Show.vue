<template>
  <div class="min-h-screen flex flex-col bg-[#080B14] text-[#F1F5F9] font-[Inter,_Segoe_UI,_sans-serif]">

    <AppNavbar :auth="auth" />

    <!-- ══════════════ BEAUTIFUL ALERT MODAL ══════════════ -->
    <transition name="alert-fade">
      <div v-if="showErrorOverlay"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-4"
        @click.self="closeErrorPopup">
        <div class="w-full max-w-sm rounded-2xl shadow-2xl p-6 border" :class="{
          'bg-[#0D1120] border-red-500/25': alertType === 'error',
          'bg-[#0D1120] border-amber-500/25': alertType === 'warning',
          'bg-[#0D1120] border-[#7C3AED]/25': alertType === 'info',
        }">
          <!-- Icon -->
          <div class="flex justify-center mb-4">
            <div v-if="alertType === 'error'"
              class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center">
              <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
            <div v-else-if="alertType === 'warning'"
              class="w-16 h-16 rounded-full bg-amber-500/10 flex items-center justify-center">
              <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
              </svg>
            </div>
            <div v-else class="w-16 h-16 rounded-full bg-[#7C3AED]/10 flex items-center justify-center">
              <svg class="w-8 h-8 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
          <!-- Title -->
          <h3 class="text-center text-lg font-bold text-[#F1F5F9] mb-2">
            <span v-if="alertType === 'error'">လုပ်ဆောင်မှု မအောင်မြင်ပါ</span>
            <span v-else-if="alertType === 'warning'">သတိပေးချက်</span>
            <span v-else>Login လိုအပ်သည်</span>
          </h3>
          <p class="text-center text-sm text-[#94A3B8] mb-6 leading-relaxed">{{ errorPopupMsg }}</p>
          <!-- Buttons -->
          <div class="flex flex-col gap-2">
            <button @click="closeErrorPopup"
              class="w-full bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] hover:opacity-90 text-white font-semibold py-2.5 rounded-xl border-none cursor-pointer transition-opacity">
              နားလည်ပါပြီ
            </button>
            <a v-if="alertType === 'error'" href="/logout"
              class="block text-center w-full bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-[#94A3B8] text-sm font-medium py-2.5 rounded-xl no-underline transition-colors">
              Logout လုပ်မည်
            </a>
          </div>
        </div>
      </div>
    </transition>

    <!-- ══════════════ MAIN CARD ══════════════ -->
    <main class="flex-1 flex items-start justify-center px-3 py-6 mt-12 sm:px-4 sm:py-10">
      <div
        class="w-full max-w-6xl bg-[rgba(255,255,255,0.03)] backdrop-blur-xl rounded-3xl shadow-2xl border border-[rgba(255,255,255,0.08)] px-4 py-5 sm:px-6 sm:py-6 md:px-8">

        <!-- Header -->
        <div
          class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3 mb-8 pb-6 border-b border-[rgba(255,255,255,0.08)]">
          <div
            class="bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] text-white p-3.5 rounded-2xl shadow-lg shadow-[#7C3AED]/30 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2v8a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-extrabold text-[#F1F5F9] tracking-tight">Auto Movie Recap Engine</h1>
            <p class="text-sm font-medium text-[#94A3B8] mt-0.5">AI-Powered Video Transcription &amp; Recap Generator
            </p>
          </div>
        </div>

        <!-- Body Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-[5fr_7fr] gap-6 mb-8">

          <!-- ── LEFT COLUMN ── -->
          <div class="flex flex-col gap-5">

            <!-- Tab switcher -->
            <div class="flex bg-[rgba(255,255,255,0.05)] p-1.5 rounded-xl">
              <button @click="switchTab('upload')" :class="['flex-1 py-2.5 text-sm font-semibold rounded-lg border-none cursor-pointer transition-all',
                activeMode === 'upload'
                  ? 'bg-[rgba(124,58,237,0.12)] text-[#A78BFA] shadow border border-[rgba(124,58,237,0.3)]'
                  : 'bg-transparent text-[#64748B] hover:text-[#94A3B8]']">📤 Upload Video File</button>
              <button @click="switchTab('youtube')" :class="['flex-1 py-2.5 text-sm font-semibold rounded-lg border-none cursor-pointer transition-all',
                activeMode === 'youtube'
                  ? 'bg-[rgba(124,58,237,0.12)] text-[#A78BFA] shadow border border-[rgba(124,58,237,0.3)]'
                  : 'bg-transparent text-[#64748B] hover:text-[#94A3B8]']">🔗 YouTube URL</button>
            </div>

            <!-- Upload mode -->
            <div v-show="activeMode === 'upload'">
              <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">Select Local Video File</label>
              <div ref="uploadZone" v-show="!hasVideoSelected"
                class="border-2 border-dashed border-[rgba(255,255,255,0.15)] rounded-2xl p-6 text-center cursor-pointer relative bg-[rgba(255,255,255,0.02)] hover:border-[#7C3AED] transition-colors"
                :class="{ 'pointer-events-none opacity-60': isProcessing }">
                <div ref="uploadIconArea" class="flex flex-col items-center justify-center py-2 pb-3 text-[#64748B]">
                  <input ref="videoFileInput" type="file" accept="video/mp4,video/x-matroska,video/quicktime,video/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" :disabled="isProcessing"
                    @change="previewVideo" />
                  <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                  </svg>
                  <p ref="uploadPlaceholder" class="text-sm font-semibold">Click to upload or drag &amp; drop video</p>
                </div>
                <p ref="uploadFormats" class="text-xs text-[#64748B] mt-1 opacity-70">MP4, MKV, MOV (Max 2GB)</p>
              </div>

              <!-- Video preview -->
              <div ref="videoPreviewContainer"
                class="mt-3 p-3 rounded-2xl border border-[rgba(255,255,255,0.08)] bg-[rgba(255,255,255,0.03)] justify-center items-center"
                style="display:none">
                <div ref="previewFrame"
                  class="relative w-full max-w-[350px] mx-auto bg-black rounded-xl overflow-hidden shadow-2xl border-4 border-slate-800"
                  style="aspect-ratio:9/16">

                  <button type="button" @click="removeSelectedVideo"
                    class="absolute top-3 right-3 z-50 p-2 rounded-full bg-black/50 hover:bg-red-600 border-none text-white cursor-pointer transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>

                  <video ref="videoPreview" controls class="absolute inset-0 w-full h-full object-cover"></video>

                  <div ref="watermarkLayer" class="absolute inset-0 pointer-events-none z-40 overflow-hidden">
                    <img ref="watermarkLogo" src="" class="absolute pointer-events-auto cursor-grab rounded-sm"
                      alt="Watermark" draggable="false" style="display:none" />
                  </div>

                  <div ref="blurBoxEl"
                    class="absolute border-2 border-dashed border-amber-400 bg-yellow-200/20 cursor-move z-30 select-none"
                    style="display:none; left:0; top:85%; width:100%; height:15%"><span
                      class="absolute top-1 left-1 text-[9px] font-bold text-yellow-300 bg-black/40 px-1 py-px rounded">BLUR</span>
                  </div>

                </div>
              </div>
            </div>

            <!-- YouTube mode -->
            <div v-show="activeMode === 'youtube'">
              <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">YouTube Video URL</label>
              <input ref="urlInput" type="text" placeholder="https://www.youtube.com/watch?v=..."
                class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] outline-none transition-all focus:border-[#7C3AED] focus:ring-4 focus:ring-[#7C3AED]/15 placeholder:text-[#64748B]"
                :disabled="isProcessing" />
            </div>
          </div>

          <!-- ── RIGHT COLUMN ── -->
          <div class="flex flex-col gap-5 mt-2 lg:mt-0">
            <!-- Mobile divider -->
            <div class="lg:hidden border-t border-[rgba(255,255,255,0.08)] pt-4"></div>
            <h3 class="text-lg font-bold text-[#F1F5F9] flex items-center gap-2">⚙️ Processing Settings</h3>

            <div
              class="bg-[rgba(255,255,255,0.02)] rounded-2xl p-4 sm:p-6 border border-[rgba(255,255,255,0.06)] shadow-sm flex flex-col gap-4">

              <!-- Voiceover toggle -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <div class="flex justify-between items-center mb-3">
                  <label class="font-bold text-[#CBD5E1] text-sm">Enable AI 🎙️ Voiceover</label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input ref="enableVoiceover" type="checkbox" class="sr-only peer" @change="toggleVoiceover"
                      :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </label>
                </div>
                <div ref="voiceoverContainer" class="opacity-50 pointer-events-none transition-all">
                  <VoiceSelector v-model="selectedVoice" :isProcessing="isProcessing" />
                </div>
              </div>

              <!-- Blur region -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <label class="font-bold text-[#CBD5E1] text-sm block mb-2">🌀 Blur Region</label>
                <p class="text-xs text-[#64748B] mb-2">Blur box ကို Video Preview ထဲမှာ Drag လုပ်ပြီး position သတ်မှတ်ပါ
                </p>
                <div class="flex flex-wrap gap-2 items-center">
                  <input type="range" id="blur-height" min="5" max="50" value="15" class="w-full accent-[#7C3AED]"
                    @input="setBlurHeight($event.target.value)" :disabled="isProcessing" />
                  <div class="flex flex-wrap gap-2 mt-2">
                    <div
                      class="bg-[rgba(124,58,237,0.12)] px-2 py-1 rounded-md text-xs font-semibold text-[#A78BFA] font-mono">
                      X: <span>{{ blurX.toFixed(2) }}</span>%</div>
                    <div
                      class="bg-[rgba(124,58,237,0.12)] px-2 py-1 rounded-md text-xs font-semibold text-[#A78BFA] font-mono">
                      Y: <span>{{ blurY.toFixed(2) }}</span>%</div>
                    <div
                      class="bg-[rgba(124,58,237,0.12)] px-2 py-1 rounded-md text-xs font-semibold text-[#A78BFA] font-mono">
                      H: <span>{{ blurH.toFixed(2) }}</span>%</div>
                  </div>
                </div>
              </div>

              <!-- Toggles grid -->
              <div class="grid grid-cols-2 gap-3">
                <label class="inline-flex items-center cursor-pointer gap-3">
                  <div class="relative inline-flex items-center">
                    <input ref="enableSubtitles" type="checkbox" class="sr-only peer" :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </div>
                  <span class="text-sm font-medium text-[#CBD5E1]">Auto Subtitles</span>
                </label>
                <label class="inline-flex items-center cursor-pointer gap-3">
                  <div class="relative inline-flex items-center">
                    <input ref="enableFlip" type="checkbox" class="sr-only peer" :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </div>
                  <span class="text-sm font-medium text-[#CBD5E1]">Mirror Mode</span>
                </label>
                <label class="inline-flex items-center cursor-pointer gap-3 col-span-2">
                  <div class="relative inline-flex items-center">
                    <input ref="enableWatermark" type="checkbox" class="sr-only peer" @change="waterMarkToggle"
                      :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </div>
                  <span class="text-sm font-medium text-[#CBD5E1]">Enable Watermark Logo</span>
                </label>
              </div>

              <!-- Watermark upload -->
              <div ref="watermarkUploadSection"
                class="mt-2 p-4 bg-[rgba(255,255,255,0.03)] rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm"
                style="display:none">
                <label class="block text-sm font-semibold text-[#CBD5E1] mb-3">Upload &amp; Drag Logo</label>
                <div class="flex gap-4 items-center">
                  <label
                    class="flex flex-col items-center justify-center w-24 h-24 border-2 border-dashed border-[rgba(255,255,255,0.15)] hover:border-[#7C3AED] hover:bg-[rgba(124,58,237,0.08)] rounded-xl cursor-pointer transition-all group">
                    <div class="flex flex-col items-center text-[#64748B] group-hover:text-[#A78BFA]">
                      <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                      </svg>
                      <span class="text-[10px] font-bold uppercase tracking-wide">Upload</span>
                    </div>
                    <input ref="logoFileInput" type="file" accept="image/png,image/jpeg,image/webp" class="hidden"
                      @change="previewLogo" :disabled="isProcessing" />
                  </label>
                  <div
                    class="flex-1 bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-lg p-3 text-xs text-[#64748B]">
                    <p class="font-semibold text-[#CBD5E1] mb-1">Logo Position</p>
                    <p>Upload a logo, then drag it around the video player to set position.</p>
                    <div class="flex gap-3 font-mono text-[#A78BFA] font-bold mt-2">
                      <div class="bg-[rgba(124,58,237,0.12)] px-2 py-1 rounded-md text-xs">X: <span>{{ logoX.toFixed(2)
                      }}</span>%</div>
                      <div class="bg-[rgba(124,58,237,0.12)] px-2 py-1 rounded-md text-xs">Y: <span>{{ logoY.toFixed(2)
                      }}</span>%</div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Submit button -->
            <button @click="startProcess" :disabled="isProcessing"
              :class="['w-full py-4 rounded-xl text-base font-semibold border-none cursor-pointer flex items-center justify-center gap-2 transition-all mt-2',
                isProcessing
                  ? 'bg-[rgba(255,255,255,0.08)] text-[#64748B] cursor-not-allowed'
                  : 'bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] hover:opacity-90 text-white shadow-lg shadow-[#7C3AED]/30']">
              <span>{{ isProcessing ? 'Processing Video... Please wait' : 'Generate Recap Video' }}</span>
            </button>
          </div>
        </div>

        <!-- ══════════════ STATUS CARD ══════════════ -->
        <div class="border border-[rgba(255,255,255,0.08)] rounded-2xl px-6 py-5 bg-[rgba(255,255,255,0.02)]">
          <h3 class="font-bold text-[#F1F5F9] mb-5 flex items-center gap-2 text-base">
            <span v-if="isProcessing" class="relative inline-flex w-2.5 h-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#7C3AED] opacity-60"></span>
              <span class="relative inline-flex rounded-full w-2.5 h-2.5 bg-[#7C3AED]"></span>
            </span>
            Processing Pipeline Status
          </h3>

          <!-- Upload progress -->
          <div v-if="uploadProgress" class="mb-5">
            <div
              :class="['bg-[rgba(255,255,255,0.03)] border rounded-xl p-4 shadow-sm', uploadProgress.done ? 'border-emerald-500/30' : 'border-[#7C3AED]/30']">
              <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                  <span
                    :class="['animate-ping absolute inline-flex h-full w-full rounded-full opacity-60', uploadProgress.done ? 'bg-emerald-400' : 'bg-[#7C3AED]']"></span>
                  <span
                    :class="['relative inline-flex rounded-full h-2.5 w-2.5', uploadProgress.done ? 'bg-emerald-500' : 'bg-[#7C3AED]']"></span>
                </span>
                <span
                  :class="['text-sm font-bold', uploadProgress.done ? 'text-emerald-400 animate-pulse' : 'text-[#A78BFA]']">
                  {{ uploadProgress.done ? '✅ Upload complete. Starting pipeline...' : `📡 Uploading to Server...
                  ${uploadProgress.pct}%` }}
                </span>
                <span v-if="!uploadProgress.done" class="ml-auto text-xs text-[#64748B] font-mono">{{
                  uploadProgress.current }} / {{ uploadProgress.total }} chunks</span>
              </div>
              <div v-if="!uploadProgress.done"
                class="w-full bg-[rgba(255,255,255,0.08)] rounded-full h-2 overflow-hidden mt-3">
                <div class="h-full rounded-full bg-[#7C3AED] transition-all duration-300"
                  :style="`width:${uploadProgress.pct}%`"></div>
              </div>
            </div>
          </div>

          <!-- Pipeline steps -->
          <div class="grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
            <div v-for="(step, idx) in pipelineSteps" :key="idx"
              class="bg-[rgba(255,255,255,0.03)] p-3.5 rounded-xl border border-[rgba(255,255,255,0.06)] shadow-sm">
              <div :class="['font-semibold flex flex-col gap-1', step.colorClass]">
                <span class="flex items-center gap-1 text-xs">{{ step.icon }} <span>{{ step.label }}</span></span>
                <span v-if="step.showPct" :class="['font-mono text-xs', step.pctClass]">{{ step.pctText }}</span>
              </div>
              <div v-if="step.showBar"
                class="w-full bg-[rgba(255,255,255,0.08)] rounded-full h-1.5 mt-2 overflow-hidden">
                <div :class="['h-full rounded-full transition-all duration-300', step.barClass]"
                  :style="`width:${step.pct}%`"></div>
              </div>
            </div>
          </div>

          <!-- Inline error -->
          <div v-if="inlineError"
            class="mt-4 p-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg text-sm font-medium">
            <span class="font-bold">Error Occurred: </span>{{ inlineError }}
          </div>
        </div>

      </div>
    </main>

    <AppFooter />

  </div>
</template>

<script>
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';
import VoiceSelector from '@/Components/VoiceSelector.vue';
export default {
  name: 'MovieRecapShow',

  props: {
    auth: Object,
    user: Object
  },

  components: {
    AppNavbar,
    AppFooter,
    VoiceSelector,
  },

  data() {
    return {
      baseUrl: '',
      baseUrlReady: false,
      servers: {
        v1: 'https://zakerxa-zakerxa-v1.hf.space',
        v2: 'https://zakerxa-zakerxa-v1.hf.space',
        v3: 'https://zakerxav2-zakerxav2-v2.hf.space',
        recap: 'https://recap.zakerxa.com',
      },

      activeMode: 'upload',
      isProcessing: false,
      showErrorOverlay: false,
      errorPopupMsg: '',
      alertType: 'error',
      inlineError: '',
      selectedVoice: 'my-MM-ThihaNeural',
      logoX: 0,
      logoY: 0,
      blurX: 0,
      blurY: 85,
      blurH: 15,

      uploadProgress: null,
      hasVideoSelected: false,

      stepCurrent: 0,
      stepProgress: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 },

      _logoIsDragging: false,
      _logoStartX: 0, _logoStartY: 0,
      _logoInitialX: 0, _logoInitialY: 0,
      _blurIsDragging: false,
      _blurStartX: 0, _blurStartY: 0,
      _blurInitialX: 0, _blurInitialY: 0,
    };
  },

  computed: {
    pipelineSteps() {
      const cur = this.stepCurrent;
      const prog = this.stepProgress;
      const STEP_LABELS = ['Process Input', 'Extract Audio', 'AI Translate', 'AI Voice (TTS)', 'Render Final'];
      const isWaitingInQueue = cur === 1 && prog[1] === 100;

      return [1, 2, 3, 4, 5].map(i => {
        const pct = prog[i] || 0;
        const label = `${i}. ${STEP_LABELS[i - 1]}`;
        const isQueue = i === 2 && isWaitingInQueue;
        const isDone = i < cur || (i === 1 && isWaitingInQueue);
        const isActive = i === cur && !isWaitingInQueue;

        if (isQueue) return {
          icon: '⏳', label: '2. Waiting in Queue...',
          colorClass: 'text-amber-500 font-bold flex flex-col gap-1',
          pctText: 'စောင့်ဆိုင်းနေပါသည်',
          pctClass: 'font-mono text-[10px] text-amber-600 animate-pulse',
          showPct: true, showBar: true,
          barClass: 'bg-amber-400 animate-pulse', pct: 100,
        };
        if (isDone) return {
          icon: '✅', label,
          colorClass: 'text-emerald-600 font-bold flex flex-col gap-1',
          pctText: '100%', pctClass: 'font-mono text-xs',
          showPct: true, showBar: true,
          barClass: 'bg-emerald-500', pct: 100,
        };
        if (isActive) return {
          icon: '🔄', label,
          colorClass: 'text-indigo-600 font-bold flex flex-col gap-1',
          pctText: `${pct}%`, pctClass: 'font-mono text-xs',
          showPct: true, showBar: true,
          barClass: 'bg-indigo-500 animate-pulse', pct,
        };
        return {
          icon: '⏳', label,
          colorClass: 'text-slate-400 font-medium flex flex-col gap-1',
          pctText: `${pct}%`, pctClass: 'font-mono text-xs',
          showPct: false, showBar: false,
          barClass: 'bg-indigo-500', pct,
        };
      });
    },
  },

  methods: {

    switchTab(mode) {
      if (this.isProcessing) return;
      this.activeMode = mode;
      if (mode === 'youtube') this.removeSelectedVideo();
    },

    previewVideo(event) {
      const file = event.target.files[0];
      if (!file) { this.removeSelectedVideo(); return; }

      const isVideo = file.type.startsWith('video/');
      const allowed = ['.mp4', '.mkv', '.mov', '.avi', '.webm'];
      const ext = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();
      if (!isVideo && !allowed.includes(ext)) {
        this.showAlert('warning', '❌ Invalid File Type! Please upload a valid video file (MP4, MKV, MOV).');
        this.removeSelectedVideo(); return;
      }

      this.hasVideoSelected = true;

      if (this.$refs.uploadPlaceholder) {
        this.$refs.uploadPlaceholder.innerHTML = `Selected File: <span style="color:#4F46E5;font-weight:700">${file.name}</span>`;
      }
      if (this.$refs.uploadIconArea) this.$refs.uploadIconArea.style.display = 'none';
      if (this.$refs.uploadFormats) this.$refs.uploadFormats.style.display = 'none';
      if (this.$refs.videoPreview) this.$refs.videoPreview.src = URL.createObjectURL(file);
      if (this.$refs.videoPreviewContainer) this.$refs.videoPreviewContainer.style.display = 'flex';

      if (this.$refs.blurBoxEl) {
        const b = this.$refs.blurBoxEl;
        b.style.display = 'block';
        b.style.width = '100%';
        b.style.left = '0px';
        b.style.top = '85%';
        b.style.height = '15%';
        setTimeout(() => this.updateBlurCoordinates(), 100);
      }
    },

    removeSelectedVideo() {
      if (this.$refs.videoFileInput) this.$refs.videoFileInput.value = '';
      if (this.$refs.videoPreview) this.$refs.videoPreview.src = '';
      if (this.$refs.videoPreviewContainer) this.$refs.videoPreviewContainer.style.display = 'none';
      if (this.$refs.blurBoxEl) this.$refs.blurBoxEl.style.display = 'none';
      if (this.$refs.uploadIconArea) this.$refs.uploadIconArea.style.display = 'flex';
      if (this.$refs.uploadFormats) this.$refs.uploadFormats.style.display = 'block';
      if (this.$refs.uploadPlaceholder) this.$refs.uploadPlaceholder.innerText = 'Click to upload or drag & drop video';
      this.hasVideoSelected = false;
    },

    previewLogo(event) {
      const file = event.target.files[0];
      const img = this.$refs.watermarkLogo;
      if (!file || !img) return;
      img.src = URL.createObjectURL(file);
      img.style.display = 'block';
      img.style.width = '60px';
      img.style.left = '10px';
      img.style.top = '10px';
      img.onload = () => this.updateLogoCoordinates();
      img.removeEventListener('touchstart', this.logoDragStart);
      img.addEventListener('touchstart', this.logoDragStart, { passive: false });
    },

    updateLogoCoordinates() {
      const img = this.$refs.watermarkLogo;
      const frame = this.$refs.previewFrame;
      if (!img || !frame) return;
      this.logoX = (img.offsetLeft / frame.clientWidth) * 100;
      this.logoY = (img.offsetTop / frame.clientHeight) * 100;
    },

    logoDragStart(e) {
      const pt = e.touches ? e.touches[0] : e;
      e.preventDefault();
      this._logoIsDragging = true;
      this._logoStartX = pt.clientX;
      this._logoStartY = pt.clientY;
      this._logoInitialX = this.$refs.watermarkLogo.offsetLeft;
      this._logoInitialY = this.$refs.watermarkLogo.offsetTop;
      this.$refs.watermarkLogo.style.cursor = 'grabbing';
    },
    logoDrag(e) {
      if (!this._logoIsDragging) return;
      e.preventDefault();
      const pt = e.touches ? e.touches[0] : e;
      const img = this.$refs.watermarkLogo;
      const frame = this.$refs.previewFrame;
      if (!img || !frame) return;
      let nx = this._logoInitialX + (pt.clientX - this._logoStartX);
      let ny = this._logoInitialY + (pt.clientY - this._logoStartY);
      nx = Math.max(0, Math.min(nx, frame.clientWidth - img.clientWidth));
      ny = Math.max(0, Math.min(ny, frame.clientHeight - img.clientHeight));
      img.style.left = nx + 'px';
      img.style.top = ny + 'px';
      this.updateLogoCoordinates();
    },
    logoDragEnd() {
      this._logoIsDragging = false;
      if (this.$refs.watermarkLogo) this.$refs.watermarkLogo.style.cursor = 'grab';
    },

    blurDragStart(e) {
      const pt = e.touches ? e.touches[0] : e;
      e.preventDefault();
      this._blurIsDragging = true;
      this._blurStartX = pt.clientX;
      this._blurStartY = pt.clientY;
      this._blurInitialX = this.$refs.blurBoxEl.offsetLeft;
      this._blurInitialY = this.$refs.blurBoxEl.offsetTop;
      this.$refs.blurBoxEl.style.cursor = 'grabbing';
    },
    blurDrag(e) {
      if (!this._blurIsDragging) return;
      e.preventDefault();
      const pt = e.touches ? e.touches[0] : e;
      const box = this.$refs.blurBoxEl;
      const frame = this.$refs.previewFrame;
      if (!box || !frame) return;
      let nx = this._blurInitialX + (pt.clientX - this._blurStartX);
      let ny = this._blurInitialY + (pt.clientY - this._blurStartY);
      nx = Math.max(0, Math.min(nx, frame.clientWidth - box.clientWidth));
      ny = Math.max(0, Math.min(ny, frame.clientHeight - box.clientHeight));
      box.style.left = nx + 'px';
      box.style.top = ny + 'px';
      this.updateBlurCoordinates();
    },
    blurDragEnd() {
      this._blurIsDragging = false;
      if (this.$refs.blurBoxEl) this.$refs.blurBoxEl.style.cursor = 'move';
    },

    updateBlurCoordinates() {
      const box = this.$refs.blurBoxEl;
      const frame = this.$refs.previewFrame;
      if (!box || !frame) return;
      this.blurX = (box.offsetLeft / frame.clientWidth) * 100;
      this.blurY = (box.offsetTop / frame.clientHeight) * 100;
      this.blurH = (box.clientHeight / frame.clientHeight) * 100;
    },

    setBlurHeight(val) {
      this.blurH = parseFloat(val);
      const box = this.$refs.blurBoxEl;
      const frame = this.$refs.previewFrame;
      if (!box || !frame) return;
      const newHeightPx = (this.blurH / 100) * frame.clientHeight;
      box.style.height = newHeightPx + 'px';
      const maxTop = frame.clientHeight - newHeightPx;
      if (box.offsetTop > maxTop) box.style.top = maxTop + 'px';
      this.updateBlurCoordinates();
    },

    toggleVoiceover() {
      const cont = this.$refs.voiceoverContainer;
      if (!cont) return;
      if (this.$refs.enableVoiceover?.checked) {
        cont.classList.remove('opacity-50', 'pointer-events-none');
      } else {
        cont.classList.add('opacity-50', 'pointer-events-none');
      }
    },

    waterMarkToggle() {
      const isEnabled = this.$refs.enableWatermark?.checked;
      const section = this.$refs.watermarkUploadSection;
      const img = this.$refs.watermarkLogo;
      if (!section) return;
      section.style.display = isEnabled ? 'block' : 'none';
      if (img) img.style.display = (isEnabled && img.src && !img.src.endsWith(window.location.pathname)) ? 'block' : 'none';
    },

    // ─── Alert helpers ───
    showAlert(type, msg) {
      this.alertType = type;
      this.errorPopupMsg = msg;
      this.showErrorOverlay = true;
      this.isProcessing = false;
    },
    showError(msg) {
      this.alertType = 'error';
      this.inlineError = msg;
      this.errorPopupMsg = msg;
      this.showErrorOverlay = true;
      this.isProcessing = false;
    },
    closeErrorPopup() {
      this.showErrorOverlay = false;
    },

    showUploadProgressState(current, total) {
      const pct = Math.round((current / total) * 100);
      this.uploadProgress = { current, total, pct, done: false };
    },
    showUploadFinalizingState() {
      this.uploadProgress = { done: true };
    },
    hideUploadProgressState() {
      this.uploadProgress = null;
    },

    resetSteps() {
      this.stepCurrent = 0;
      this.stepProgress = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
    },

    cleanDashboardPreview() {
      this.blurX = 0; this.blurY = 85; this.blurH = 15;
      if (this.$refs.urlInput) this.$refs.urlInput.value = '';
      if (this.$refs.enableSubtitles) this.$refs.enableSubtitles.checked = false;
      if (this.$refs.enableFlip) this.$refs.enableFlip.checked = false;
      if (this.$refs.enableWatermark) this.$refs.enableWatermark.checked = false;
      if (this.$refs.logoFileInput) this.$refs.logoFileInput.value = '';
      if (this.$refs.watermarkLogo) this.$refs.watermarkLogo.src = '';
      if (this.$refs.voiceModel) this.$refs.voiceModel.value = 'my-MM-ThihaNeural';
      window._downloadTriggered = false;
      this.waterMarkToggle();
      this.removeSelectedVideo();
    },

    async pollStatus(jobId) {
      try {
        const res = await fetch(`/jobs/status/${jobId}`);
        if (!res.ok) throw new Error('Status synchronization failed.');
        const data = await res.json();
        if (data.error) { this.showError(data.error); return; }

        this.stepCurrent = data.step;
        this.stepProgress = data.progress || { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };

        if (data.done) {
          if (!window._downloadTriggered) {
            window._downloadTriggered = true;
            const link = document.createElement('a');
            link.href = `${this.baseUrl}/download/${jobId}`;
            link.download = 'Recap_Ready.mp4';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          }
          this.isProcessing = false;
          this.cleanDashboardPreview();
          return;
        }
        setTimeout(() => this.pollStatus(jobId), 4000);
      } catch (err) {
        setTimeout(() => this.pollStatus(jobId), 5000);
      }
    },


    async getVideoDuration(file) {
      return new Promise((resolve) => {
        const video = document.createElement('video');
        video.preload = 'metadata';
        video.src = URL.createObjectURL(file);
        video.onloadedmetadata = () => {
          URL.revokeObjectURL(video.src); // memory free
          resolve(video.duration); // seconds အနေနဲ့ return
        };
      });
    },

    // Single endpoint health check with timeout
    async checkHealth(url, timeoutMs = 4000) {
      const controller = new AbortController();
      const timer = setTimeout(() => controller.abort(), timeoutMs);
      try {
        const res = await fetch(`${url}/health`, { signal: controller.signal });
        clearTimeout(timer);
        if (!res.ok) return false;

        const data = await res.json();
        console.log(`[health] ${url} ->`, data.status, data.message);

        // TODO (later): queue/semaphore full check
        // if (data.queue_count >= 3) return false;

        return data.status === 'ok' || data.status === 'healthy';
      } catch (err) {
        clearTimeout(timer);
        console.warn(`[health] ${url} unreachable:`, err.message);
        return false;
      }
    },

    // Decide [primary, fallback] pair based on role
    getServerPairForRole(roleName) {
      switch (roleName) {
        case 'tester':
          return [this.servers.v1, this.servers.v3];
        case 'normal':
          return [this.servers.v2, this.servers.v3];
        case 'pro':
          return [this.servers.recap, this.servers.v3];
        default:
          return [this.servers.recap, this.servers.v3];
      }
    },

    // Resolve baseUrl: try primary, fallback if primary fails
    async resolveBaseUrl() {
      const roleName = this.auth.user?.role_name;
      const [primary, fallback] = this.getServerPairForRole(roleName);

      const primaryOk = await this.checkHealth(primary);
      if (primaryOk) {
        this.baseUrl = primary;
        this.baseUrlReady = true;
        // console.log(`[resolveBaseUrl] role=${roleName} -> primary (${primary})`);
        return primary;
      }

      const fallbackOk = await this.checkHealth(fallback);
      if (fallbackOk) {
        this.baseUrl = fallback;
        this.baseUrlReady = true;
        // console.log(`[resolveBaseUrl] role=${roleName} -> fallback (${fallback})`);
        return fallback;
      }

      // အကုန် fail ရင် primary ကိုပဲ ပြန်ပေးမယ် (startProcess() ထဲက try/catch က handle လုပ်ပေးလိမ့်မယ်)
      // console.error(`[resolveBaseUrl] role=${roleName} -> both primary & fallback failed, defaulting to primary`);
      this.baseUrl = primary;
      this.baseUrlReady = true;
      return primary;
    },

    async startProcess() {

      if (!this.auth.user) {
        this.showAlert('info', 'ဤ feature ကို အသုံးပြုရန် Login ဝင်ရောက်ရန် လိုအပ်ပါသည်။');
        return;
      }

      await this.resolveBaseUrl();

      const file = this.$refs.videoFileInput?.files[0];
      const youtubeUrl = this.$refs.urlInput?.value;
      const subtitles = this.$refs.enableSubtitles?.checked;
      const flip = this.$refs.enableFlip?.checked;
      const watermark = this.$refs.enableWatermark?.checked;
      const voiceover = this.$refs.enableVoiceover?.checked;
      const voice = this.selectedVoice;
      const logoFile = this.$refs.logoFileInput?.files[0];

      if (this.activeMode === 'youtube' && !youtubeUrl) { this.showAlert('warning', 'YouTube URL တစ်ခု ထည့်သွင်းပါ။'); return; }
      if (this.activeMode === 'upload' && !file) { this.showAlert('warning', 'Upload လုပ်မည့် Video File တစ်ခု ရွေးချယ်ပါ။'); return; }

      if (this.auth.user.role_name != 'admin') {

        this.isProcessing = true;

        if (this.auth.user.total_recap_used >= this.auth.user.recap_limit) {
          this.showAlert('warning', `ဒီနေ့ limit (${this.auth.user.recap_limit}/day) ပြည့်သွားပြီ။ နောက်နေ့ ထပ်သုံးနိုင်သည်။`)
          return;
        }

        const duration = await this.getVideoDuration(file);
        // မိနစ်၊ စက္ကန့် အနေနဲ့ ကြည့်ချင်ရင်
        const mins = Math.floor(duration / 60);
        const secs = Math.floor(duration % 60);
        console.log(`Video Duration: ${duration} seconds`, secs, mins);
        if (this.auth.user.role_name == 'tester') {
          if (watermark) {
            this.showAlert('warning', 'WaterMark အသုံးပြုရန် သင့် Plan ကိုအဆင့်မြင်‌တင်ပါ။'); return;
          }
          if (secs > 35) {
            this.showAlert('warning', `သင့် video မှာ ${secs}s ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Normal Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

        if (this.auth.user.role_name == 'normal') {
          if (watermark) {
            this.showAlert('warning', 'WaterMark အသုံးပြုရန် Pro Plan ကိုအဆင့်မြင်‌တင်ပါ။'); return;
          }
          if (secs > 60) {
            this.showAlert('warning', `သင့် video မှာ ${secs}s ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Pro Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

        if (this.auth.user.role_name == 'pro') {
          if (secs > 105) {
            this.showAlert('warning', `သင့် video မှာ သတ်မှတ်ချက်ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Vip Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

      }

      this.isProcessing = true;
      this.inlineError = '';
      this.resetSteps();

      try {
        let response;

        if (this.activeMode === 'upload') {
          const CHUNK_SIZE = 5 * 1024 * 1024;
          const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
          const sessionId = crypto.randomUUID();
          this.showUploadProgressState(0, totalChunks);

          for (let i = 0; i < totalChunks; i++) {
            const chunk = file.slice(i * CHUNK_SIZE, (i + 1) * CHUNK_SIZE);
            const chunkForm = new FormData();
            chunkForm.append('chunk', chunk, 'chunk');
            chunkForm.append('chunkIndex', i);
            chunkForm.append('totalChunks', totalChunks);
            chunkForm.append('sessionId', sessionId);


            const chunkRes = await fetch(`${this.baseUrl}/upload-chunk`, {
              method: 'POST',
              body: chunkForm,
              headers: {
                'X-Auth-Username': this.auth.user.username,
                'X-Auth-Role': this.auth.user.role_name
              }
            });
            if (!chunkRes.ok) {
              const err = await chunkRes.json().catch(() => null);
              throw new Error(err?.detail || 'Chunk upload failed.');
            }
            this.showUploadProgressState(i + 1, totalChunks);
          }

          this.showUploadFinalizingState();

          const finalForm = new FormData();
          finalForm.append('sessionId', sessionId);
          finalForm.append('voice_model', voice);
          finalForm.append('blur_x', this.blurX.toFixed(2));
          finalForm.append('blur_y', this.blurY.toFixed(2));
          finalForm.append('blur_h', this.blurH.toFixed(2));
          finalForm.append('enable_subtitles', subtitles);
          finalForm.append('enable_flip', flip);
          finalForm.append('enable_watermark', watermark);
          finalForm.append('enable_voiceover', voiceover);
          if (watermark) {
            finalForm.append('watermark_x', this.logoX.toFixed(2));
            finalForm.append('watermark_y', this.logoY.toFixed(2));
            if (logoFile) finalForm.append('watermark_file', logoFile);
          }


          response = await fetch(`${this.baseUrl}/upload-chunk-finalize`, {
            method: 'POST',
            body: finalForm,
            headers: {
              'X-Auth-Username': this.auth.user.username,
              'X-Auth-Role': this.auth.user.role_name
            }
          });

        } else {

          if (this.auth.user.role_name == 'tester') {
            this.showAlert("warning", "Please upgrade your plan!");
            return
          }
          const formData = new FormData();
          formData.append('url', youtubeUrl);
          formData.append('voice_model', voice);
          formData.append('blur_x', this.blurX.toFixed(2));
          formData.append('blur_y', this.blurY.toFixed(2));
          formData.append('blur_h', this.blurH.toFixed(2));
          formData.append('enable_subtitles', subtitles);
          formData.append('enable_flip', flip);
          formData.append('enable_watermark', watermark);
          formData.append('enable_voiceover', voiceover);
          if (watermark) {
            formData.append('watermark_x', this.logoX.toFixed(2));
            formData.append('watermark_y', this.logoY.toFixed(2));
            if (logoFile) formData.append('watermark_file', logoFile);
          }
          response = await fetch(`${this.baseUrl}/process-youtube`, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Auth-Username': this.auth.user.username,
              'X-Auth-Role': this.auth.user.role_name
            }
          });
        }

        if (!response.ok) {
          const err = await response.json().catch(() => null);
          throw new Error(err?.detail || 'Failed to start processing job on Server.');
        }

        this.hideUploadProgressState();
        const data = await response.json();
        this.pollStatus(data.job_id);

      } catch (error) {
        this.hideUploadProgressState();
        this.showError(error.message);
      }
    },
  },

  mounted() {
    if (this.$refs.watermarkLogo) {
      this.$refs.watermarkLogo.addEventListener('mousedown', this.logoDragStart);
    }
    document.addEventListener('mousemove', this.logoDrag);
    document.addEventListener('mouseup', this.logoDragEnd);
    document.addEventListener('touchmove', this.logoDrag, { passive: false });
    document.addEventListener('touchend', this.logoDragEnd);
    document.addEventListener('touchcancel', this.logoDragEnd);

    if (this.$refs.blurBoxEl) {
      this.$refs.blurBoxEl.addEventListener('mousedown', this.blurDragStart);
      this.$refs.blurBoxEl.addEventListener('touchstart', this.blurDragStart, { passive: false });
    }
    document.addEventListener('mousemove', this.blurDrag);
    document.addEventListener('mouseup', this.blurDragEnd);
    document.addEventListener('touchmove', this.blurDrag, { passive: false });
    document.addEventListener('touchend', this.blurDragEnd);
    document.addEventListener('touchcancel', this.blurDragEnd);
  },

  beforeUnmount() {
    document.removeEventListener('mousemove', this.logoDrag);
    document.removeEventListener('mouseup', this.logoDragEnd);
    document.removeEventListener('touchmove', this.logoDrag);
    document.removeEventListener('touchend', this.logoDragEnd);
    document.removeEventListener('touchcancel', this.logoDragEnd);

    document.removeEventListener('mousemove', this.blurDrag);
    document.removeEventListener('mouseup', this.blurDragEnd);
    document.removeEventListener('touchmove', this.blurDrag);
    document.removeEventListener('touchend', this.blurDragEnd);
    document.removeEventListener('touchcancel', this.blurDragEnd);
  },
};
</script>

<style scoped>
.alert-fade-enter-active,
.alert-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.alert-fade-enter-from,
.alert-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>