<!-- resources/js/components/ui/CardTabs.vue -->
<template>
  <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
    <div class="border-b bg-gray-50 px-3 pt-2">
      <div class="flex items-end gap-2 overflow-x-auto">
        <button
            v-for="t in tabsSafe"
            :key="t.key"
            type="button"
            class="whitespace-nowrap select-none rounded-t-lg border border-b-0 px-3 py-2 text-sm font-medium"
            :class="tabClass(t)"
            :disabled="t.disabled"
            @click="onClickTab(t)"
        >
          {{ t.label }}
        </button>
      </div>
    </div>

    <div class="p-4">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, required: true },
  tabs: { type: Array, required: true }, // [{ key, label, disabled }]
})

const emit = defineEmits(['update:modelValue'])

const tabsSafe = computed(() => {
  const arr = Array.isArray(props.tabs) ? props.tabs : []
  return arr
      .map(t => ({
        key: String(t?.key ?? ''),
        label: String(t?.label ?? ''),
        disabled: !!t?.disabled,
      }))
      .filter(t => t.key.length > 0)
})

function onClickTab(t) {
  if (t.disabled) return
  const key = String(t.key)
  if (props.modelValue === key) return
  emit('update:modelValue', key)
}

function tabClass(t) {
  if (t.disabled) return 'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200'
  if (props.modelValue === t.key) return 'bg-white text-gray-900 border-gray-200 relative -mb-px'
  return 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-white'
}
</script>