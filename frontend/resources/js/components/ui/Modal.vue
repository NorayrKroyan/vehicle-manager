<template>
  <teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50">
      <div class="absolute inset-0 bg-black/40" @click="$emit('close')" />

      <div class="absolute inset-0 flex items-center justify-center p-2 sm:p-4">
        <!-- ✅ RESIZABLE MODAL PANEL -->
        <div
            ref="panelEl"
            class="rounded-2xl bg-white shadow-xl border border-gray-200 flex flex-col"
            style="
            resize: both;
            overflow: auto;

            /* starting size */
            width: min(1024px, 95vw);
            height: min(820px, 95vh);

            /* limits */
            min-width: 360px;
            min-height: 360px;
            max-width: 95vw;
            max-height: 95vh;
          "
            @click.stop
        >
          <!-- header -->
          <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-4 py-3 shrink-0">
            <div class="min-w-0">
              <div v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</div>
              <div class="text-xl font-semibold text-gray-900 truncate">{{ title }}</div>
            </div>
          </div>

          <!-- body -->
          <div class="px-4 py-3 flex-1 overflow-auto">
            <slot />
          </div>

          <!-- footer -->
          <div class="border-t border-gray-200 px-4 py-3 flex items-center justify-end gap-2 shrink-0">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  open: Boolean,
  title: String,
  subtitle: String,
})
defineEmits(['close'])

const panelEl = ref(null)

/**
 * Allow parent to resize the modal panel programmatically.
 * We clamp to viewport using max 95vw/95vh, and min sizes.
 */
function setSize(widthPx, heightPx) {
  const el = panelEl.value
  if (!el) return

  const minW = 360
  const minH = 360

  const maxW = Math.floor(window.innerWidth * 0.95)
  const maxH = Math.floor(window.innerHeight * 0.95)

  const w = Math.max(minW, Math.min(maxW, Math.floor(Number(widthPx || 0))))
  const h = Math.max(minH, Math.min(maxH, Math.floor(Number(heightPx || 0))))

  el.style.width = `${w}px`
  el.style.height = `${h}px`
}

function getSize() {
  const el = panelEl.value
  if (!el) return { width: 0, height: 0 }
  const r = el.getBoundingClientRect()
  return { width: r.width, height: r.height }
}

defineExpose({ setSize, getSize })
</script>