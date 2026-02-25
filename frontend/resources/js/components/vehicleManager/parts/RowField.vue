<template>
  <div class="cd-row">
    <div class="cd-label">
      <span>{{ label }}</span>
    </div>

    <div class="cd-field">
      <!-- ✅ do not change your layout, only add slot props -->
      <slot :hasError="hasError" :errorText="errorText" />

      <!-- ✅ show first error text -->
      <div v-if="errorText" class="mt-1 text-xs text-red-600">{{ errorText }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: { type: String, default: '' },
  err: { type: [String, Array, Object], default: '' },
})

const hasError = computed(() => {
  const e = props.err
  if (!e) return false

  if (typeof e === 'string' && e.trim().length) return true
  if (Array.isArray(e) && e.length) return true
  if (typeof e === 'object' && Object.keys(e).length) return true

  return false
})

const errorText = computed(() => {
  return hasError.value ? 'Validation error' : ''
})
</script>

<style scoped>
.cd-row{
  display: grid;
  align-items: center;
  column-gap: 0.75rem;
  grid-template-columns: 300px 1fr;
}
.cd-label{
  text-align: right;
  white-space: nowrap;
  font-size: 0.95rem;
  font-weight: 600;
  color: #374151;
}
.cd-field{
  min-width: 0;
}
</style>