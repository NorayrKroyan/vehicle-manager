<!-- resources/js/pages/VehicleManagerPage.vue -->
<template>
  <div class="p-6">
    <!-- HEADER -->
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Vehicle Manager</h1>
      </div>
      <button
          class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
          @click="vmModal.openCreate()"
      >
        New Vehicle
      </button>
    </div>

    <!-- SEARCH + TYPE FILTER -->
    <div class="mt-1">
      <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div class="w-full md:max-w-[520px]">
          <input
              v-model="vm.q.value"
              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm
                 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
              placeholder="Search vehicles (name, number, type, VIN...)"
          />
        </div>

        <!-- right side filter -->
        <div class="flex justify-end">
          <div class="inline-flex overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
            <button
                type="button"
                class="px-4 py-2 text-sm font-semibold"
                :class="vm.type.value === 'all' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200'"
                @click="vm.type.value = 'all'"
            >
              Any
            </button>

            <button
                type="button"
                class="px-4 py-2 text-sm font-semibold border-l border-gray-200"
                :class="Number(vm.type.value) === 2 ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200'"
                @click="vm.type.value = '2'"
            >
              Truck
            </button>

            <button
                type="button"
                class="px-4 py-2 text-sm font-semibold border-l border-gray-200"
                :class="Number(vm.type.value) === 1 ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-200'"
                @click="vm.type.value = '1'"
            >
              Trailer
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ERROR -->
    <div
        v-if="vm.err.value"
        class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
    >
      {{ vm.err.value }}
    </div>

    <!-- CARD -->
    <div class="mt-1 rounded-xl bg-white shadow-sm">
      <!-- HEADER ROW -->
      <div class="mt-2">
        <div
            class="grid grid-cols-[1.4fr_0.8fr_0.8fr_1.2fr_0.6fr_0.8fr] gap-3 rounded-md bg-gray-200 px-3 py-2 text-[18px] font-bold text-gray-900"
        >
          <div>Vehicle Name</div>
          <div>Type</div>
          <div>Number</div>
          <div>VIN</div>
          <div>Year</div>
          <div class="text-right">Owner</div>
        </div>
      </div>

      <!-- LIST -->
      <div class="pt-2">
        <div class="space-y-1">
          <button
              v-for="(r, idx) in vm.list.value"
              :key="r.id"
              type="button"
              class="w-full rounded-md text-left transition-colors"
              :class="rowClass(r, idx)"
              @click="vm.selectVehicle(r.id)"
          >
            <div class="grid grid-cols-[1.4fr_0.8fr_0.8fr_1.2fr_0.6fr_0.8fr] gap-3 px-3 py-1.5 text-xs">
              <div class="min-w-0">
                <button
                    type="button"
                    class="block truncate font-medium text-blue-700 hover:text-blue-900 hover:underline"
                    @click.stop.prevent="vmModal.openEdit(r)"
                >
                  {{ r.vehicle_name || '—' }}
                </button>
              </div>
              <div class="truncate text-gray-700">{{ r.vehicle_type_name || '—' }}</div>
              <div class="truncate text-gray-700">{{ r.vehicle_number || '—' }}</div>
              <div class="truncate text-gray-700">{{ r.vehicle_vin || '—' }}</div>
              <div class="truncate text-gray-700">{{ r.vehicle_year || '—' }}</div>
              <div class="truncate text-gray-700 text-right">{{ r.owner || '—' }}</div>
            </div>
          </button>

          <div v-if="!vm.list.value.length" class="py-4 text-sm text-gray-500">
            No vehicles.
          </div>
        </div>
      </div>

      <!-- PAGINATION -->
      <div class="py-4">
        <div class="flex items-center justify-between gap-3">
          <div class="text-xs text-gray-500">
            Page {{ vm.page.value }}{{ vm.totalPages.value ? ` / ${vm.totalPages.value}` : '' }}
          </div>

          <div class="flex items-center gap-2">
            <select
                v-model.number="vm.limit.value"
                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-xs"
            >
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>

            <button
                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-xs hover:bg-gray-50 disabled:opacity-50"
                :disabled="vm.page.value <= 1 || vm.loading.value"
                @click="vm.page.value -= 1"
            >
              Prev
            </button>

            <button
                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-xs hover:bg-gray-50 disabled:opacity-50"
                :disabled="!vm.canNext.value || vm.loading.value"
                @click="vm.page.value += 1"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- VEHICLE MODAL -->
    <VehicleModal
        :open="vmModal.open.value"
        :mode="vmModal.mode.value"
        :saving="vmModal.saving.value || vmModal.deleting.value"
        :errors="vmModal.errors.value"
        :form="vmModal.form"
        :lookups="vm.lookups.value"
        :billingOn="vmModal.billingOn.value"
        :insuranceProviderOptions="vmModal.insuranceProviderOptions"
        @close="vmModal.close()"
        @save="vmModal.save().then(afterSave)"
        @delete="vmModal.del().then(async (r) => { if (r?.ok) await vm.reloadList() })"
    />

    <!-- DOC MODAL -->
    <Modal :open="docOpen" title="Document Viewer" subtitle="Preview" @close="docOpen = false">
      <iframe
          v-if="vm.activeDoc.value?.file_url"
          :src="vm.activeDoc.value.file_url"
          class="h-[75vh] w-full rounded-lg border border-gray-200"
      />
      <div v-else class="text-sm text-gray-500">No file URL.</div>

      <template #footer>
        <button class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50" @click="docOpen = false">
          Close
        </button>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { useVehicleManagerPage } from '@/composables/useVehicleManagerPage'
import { useVehicleModal } from '@/composables/useVehicleModal'

import CardTabsBar from '@/components/ui/CardTabsBar.vue'
import Modal from '@/components/ui/Modal.vue'
import VehicleModal from '@/components/vehicleManager/VehicleModal.vue'

const vm = useVehicleManagerPage()
const vmModal = useVehicleModal()
const docOpen = ref(false)

const tabs = [
  { key: 'assignments', label: 'Assignment History' },
  { key: 'documents', label: 'Documents' },
  { key: 'billing', label: 'Billing History (future)', disabled: true },
  { key: 'loads', label: 'Load History (future)', disabled: true },
]

// tabs model must be string
const activeTabModel = computed({
  get: () => vm.activeTab.value,
  set: (v) => { vm.activeTab.value = String(v || 'assignments') },
})

// zebra without border lines
function rowClass(r, idx) {
  if (vm.selected.value?.id === r.id) return 'bg-blue-100 hover:bg-blue-100'
  return (idx % 2 ? 'bg-white' : 'bg-gray-50') + ' hover:bg-gray-100'
}

async function afterSave(res) {
  if (!res?.ok) return
  await vm.reloadList()
  if (res.id) await vm.selectVehicle(res.id)
}

// debounce + reset page
let t = null
watch(
    () => [vm.q.value, vm.type.value],
    () => {
      vm.page.value = 1
      if (t) clearTimeout(t)
      t = setTimeout(() => vm.reloadList(), 350)
    }
)

// page/limit reload
watch(
    () => [vm.page.value, vm.limit.value],
    () => vm.reloadList()
)

onMounted(vm.boot)
</script>