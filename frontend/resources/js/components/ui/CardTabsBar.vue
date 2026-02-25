<template>
  <div class="w-full">
    <!-- Tabs header area (like card header) -->
    <div class="rounded-t-xl border border-gray-200 bg-white">
      <div class="flex items-end gap-1 px-2 pt-2">
        <button
            v-for="t in tabs"
            :key="t.key"
            type="button"
            :disabled="!!t.disabled"
            class="relative -mb-px select-none rounded-t-lg px-4 py-2 text-sm font-medium transition"
            :class="tabClass(t)"
            @click="onClick(t)"
        >
          {{ t.label }}
        </button>
      </div>

      <!-- Divider line under the tabs -->
      <div class="border-t border-gray-200"></div>
    </div>

    <!-- Content panel (card body) -->
    <div class="rounded-b-xl border border-t-0 border-gray-200 bg-white p-4">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, required: true },
  tabs: { type: Array, required: true }, // [{key,label,disabled?}]
})

const emit = defineEmits(['update:modelValue'])

const active = computed(() => String(props.modelValue || ''))

function onClick(t) {
  if (!t || t.disabled) return
  emit('update:modelValue', String(t.key))
}

function tabClass(t) {
  const isActive = String(t.key) === active.value
  const isDisabled = !!t.disabled

  if (isDisabled) {
    return [
      'cursor-not-allowed text-gray-400',
      'border border-transparent',
    ].join(' ')
  }

  // Active tab "connected" to body: white bg + border + no bottom border (handled by -mb-px)
  if (isActive) {
    return [
      'bg-white text-gray-900',
      'border border-gray-200 border-b-white',
      'shadow-[0_1px_0_0_rgba(255,255,255,1)]',
    ].join(' ')
  }

  // Inactive tab: looks like link sitting on header
  return [
    'bg-transparent text-blue-600 hover:text-blue-800',
    'border border-transparent',
  ].join(' ')
}
</script>