<template>
  <div class="voice-selector">
    <label class="voice-label">
      🎙️ Voice / Language
      <span class="voice-lang-tag" v-if="detectedLang">{{ detectedLang }}</span>
    </label>

    <div v-if="loading" class="loading-state">Loading voices...</div>

    <select
      v-else
      v-model="selected"
      class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] cursor-pointer outline-none focus:border-[#7C3AED]"
      :disabled="isProcessing"
      @change="$emit('update:modelValue', selected)"
    >
      <optgroup
        v-for="group in groupedVoices"
        :key="group.locale"
        :label="group.label"
      >
        <option
          v-for="v in group.voices"
          :key="v.name"
          :value="v.name"
        >
          {{ flag(v.locale) }} {{ v.name.split('-')[2].replace('Neural','') }} ({{ v.gender }})
        </option>
      </optgroup>
    </select>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  modelValue:   { type: String, default: 'my-MM-ThihaNeural' },
  isProcessing: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);

const selected = ref(props.modelValue);
const voices   = ref([]);
const loading  = ref(true);

const LOCALE_META = {
  'my-MM': { label: '🇲🇲 Burmese (မြန်မာ)',          flag: '🇲🇲', order: 1  },
  'th-TH': { label: '🇹🇭 Thai (ไทย)',                flag: '🇹🇭', order: 2  },
  'ja-JP': { label: '🇯🇵 Japanese (日本語)',           flag: '🇯🇵', order: 3  },
  'zh-CN': { label: '🇨🇳 Chinese Simplified (普通话)', flag: '🇨🇳', order: 4  },
  'zh-TW': { label: '🇹🇼 Chinese Traditional (繁體)', flag: '🇹🇼', order: 5  },
  'ko-KR': { label: '🇰🇷 Korean (한국어)',             flag: '🇰🇷', order: 6  },
  'vi-VN': { label: '🇻🇳 Vietnamese (Tiếng Việt)',    flag: '🇻🇳', order: 7  },
  'id-ID': { label: '🇮🇩 Indonesian (Bahasa)',         flag: '🇮🇩', order: 8  },
  'hi-IN': { label: '🇮🇳 Hindi (हिन्दी)',              flag: '🇮🇳', order: 9  },
  'ar-SA': { label: '🇸🇦 Arabic (Saudi)',              flag: '🇸🇦', order: 10 },
  'ar-EG': { label: '🇪🇬 Arabic (Egypt)',              flag: '🇪🇬', order: 11 },
  'es-ES': { label: '🇪🇸 Spanish (Spain)',             flag: '🇪🇸', order: 12 },
  'es-MX': { label: '🇲🇽 Spanish (Mexico)',            flag: '🇲🇽', order: 13 },
  'fr-FR': { label: '🇫🇷 French (Français)',           flag: '🇫🇷', order: 14 },
  'de-DE': { label: '🇩🇪 German (Deutsch)',            flag: '🇩🇪', order: 15 },
  'pt-BR': { label: '🇧🇷 Portuguese (Brazil)',         flag: '🇧🇷', order: 16 },
  'pt-PT': { label: '🇵🇹 Portuguese (Portugal)',       flag: '🇵🇹', order: 17 },
  'ru-RU': { label: '🇷🇺 Russian (Русский)',           flag: '🇷🇺', order: 18 },
  'tr-TR': { label: '🇹🇷 Turkish (Türkçe)',            flag: '🇹🇷', order: 19 },
  'en-US': { label: '🇺🇸 English (US)',                flag: '🇺🇸', order: 20 },
  'en-GB': { label: '🇬🇧 English (UK)',                flag: '🇬🇧', order: 21 },
  'en-AU': { label: '🇦🇺 English (Australia)',         flag: '🇦🇺', order: 22 },
};

onMounted(async () => {
  try {
    const res  = await fetch('https://recap.zakerxa.com/voices');
    const data = await res.json();
    voices.value = data;
  } catch (e) {
    // Fallback to Burmese only if API fails
    voices.value = [
      { name: 'my-MM-ThihaNeural',  gender: 'Male',   locale: 'my-MM' },
      { name: 'my-MM-NilarNeural',  gender: 'Female', locale: 'my-MM' },
    ];
  } finally {
    loading.value = false;
  }
});

const groupedVoices = computed(() => {
  const map = {};
  for (const v of voices.value) {
    if (!map[v.locale]) map[v.locale] = [];
    map[v.locale].push(v);
  }
  return Object.entries(map)
    .map(([locale, vs]) => ({
      locale,
      label:  LOCALE_META[locale]?.label || locale,
      order:  LOCALE_META[locale]?.order || 99,
      voices: vs,
    }))
    .sort((a, b) => a.order - b.order);
});

const flag = (locale) => LOCALE_META[locale]?.flag || '🌐';

const PREFIX_LANG = {
  'my-mm': 'Burmese 🇲🇲', 'th-th': 'Thai 🇹🇭', 'ja-jp': 'Japanese 🇯🇵',
  'zh-cn': 'Chinese 🇨🇳',  'zh-tw': 'Chinese 🇹🇼', 'ko-kr': 'Korean 🇰🇷',
  'vi-vn': 'Vietnamese 🇻🇳','id-id': 'Indonesian 🇮🇩','hi-in': 'Hindi 🇮🇳',
  'ar-sa': 'Arabic 🇸🇦',   'ar-eg': 'Arabic 🇪🇬',  'es-es': 'Spanish 🇪🇸',
  'es-mx': 'Spanish 🇲🇽',  'fr-fr': 'French 🇫🇷',  'de-de': 'German 🇩🇪',
  'pt-br': 'Portuguese 🇧🇷','pt-pt': 'Portuguese 🇵🇹','ru-ru': 'Russian 🇷🇺',
  'tr-tr': 'Turkish 🇹🇷',  'en-us': 'English 🇺🇸',  'en-gb': 'English 🇬🇧',
  'en-au': 'English 🇦🇺',
};

const detectedLang = computed(() =>
  PREFIX_LANG[selected.value.substring(0, 5).toLowerCase()] || ''
);
</script>

<style scoped>
.voice-selector { display: flex; flex-direction: column; gap: 6px; }
.voice-label {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: #94A3B8;
}
.voice-lang-tag {
  font-size: 11.5px; font-weight: 700;
  background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.3);
  color: #C4B5FD; padding: 2px 9px; border-radius: 100px;
}
.loading-state {
  font-size: 13px; color: #475569; padding: 10px 14px;
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
}
select option, select optgroup { background: #0E1120; color: #F1F5F9; }
</style>