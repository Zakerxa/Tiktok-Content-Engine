<template>
  <Head title="Movie-Recap Studio" />
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
          'bg-[#0D1120] border-[#7C3AED]/25': alertType === 'queue',
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
            <div v-else-if="alertType === 'queue'"
              class="w-16 h-16 rounded-full bg-amber-500/10 flex items-center justify-center">
              <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <a v-if="alertType === 'warning'" :href="telegramUrl" target="_blank" rel="noopener"
              class="block text-center w-full bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-[#94A3B8] text-sm font-medium py-2.5 rounded-xl no-underline transition-colors">
              Telegram Group ကို ဝင်ရောက်ရန်
            </a>
          </div>
        </div>
      </div>
    </transition>

    <!-- ══════════════ MAIN CARD ══════════════ -->
    <main class="flex-1 flex items-start justify-center px-3 py-6 mt-12 sm:px-4 sm:py-10">
      <div
        class="relative w-full max-w-6xl bg-[rgba(255,255,255,0.03)] backdrop-blur-xl rounded-3xl shadow-2xl border border-[rgba(255,255,255,0.08)] px-4 py-5 sm:px-6 sm:py-6 md:px-8">

        <!-- Plan status badge (top-right corner) -->
        <div class="absolute top-4 right-4 sm:top-5 sm:right-6 z-10" ref="planBadgeWrap">
          <button
            type="button"
            @click="showPlanTooltip = !showPlanTooltip"
            class="flex items-center gap-1.5 rounded-full border px-2.5 py-1 sm:px-3 sm:py-1.5 text-[11px] sm:text-xs font-bold backdrop-blur-md transition-colors"
            :class="planBadge.pillClass"
          >
            <span>{{ planBadge.icon }}</span>
            <span>{{ planBadge.shortLabel }}</span>
          </button>

          <transition name="alert-fade">
            <div
              v-if="showPlanTooltip"
              class="absolute right-0 mt-2 w-56 sm:w-64 rounded-xl border p-3.5 text-xs shadow-2xl backdrop-blur-xl"
              :class="planBadge.popoverClass"
            >
              <p class="font-semibold text-[#F1F5F9] mb-1 leading-snug">{{ planBadge.fullLabel }}</p>
              <p class="text-[#94A3B8] leading-relaxed">{{ planBadge.hint }}</p>
              <a href="/plan" class="mt-2 inline-block text-[#A78BFA] font-semibold no-underline hover:underline">Plan အသေးစိတ်ကြည့်ရန် →</a>
            </div>
          </transition>
        </div>

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
              <!-- <button @click="switchTab('youtube')" :class="['flex-1 py-2.5 text-sm font-semibold rounded-lg border-none cursor-pointer transition-all',
                activeMode === 'youtube'
                  ? 'bg-[rgba(124,58,237,0.12)] text-[#A78BFA] shadow border border-[rgba(124,58,237,0.3)]'
                  : 'bg-transparent text-[#64748B] hover:text-[#94A3B8]']">🔗 YouTube URL</button> -->
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
                <p ref="uploadFormats" class="text-xs text-[#64748B] mt-1 opacity-70">MP4, MKV, MOV (Max 100MB)</p>
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
                    style="display:none; left:0; top:90%; width:100%; height:10%"><span
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

              <!-- Blur region -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <label class="font-bold text-[#CBD5E1] text-sm block mb-2">🌀 Blur Region</label>
                <p class="text-xs text-[#64748B] mb-2">Blur box ကို Video Preview ထဲမှာ Drag လုပ်ပြီး position သတ်မှတ်ပါ
                </p>
                <div class="flex flex-wrap gap-2 items-center">
                  <input type="range" id="blur-height" min="5" max="20" value="10" class="w-full accent-[#7C3AED]"
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

              <!-- Mirror Mode row — တစ်ခုထဲသီးသန့် -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <label class="flex items-center justify-between cursor-pointer gap-3">
                  <span class="text-sm font-medium text-[#CBD5E1]">🪞 Mirror Mode</span>
                  <div class="relative inline-flex items-center">
                    <input ref="enableFlip" type="checkbox" class="sr-only peer" v-model="mirrorModeEnabled" :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </div>
                </label>
              </div>


              <!-- Auto Subtitles row — toggle + selector တစ်ခုတည်း box ထဲမှာ -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <label class="flex items-center justify-between cursor-pointer gap-3">
                  <span class="text-sm font-medium text-[#CBD5E1]">💬 Auto Subtitles</span>
                  <div class="relative inline-flex items-center">
                    <input ref="enableSubtitles" type="checkbox" class="sr-only peer" v-model="subtitlesEnabled"
                      :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </div>
                </label>

                <!-- Subtitle Text Color selector — subtitle enable ဖြစ်မှသာ ပြမည် -->
                <div v-if="subtitlesEnabled" class="mt-4 pt-4 border-t border-[rgba(255,255,255,0.08)]">
                  <label class="font-bold text-[#CBD5E1] text-sm block mb-3">🎨 Subtitle Text Color</label>
                  <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="selectedSubtitleColor = 'yellow'" :disabled="isProcessing"
                      :class="['flex flex-col items-center gap-1.5 py-2.5 rounded-xl border-2 cursor-pointer transition-all bg-[rgba(255,255,255,0.02)]',
                        selectedSubtitleColor === 'yellow' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)]' : 'border-[rgba(255,255,255,0.1)] hover:border-[rgba(255,255,255,0.25)]']">
                      <span class="w-6 h-6 rounded-full border border-black/30" style="background:#FFEB3B"></span>
                      <span class="text-xs font-semibold text-[#CBD5E1]">Yellow</span>
                    </button>
                    <button type="button" @click="selectedSubtitleColor = 'white'" :disabled="isProcessing"
                      :class="['flex flex-col items-center gap-1.5 py-2.5 rounded-xl border-2 cursor-pointer transition-all bg-[rgba(255,255,255,0.02)]',
                        selectedSubtitleColor === 'white' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)]' : 'border-[rgba(255,255,255,0.1)] hover:border-[rgba(255,255,255,0.25)]']">
                      <span class="w-6 h-6 rounded-full border border-black/30" style="background:#FFFFFF"></span>
                      <span class="text-xs font-semibold text-[#CBD5E1]">White</span>
                    </button>
                    <button type="button" @click="selectedSubtitleColor = 'netflix'" :disabled="isProcessing"
                      :class="['flex flex-col items-center gap-1.5 py-2.5 rounded-xl border-2 cursor-pointer transition-all bg-[rgba(255,255,255,0.02)]',
                        selectedSubtitleColor === 'netflix' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)]' : 'border-[rgba(255,255,255,0.1)] hover:border-[rgba(255,255,255,0.25)]']">
                      <span class="w-6 h-6 rounded-full border border-black/30"
                        style="background:#FFFFFF; box-shadow: inset 0 -10px 0 0 rgba(0,0,0,0.65)"></span>
                      <span class="text-xs font-semibold text-[#CBD5E1]">Netflix</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Watermark row — toggle + upload selector တစ်ခုတည်း box ထဲမှာ -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <label class="flex items-center justify-between cursor-pointer gap-3">
                  <span class="text-sm font-medium text-[#CBD5E1]">🖼️ Custom Logo</span>
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
                </label>

                <!-- Watermark upload -->
                <div ref="watermarkUploadSection" class="mt-4 pt-4 border-t border-[rgba(255,255,255,0.08)]"
                  style="display:none">
                  <label class="block text-sm font-semibold text-[#CBD5E1] mb-3">Upload &amp; Drag Logo</label>
                  <div class="flex gap-4 items-center">
                    <label class="relative flex flex-col items-center justify-center w-24 h-24 border-2 border-dashed border-[rgba(255,255,255,0.15)] hover:border-[#7C3AED] hover:bg-[rgba(124,58,237,0.08)] rounded-xl cursor-pointer transition-all group overflow-hidden">

                      <!-- Placeholder icon — logo upload ပြီးရင် ဖျောက်ပေးမည် -->
                      <div ref="logoUploadPlaceholder" class="flex flex-col items-center text-[#64748B] group-hover:text-[#A78BFA]">
                        <svg class="w-7 h-7 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wide">Upload</span>
                      </div>

                      <!-- ✅ NEW — static thumbnail only, drag logic မပါ -->
                      <img
                        ref="uploadBoxThumbnail"
                        src=""
                        class="absolute inset-0 w-full h-full object-contain p-2"
                        alt="Uploaded logo"
                        draggable="false"
                        style="display:none"
                      />

                      <input ref="logoFileInput" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="previewLogo" :disabled="isProcessing"/>
                    </label>
                    <div class="flex-1 bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-lg p-3 text-xs text-[#64748B]">
                      <p class="font-semibold text-[#CBD5E1] mb-1">Logo Position</p>
                      <p>Video Preview ထဲမှာ Drag လုပ်ပြီး position သတ်မှတ်ပါ</p>
                      <div class="flex gap-3 font-mono text-[#A78BFA] font-bold mt-2">
                        <div class="bg-[rgba(124,58,237,0.12)] px-1 py-1 rounded-md text-xs">X: <span>{{ logoX.toFixed(2) }}</span>%</div>
                        <div class="bg-[rgba(124,58,237,0.12)] px-1 py-1 rounded-md text-xs">Y: <span>{{ logoY.toFixed(2) }}</span>%</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <!-- Background Music toggle + selector -->
              <div class="bg-[rgba(255,255,255,0.03)] p-4 rounded-xl border border-[rgba(255,255,255,0.08)] shadow-sm">
                <div class="flex justify-between items-center mb-1">
                  <label class="font-bold text-[#CBD5E1] text-sm">🎵 Background Music</label>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input ref="enableBgMusic" type="checkbox" class="sr-only peer" v-model="bgMusicEnabled"
                      :disabled="isProcessing" />
                    <div
                      class="w-11 h-6 bg-[rgba(255,255,255,0.12)] peer-checked:bg-[#7C3AED] rounded-full transition-colors">
                    </div>
                    <div
                      class="absolute top-0.5 left-0.5 bg-white rounded-full h-5 w-5 transition-transform peer-checked:translate-x-5 shadow-sm">
                    </div>
                  </label>
                </div>

                <!-- Enable ဖြစ်မှသာ Selector ကို အောက်ဘက်မှာ ပြသမည် -->
                <div v-show="bgMusicEnabled" class="mt-3">
                  <p class="text-xs text-[#64748B] mb-3">Music ကို ရွေးချယ်ပါ — 🔊 ကိုနှိပ်ပြီး Preview နားထောင်ကြည့်နိုင်ပါသည်</p>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <button type="button" @click="selectBgMusic('Epic-Spectrum-Wandering.mp3')" :disabled="isProcessing"
                      :class="['flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl border-2 cursor-pointer transition-all text-xs font-semibold bg-[rgba(255,255,255,0.02)]',
                        selectedBgMusic === 'Epic-Spectrum-Wandering.mp3' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)] text-[#A78BFA]' : 'border-[rgba(255,255,255,0.1)] text-[#CBD5E1] hover:border-[rgba(255,255,255,0.25)]']">
                      <span>{{ playingMusic === 'Epic-Spectrum-Wandering.mp3' ? '⏸️' : '🔊' }}</span>
                      <span>🎬 Epic Spectrum</span>
                    </button>
                    <button type="button" @click="selectBgMusic('Aylex-Colossal.mp3')" :disabled="isProcessing"
                      :class="['flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl border-2 cursor-pointer transition-all text-xs font-semibold bg-[rgba(255,255,255,0.02)]',
                        selectedBgMusic === 'Aylex-Colossal.mp3' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)] text-[#A78BFA]' : 'border-[rgba(255,255,255,0.1)] text-[#CBD5E1] hover:border-[rgba(255,255,255,0.25)]']">
                      <span>{{ playingMusic === 'Aylex-Colossal.mp3' ? '⏸️' : '🔊' }}</span>
                      <span>🌩️ Colossal</span>
                    </button>
                    <button type="button" @click="selectBgMusic('Pufino-Legend.mp3')" :disabled="isProcessing"
                      :class="['flex items-center justify-center gap-1.5 py-2.5 px-2 rounded-xl border-2 cursor-pointer transition-all text-xs font-semibold bg-[rgba(255,255,255,0.02)]',
                        selectedBgMusic === 'Pufino-Legend.mp3' ? 'border-[#7C3AED] bg-[rgba(124,58,237,0.12)] text-[#A78BFA]' : 'border-[rgba(255,255,255,0.1)] text-[#CBD5E1] hover:border-[rgba(255,255,255,0.25)]']">
                      <span>{{ playingMusic === 'Pufino-Legend.mp3' ? '⏸️' : '🔊' }}</span>
                      <span>🗺️ Legend</span>
                    </button>
                  </div>
                </div>

                <!-- Preview audio element (invisible) — Laravel public folder ထဲက music file ကို ဖွင့်ပြသမည် -->
                <audio ref="bgMusicPreviewAudio" class="hidden" @ended="playingMusic = null"></audio>
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
                  uploadProgress.current }} / {{ uploadProgress.total }}</span>
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
    todayUsed: Number,
    dailyLimit: Number,
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
      servers: [],

      activeMode: 'upload',
      isProcessing: false,
      showErrorOverlay: false,
      errorPopupMsg: '',
      alertType: 'error',
      inlineError: '',
      showPlanTooltip: false,
      telegramUrl: 'https://t.me/+6hc4y3AceQJmNTQ1',
      selectedVoice: 'my-MM-ThihaNeural',
      subtitlesEnabled: false,
      selectedSubtitleColor: 'yellow',
      selectedBgMusic: 'Epic-Spectrum-Wandering.mp3',
      bgMusicEnabled: false,
      mirrorModeEnabled: false,
      playingMusic: null,
      musicPreviewBaseUrl: '/music/', // 👉 Laravel public folder ထဲက music file တွေ ရှိတဲ့ path (public/music/xxx.mp3)
      logoX: 0,
      logoY: 0,
      blurX: 0,
      blurY: 88,
      blurH: 12,

      uploadProgress: null,
      hasVideoSelected: false,
      queueNoticeShown: false,

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

  watch: {
    isWaitingInQueue(nowWaiting) {
      if (nowWaiting && !this.queueNoticeShown && this.auth.user.role_name !== 'tester') {
        this.queueNoticeShown = true;
        this.showAlert('queue','ဤ Video ကို Queue ထဲ ထားရှိပြီးပါပြီ။ ဤစာမျက်နှာကို ပိတ်ထား/ထွက်သွားနိုင်ပါသည် — ၁၀ မိနစ်ခန့်အကြာတွင် Job History စာမျက်နှာမှ ပြန်ဝင်ပြီး Video ကို Download ဆွဲနိုင်ပါသည်။');
      }
    },
  },
  computed: {
    planBadge() {
      const role = (this.auth.user?.role_name || 'tester').toLowerCase();
      const MAP = {
        tester: {
          icon: '🔥',
          shortLabel: 'Free',
          fullLabel: 'အခမဲ့ (Free) Plan အသုံးပြုနေပါသည်',
          hint: 'Video 1 min max ၊ watermark ပါဝင်သည်။ ပိုမိုကောင်းမွန်သော feature များအတွက် Plan အဆင့်မြှင့်ပါ။',
          pillClass: 'border-slate-500/30 bg-slate-500/10 text-slate-300',
          popoverClass: 'border-slate-500/25 bg-[#0D1120]',
        },
        normal: {
          icon: '⚡',
          shortLabel: 'Standard',
          fullLabel: 'Standard Plan အသုံးပြုနေပါသည်',
          hint: 'Video 1.5 min max ၊ watermark ပါဝင်သည်။ Pro Plan သို့ မြှင့်တင်ရန် Telegram မှ ဆက်သွယ်ပါ။',
          pillClass: 'border-amber-500/30 bg-amber-500/10 text-amber-300',
          popoverClass: 'border-amber-500/25 bg-[#0D1120]',
        },
        pro: {
          icon: '👑',
          shortLabel: 'Pro',
          fullLabel: 'Pro Plan အသုံးပြုနေပါသည်',
          hint: 'High quality export ၊ watermark မပါ ၊ video 2.5 min max ။',
          pillClass: 'border-[#7C3AED]/40 bg-[#7C3AED]/10 text-[#C4B5FD]',
          popoverClass: 'border-[#7C3AED]/30 bg-[#0D1120]',
        },
        vip: {
          icon: '💎',
          shortLabel: 'VIP',
          fullLabel: 'VIP Plan အသုံးပြုနေပါသည်',
          hint: 'Feature အားလုံး အကန့်အသတ်နည်းစွာဖြင့် အသုံးပြုနိုင်ပါသည်။',
          pillClass: 'border-cyan-500/30 bg-cyan-500/10 text-cyan-300',
          popoverClass: 'border-cyan-500/25 bg-[#0D1120]',
        },
        admin: {
          icon: '🛡️',
          shortLabel: 'Admin',
          fullLabel: 'Admin အနေဖြင့် အကန့်အသတ်မရှိ အသုံးပြုနေပါသည်',
          hint: 'Internal access — Plan restrictions do not apply to this account.',
          pillClass: 'border-emerald-500/30 bg-emerald-500/10 text-emerald-300',
          popoverClass: 'border-emerald-500/25 bg-[#0D1120]',
        },
      };
      return MAP[role] || MAP.tester;
    },

    isWaitingInQueue() {
      return this.stepCurrent === 1 && (!this.stepProgress[1] || this.stepProgress[1] === 0);
    },

    pipelineSteps() {
      const cur = this.stepCurrent;
      const prog = this.stepProgress;
      const STEP_LABELS = ['Processing Video . . .', 'Extracting Audio . . .', 'Translating Speech . . .', 'Generating Voice (TTS)', 'Producing Final Video ...'];
      const isWaitingInQueue = cur === 1 && (!prog[1] || prog[1] === 0);

      return [1, 2, 3, 4, 5].map(i => {
        const pct = prog[i] || 0;
        const label = `${i}. ${STEP_LABELS[i - 1]}`;
        const isQueue = i === 1 && isWaitingInQueue;
        const isDone = i < cur;
        const isActive = i === cur && !isWaitingInQueue;

        if (isQueue) return {
          icon: '⏳', label: '1. Waiting in Queue...',
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

    handleOutsideClickForPlanTooltip(event) {
      if (!this.showPlanTooltip) return;
      const el = this.$refs.planBadgeWrap;
      if (el && !el.contains(event.target)) {
        this.showPlanTooltip = false;
      }
    },

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
        b.style.top     = '73%';
        b.style.height  = '13%';
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
      const img  = this.$refs.watermarkLogo;      // ← Video preview draggable logo (ဟောင်း, မပြောင်း)
      if (!file || !img) return;
    
      const objectUrl = URL.createObjectURL(file);
    
      img.src           = objectUrl;
      img.style.display = 'block';
      img.style.width   = '60px';
      img.style.left    = '10px';
      img.style.top     = '10px';
      img.onload = () => this.updateLogoCoordinates();
      img.removeEventListener('touchstart', this.logoDragStart);
      img.addEventListener('touchstart', this.logoDragStart, { passive: false });
    
      const thumb = this.$refs.uploadBoxThumbnail;
      const placeholder = this.$refs.logoUploadPlaceholder;
      if (thumb) {
        thumb.src = objectUrl;
        thumb.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
      }
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
      console.log('setBlurHeight', this.blurH);
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

    // 🎵 Background Music ရွေးချယ်ခြင်း + Preview ဖွင့်/ရပ်ခြင်း
    selectBgMusic(filename) {
      this.selectedBgMusic = filename;
      console.log(this.selectedBgMusic, filename);
      const audio = this.$refs.bgMusicPreviewAudio;
      if (!audio) return;

      // ဒီတခုကိုကလိမ့် Play နေရင် → Pause လုပ်လိုက်မည် (Toggle)
      if (this.playingMusic === filename && !audio.paused) {
        audio.pause();
        this.playingMusic = null;
        return;
      }

      // Laravel public folder ထဲက music file ကို ဖွင့်ခြင်း (public/music/xxx.mp3 → /music/xxx.mp3)
      audio.src = `${this.musicPreviewBaseUrl}${filename}`;
      audio.currentTime = 0;
      audio.play().catch(err => console.warn('🎵 Preview play failed:', err));
      this.playingMusic = filename;
    },

    stopBgMusicPreview() {
      const audio = this.$refs.bgMusicPreviewAudio;
      if (audio) audio.pause();
      this.playingMusic = null;
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
      if(type == 'queue') return;
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
      this.subtitlesEnabled = false;
      if (this.$refs.enableFlip) this.$refs.enableFlip.checked = false;
      if (this.$refs.enableWatermark) this.$refs.enableWatermark.checked = false;
      this.bgMusicEnabled = false;
      this.stopBgMusicPreview();
      if (this.$refs.logoFileInput) this.$refs.logoFileInput.value = '';
      if (this.$refs.watermarkLogo) this.$refs.watermarkLogo.src = '';
      if (this.$refs.voiceModel) this.$refs.voiceModel.value = 'my-MM-ThihaNeural';
      window._downloadTriggered = false;
      this.waterMarkToggle();
      this.removeSelectedVideo();
      this.resetSteps();
    },

    async pollStatus(jobId,jobBaseUrl) {
      try {
        
        const res = await fetch(`/jobs/status/${jobId}`,{  headers: { 'Accept': 'application/json' }});
        if (!res.ok) throw new Error('Status synchronization failed.');
        const data = await res.json();
        if (data.error) { this.showError(data.error); return; }

        this.stepProgress = data.progress || { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };

        if (data.done) {
          // ✅ step ကို 6 အဖြစ် တင်လိုက်လို့ Step 5 ဟာ "done" condition (i < cur) ကို ဖြတ်ပြီး green ပြောင်းမယ်
          this.stepCurrent = 6;

          if (!window._downloadTriggered) {
            window._downloadTriggered = true;
            this.autoDownload(jobId);
          }

          this.isProcessing = false;

          // ✅ Green bar ကို ၁.၅ စက္ကန့်လောက် ပြပြီးမှ UI ကို clean ပြန်လုပ်မယ်
          setTimeout(() => {
            this.cleanDashboardPreview();
          }, 1500);

          return;
        }

        // done မဖြစ်သေးရင်သာ stepCurrent ကို update (done ဖြစ်ပြီးရင် 6 အတိုင်း ထားမယ်)
        this.stepCurrent = Number(data.step);

        // 🎯 Step 5 (render — ကြာနိုင်) ရောက်ရင် interval ရှည်စေမယ်
        const nextDelay = this.stepCurrent >= 5 ? 5000 : 6000;
        setTimeout(() => this.pollStatus(jobId, jobBaseUrl), nextDelay)
      } catch (err) {
        setTimeout(() => this.pollStatus(jobId,jobBaseUrl), 8000);
      }
    },

    async autoDownload(jobId) {
      try {
        const res = await fetch(route('jobs.download', jobId), {
          headers: { 'Accept': 'application/json', 'X-Session-ID': window.APP_SESSION_ID },
          credentials: 'same-origin',
        });
    
        if (!res.ok) {
          console.warn('Download failed, status:', res.status, 'session id was:', window.APP_SESSION_ID);
          this.showError(this.friendlyDownloadError(res.status));
          return;
        }
    
        const blob = await res.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url; link.download = 'Recap_Ready.mp4';
        document.body.appendChild(link); link.click(); document.body.removeChild(link);
        URL.revokeObjectURL(url);
      } catch (e) {
        console.warn('Download network error:', e);
        this.showError('Internet ချိတ်ဆက်မှု ပြဿနာ ဖြစ်နေပါသည်။');
      }
    },

    friendlyDownloadError(status) {
      switch (status) {
        case 401: return 'Login သက်တမ်း ကုန်သွားပါပြီ။ ပြန်လည် Login ဝင်ပြီး ထပ်ကြိုးစားပါ။';
        case 403: return 'ဤဖိုင်ကို Download ဆွဲရန် ခွင့်ပြုချက် မရှိပါ။';
        case 404: return 'ဖိုင်ကို ရှာမတွေ့ပါ။';
        case 410: return 'Download link သက်တမ်း ကုန်သွားပါပြီ။';
        default:  return 'Download လုပ်ဆောင်မှု မအောင်မြင်ပါ။ ခဏနေမှ ထပ်ကြိုးစားပါ။';
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
        // console.log(`[health] ${url} ->`, data.status, data.message);

        // TODO (later): queue/semaphore full check
        // if (data.queue_count >= 3) return false;

        return data.status === 'ok' || data.status === 'healthy';
      } catch (err) {
        clearTimeout(timer);
        console.warn(`[health] ${url} unreachable:`, err.message);
        return false;
      }
    },

    // Server list + busy/active status ကို Laravel ကနေယူမယ်
    async fetchServers() {
      try {
        const res = await fetch('/server-status');
        if (res.status === 401 || res.status === 419) {
          this.showAlert('error', 'Session ကုန်သွားပါပြီ။ ပြန်ဝင်ပါ။');
          window.location.href = '/login';
          return [];
        }
        if (!res.ok) return [];
        return await res.json();
      } catch (err) {
        console.warn('[servers] fetch failed:', err.message);
        return [];
      }
    },

    // role အလိုက် candidate server တွေကို filter + priority sort + role-position sort
    getServersForRole(allServers, roleName) {
      return allServers
        .filter(s => !s.role_access?.length || s.role_access.includes(roleName))
        .map(s => {
          // role_access ထဲမှာ ကိုယ့် role ဘယ်နေရာမှာရှိလဲ ကြည့်မယ်
          // role_access အလွတ် [] ဆိုရင် "All" ဖြစ်လို့ tie-break အနေနဲ့ အနောက်ဆုံးထားမယ်
          const roleIdx = s.role_access?.length ? s.role_access.indexOf(roleName) : Infinity;
          return { ...s, _roleIdx: roleIdx };
        })
        .sort((a, b) => {
          // 1st: priority (small = first)
          if (a.priority !== b.priority) return a.priority - b.priority;
          // 2nd: same priority ဆိုရင် role_access array ထဲက role position (small index = first)
          // V1/V3 လိုမျိုး index တူသွားရင် Array.sort ဟာ stable sort ဖြစ်တဲ့အတွက်
          // DB order (id အလိုက်) အတိုင်း ဘယ်ဟာအရင်လာလာ ကျန်နေမယ် — order မတူသော်လည်း
          // logic အရ ဘယ်ဟာယူယူ ကိစ္စမရှိပါ (busy check ကနေဆက်စစ်မှာမို့)
          return a._roleIdx - b._roleIdx;
        });
    },
    
    // Resolve baseUrl: candidate list ကို priority + role-position order အတိုင်း တစ်ခုချင်းစမ်း
    async resolveBaseUrl() {
      const roleName = this.auth.user?.role_name;
      const allServers = await this.fetchServers();
      const candidates = this.getServersForRole(allServers, roleName);
    
      if (!candidates.length) {
        console.error(`[resolveBaseUrl] role=${roleName} -> no server configured for this role`);
        this.showAlert('error', 'ဒီ role အတွက် Server မသတ်မှတ်ရသေးပါ။');
        this.baseUrl = null;
        this.baseUrlReady = false;
        return null;
      }

      console.log(allServers, candidates);
    
      for (const server of candidates) {
        // ✅ is_busy ဖြစ်ပေမယ့် is_stuck လည်းဖြစ်နေရင် (45min ကျော်နေရင်)
        // FastAPI ဘက်က job ကို timeout kill လုပ်ပြီးသားလို့ ယူဆပြီး
        // "genuinely busy" လို့ မသတ်မှတ်ဘဲ health check ဆီဆက်ရဲရဲသွားမယ်
        const isTrulyBusy = server.is_busy && !server.is_stuck;
    
        if (isTrulyBusy) {
          // console.log(`[resolveBaseUrl] ${server.name} is genuinely busy — skip`);
          continue;
        }
    
        if (server.is_busy && server.is_stuck) {
          console.warn(`[resolveBaseUrl] ${server.name} looks stuck (>45min) — verifying via health check before reuse`);
        }
    
        // ✅ Health check — server တကယ်ရှင်လား confirm လုပ်တာ
        // stuck job ဖြစ်နေတဲ့ server ဆိုရင်တောင် health check ok ရင် ပြန်သုံးလို့ရမယ်
        const healthy = await this.checkHealth(server.url);
        if (!healthy) {
          // console.warn(`[resolveBaseUrl] ${server.name} unreachable — skip`);
          continue;
        }
    
        this.baseUrl = server.url;
        this.baseUrlReady = true;
        // console.log(`[resolveBaseUrl] role=${roleName} -> ${server.name}`);
        return server.url;
      }
    
      // candidate အားလုံး busy ဖြစ်နေရင် — error မထုတ်ဘဲ ပထမဆုံး preference
      // (candidates[0]) ကို default အနေနဲ့ ပြန်ယူပြီး queue လုပ်ထားခိုင်းမယ်
      const fallback = candidates[0];
      console.warn(`[resolveBaseUrl] role=${roleName} -> all busy, falling back to default ${fallback.name}`);
      // this.showAlert('warning', `Server အားလုံး အလုပ်များနေလို့ ${fallback.name} ကို queue ထဲထည့်ပေးလိုက်ပါပြီ။`);
      this.baseUrl = fallback.url;
      this.baseUrlReady = true;
      console.log("Fall back to ",candidates[0].name);
      return fallback.url;
    },

    async uploadChunkWithRetry(jobBaseUrl, chunkForm, chunkIndex, maxRetries = 3) {
      let lastError;
    
      for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
          const chunkRes = await fetch(`${jobBaseUrl}/upload-chunk`, {
            method: 'POST',
            body: chunkForm,
            headers: {
              'X-Session-ID': window.APP_SESSION_ID
            }
          });
    
          if (chunkRes.ok) {
            return chunkRes; // ✅ အောင်မြင်ပါက ချက်ချင်း return
          }
    
          const err = await chunkRes.json().catch(() => null);
          lastError = new Error(err?.detail || 'Chunk upload failed.');
    
          // 4xx client error (auth fail, file too big) ဆိုရင် retry မလုပ်ဘဲ ချက်ချင်း fail
          if (chunkRes.status >= 400 && chunkRes.status < 500) {
            throw lastError;
          }
    
        } catch (networkErr) {
          lastError = networkErr;
        }
    
        if (attempt < maxRetries) {
          const delayMs = 1000 * Math.pow(2, attempt - 1); // 1s → 2s → 4s
          console.warn(`⚠️ Chunk ${chunkIndex + 1} upload attempt ${attempt} failed, retrying in ${delayMs}ms...`);
          await new Promise(resolve => setTimeout(resolve, delayMs));
        }
      }
    
      throw lastError; // Retry အားလုံးကုန်သွားပြီး ဆက်fail ရင် error ပြန် throw
    },

    async startProcess() {

      if (!this.auth.user) {
        this.showAlert('info', 'ဤ feature ကို အသုံးပြုရန် Login ဝင်ရောက်ရန် လိုအပ်ပါသည်။');
        return;
      }

      this.isProcessing = true;
      this.stopBgMusicPreview();

      const file = this.$refs.videoFileInput?.files[0];
      const youtubeUrl = this.$refs.urlInput?.value;
      const subtitles = this.$refs.enableSubtitles?.checked;
      const flip = this.$refs.enableFlip?.checked;
      const watermark = this.$refs.enableWatermark?.checked;
      const voiceover = this.$refs.enableVoiceover?.checked;
      const voice = this.selectedVoice;
      const logoFile = this.$refs.logoFileInput?.files[0];
      const subtitleColor = this.selectedSubtitleColor;
      const bgMusic = this.selectedBgMusic;

      this.showAlert('warning', 'Just kidding');

      if (this.activeMode === 'youtube' && !youtubeUrl) { this.showAlert('warning', 'YouTube URL တစ်ခု ထည့်သွင်းပါ။'); return; }
      if (this.activeMode === 'upload' && !file) { this.showAlert('warning', 'Upload လုပ်မည့် Video File တစ်ခု ရွေးချယ်ပါ။'); return; }

      if (this.auth.user.role_name != 'admin') {       

        if (this.todayUsed >= this.dailyLimit) {
          let msg = '';
          console.log("Daily limit reached:", this.auth.user.recap_limit, this.auth.user.total_recap_used);
          if(this.auth.user.recap_limit >= this.auth.user.total_recap_used)  msg = 'သင့် Plan အရ နောက်နေ့မှသာ အသုံးပြုနိုင်ပါသည်။' ;
          else msg = 'သင့် Plan ကိုအဆင့်မြင့်တင်ပါ။';
          this.showAlert('warning', `ဒီနေ့ limit (${this.todayUsed}/${this.dailyLimit}) ပြည့်သွားပြီ။ ${msg}`)
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
          if (secs > 60) {
            this.showAlert('warning', `သင့် video မှာ ${secs}s ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Normal Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

        if (this.auth.user.role_name == 'normal') {
          if (watermark) {
            this.showAlert('warning', 'WaterMark အသုံးပြုရန် Pro Plan ကိုအဆင့်မြင်‌တင်ပါ။'); return;
          }
          if (secs > 90) {
            this.showAlert('warning', `သင့် video မှာ ${secs}s ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Pro Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

        if (this.auth.user.role_name == 'pro') {
          if (secs > 160) {
            this.showAlert('warning', `သင့် video မှာ သတ်မှတ်ချက်ထက်ကျော်လွန်နေ၍တင်မရပါ။ သို့ Vip Plan ကိုအဆင့်မြင့်တင်ပါ။`); return;
          }
        }

      }


      const jobBaseUrl = await this.resolveBaseUrl(); // local variable

      console.log(jobBaseUrl);
      if (!jobBaseUrl) {
        this.showAlert('error', 'Server အကုန် မရရှိနိုင်ပါ။ နောက်မှ ပြန်ကြိုးစားပါ။');
        return;
      }


      this.inlineError = '';
      this.resetSteps();

      try {
        let response;

        if (this.activeMode === 'upload') {
            const MAX_CHUNK_SIZE = 5 * 1024 * 1024; // 5MB — chunk size ကို ဒီတစ်ခုတည်းနေရာမှာ ထားပြီး ထိန်းထားပါ
            const MAX_RETRIES = 3;                   // chunk တစ်ခုချင်း ပြန်ကြိုးစားမည့် အကြိမ်ရေ
          
            const totalChunks = Math.ceil(file.size / MAX_CHUNK_SIZE);
            const sessionId = crypto.randomUUID();
            this.showUploadProgressState(0, totalChunks);
          
            for (let i = 0; i < totalChunks; i++) {
              const chunk = file.slice(i * MAX_CHUNK_SIZE, (i + 1) * MAX_CHUNK_SIZE);
              const chunkForm = new FormData();
              chunkForm.append('chunk', chunk, 'chunk');
              chunkForm.append('chunkIndex', i);
              chunkForm.append('totalChunks', totalChunks);
              chunkForm.append('sessionId', sessionId);
          
              try {
                await this.uploadChunkWithRetry(jobBaseUrl, chunkForm, i, MAX_RETRIES);
              } catch (chunkErr) {
                // Retry အားလုံးကုန်သွားလို့ fail ရင် — outer catch ကို ပို့ပြီး upload process ရပ်
                throw new Error(`Chunk ${i + 1}/${totalChunks} upload failed after ${MAX_RETRIES} attempts: ${chunkErr.message}`);
              }
          
              this.showUploadProgressState(i + 1, totalChunks);
            }
          
            this.showUploadFinalizingState();
          
            const finalForm = new FormData();
            finalForm.append('sessionId', sessionId);
            finalForm.append('originalFileName', file.name);
            finalForm.append('voice_model', voice);
            finalForm.append('blur_x', this.blurX.toFixed(2));
            finalForm.append('blur_y', this.blurY.toFixed(2));
            finalForm.append('blur_h', this.blurH.toFixed(2));
            finalForm.append('enable_subtitles', subtitles);
            finalForm.append('enable_flip', flip);
            finalForm.append('enable_watermark', watermark);
            finalForm.append('enable_voiceover', voiceover);
            finalForm.append('subtitle_style', subtitleColor);
            finalForm.append('bg_music', bgMusic);
            if (watermark) {
              finalForm.append('watermark_x', this.logoX.toFixed(2));
              finalForm.append('watermark_y', this.logoY.toFixed(2));
              if (logoFile) finalForm.append('watermark_file', logoFile);
            }
          
            response = await fetch(`${jobBaseUrl}/upload-chunk-finalize`, {
              method: 'POST',
              body: finalForm,
              headers: {
                'X-Session-ID': window.APP_SESSION_ID
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
          formData.append('subtitle_style', subtitleColor);
          formData.append('bg_music', bgMusic);
          if (watermark) {
            formData.append('watermark_x', this.logoX.toFixed(2));
            formData.append('watermark_y', this.logoY.toFixed(2));
            if (logoFile) formData.append('watermark_file', logoFile);
          }
          response = await fetch(`${jobBaseUrl}/process-youtube`, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Session-ID': window.APP_SESSION_ID
            }
          });
        }

        if (!response.ok) {
          const err = await response.json().catch(() => null);
          throw new Error(err?.detail || 'Failed to start processing job on Server.');
        }

        this.stepCurrent = 1;
        this.stepProgress = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };

        this.hideUploadProgressState();
        const data = await response.json();
        this.pollStatus(data.job_id, jobBaseUrl);

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

    document.addEventListener('click', this.handleOutsideClickForPlanTooltip);
  },

  beforeUnmount() {
    this.stopBgMusicPreview();
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

    document.removeEventListener('click', this.handleOutsideClickForPlanTooltip);
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