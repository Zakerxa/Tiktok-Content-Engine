<template>
  <div class="voice-selector">
    <label class="voice-label">
      🎙️ Voice / Language
      <span class="voice-lang-tag" v-if="detectedLang">{{ detectedLang }}</span>
    </label>

    <select
      ref="voiceModel"
      v-model="selected"
      class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] cursor-pointer outline-none focus:border-[#7C3AED]"
      :disabled="isProcessing"
      @change="$emit('update:modelValue', selected)"
    >
      <optgroup label="🇲🇲 Burmese (မြန်မာ)">
        <option value="my-MM-NilarNeural">🇲🇲 Nilar (Female)</option>
        <option value="my-MM-ThihaNeural">🇲🇲 Thiha (Male)</option>
      </optgroup>

      <optgroup label="🇹🇭 Thai (ไทย)">
        <option value="th-TH-NiwatNeural">🇹🇭 Niwat (Male)</option>
        <option value="th-TH-PremwadeeNeural">🇹🇭 Premwadee (Female)</option>
      </optgroup>

      <optgroup label="🇯🇵 Japanese (日本語)">
        <option value="ja-JP-NanamiNeural">🇯🇵 Nanami (Female)</option>
        <option value="ja-JP-KeitaNeural">🇯🇵 Keita (Male)</option>
      </optgroup>

      <optgroup label="🇨🇳 Chinese Simplified (普通话)">
        <option value="zh-CN-XiaoxiaoNeural">🇨🇳 Xiaoxiao (Female · News)</option>
        <option value="zh-CN-XiaoyiNeural">🇨🇳 Xiaoyi (Female · Lively)</option>
        <option value="zh-CN-YunxiaNeural">🇨🇳 Yunxia (Male · Cute)</option>
        <option value="zh-CN-YunyangNeural">🇨🇳 Yunyang (Male · News)</option>
      </optgroup>

      <optgroup label="🇹🇼 Chinese Traditional (繁體)">
        <option value="zh-TW-HsiaoChenNeural">🇹🇼 HsiaoChen (Female)</option>
        <option value="zh-TW-HsiaoYuNeural">🇹🇼 HsiaoYu (Female)</option>
        <option value="zh-TW-YunJheNeural">🇹🇼 YunJhe (Male)</option>
      </optgroup>

      <optgroup label="🇰🇷 Korean (한국어)">
        <option value="ko-KR-SunHiNeural">🇰🇷 SunHi (Female)</option>
        <option value="ko-KR-InJoonNeural">🇰🇷 InJoon (Male)</option>
        <option value="ko-KR-HyunsuMultilingualNeural">🇰🇷 Hyunsu (Male · Multilingual)</option>
      </optgroup>

      <optgroup label="🇻🇳 Vietnamese (Tiếng Việt)">
        <option value="vi-VN-HoaiMyNeural">🇻🇳 HoaiMy (Female)</option>
        <option value="vi-VN-NamMinhNeural">🇻🇳 NamMinh (Male)</option>
      </optgroup>

      <optgroup label="🇮🇩 Indonesian (Bahasa)">
        <option value="id-ID-GadisNeural">🇮🇩 Gadis (Female)</option>
        <option value="id-ID-ArdiNeural">🇮🇩 Ardi (Male)</option>
      </optgroup>

      <optgroup label="🇮🇳 Hindi (हिन्दी)">
        <option value="hi-IN-SwaraNeural">🇮🇳 Swara (Female)</option>
        <option value="hi-IN-MadhurNeural">🇮🇳 Madhur (Male)</option>
      </optgroup>
    </select>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: 'my-MM-ThihaNeural' },
  isProcessing: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);

const selected = ref(props.modelValue);

// Mirror of voice_config.py — prefix-based detection
const PREFIX_LANG = {
  'my-mm': 'Burmese 🇲🇲',
  'th-th': 'Thai 🇹🇭',
  'ja-jp': 'Japanese 🇯🇵',
  'zh-cn': 'Chinese Simplified 🇨🇳',
  'zh-tw': 'Chinese Traditional 🇹🇼',
  'ko-kr': 'Korean 🇰🇷',      // SunHi, InJoon, HyunsuMultilingual
  'vi-vn': 'Vietnamese 🇻🇳',
  'id-id': 'Indonesian 🇮🇩',
  'hi-in': 'Hindi 🇮🇳',
  'ar-sa': 'Arabic 🇸🇦',
  'ar-eg': 'Arabic 🇪🇬',
  'es-es': 'Spanish 🇪🇸',
  'es-mx': 'Spanish 🇲🇽',
  'fr-fr': 'French 🇫🇷',
  'de-de': 'German 🇩🇪',
  'pt-br': 'Portuguese 🇧🇷',
  'pt-pt': 'Portuguese 🇵🇹',
  'ru-ru': 'Russian 🇷🇺',
  'tr-tr': 'Turkish 🇹🇷',
  'en-us': 'English 🇺🇸',
  'en-gb': 'English 🇬🇧',
  'en-au': 'English 🇦🇺',
};

const detectedLang = computed(() => {
  const prefix = selected.value.substring(0, 5).toLowerCase();
  return PREFIX_LANG[prefix] || '';
});
</script>

<style scoped>
.voice-selector { display: flex; flex-direction: column; gap: 6px; }
.voice-label {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: #94A3B8;
}
.voice-lang-tag {
  font-size: 11.5px; font-weight: 700;
  background: rgba(124, 58, 237, 0.15);
  border: 1px solid rgba(124, 58, 237, 0.3);
  color: #C4B5FD;
  padding: 2px 9px; border-radius: 100px;
}
select option, select optgroup {
  background: #0E1120;
  color: #F1F5F9;
}
</style>