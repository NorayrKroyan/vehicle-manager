<!-- resources/js/components/vehicleManager/VehicleModal.vue -->
<template>
  <Modal
      ref="modalRef"
      :open="open"
      :title="mode === 'edit' ? 'Edit Vehicle' : 'New Vehicle'"
      :subtitle="''"
      @close="$emit('close')"
  >
    <div class="mx-auto w-full max-w-4xl space-y-3 pt-3">
      <!-- SECTION: VEHICLE -->
      <div class="bg-white">
        <div class="grid grid-cols-1 gap-3 py-2 lg:grid-cols-2">
          <!-- LEFT COL -->
          <div class="space-y-2">
            <RowField label="Vehicle Type:" :err="errors?.id_vehicle_type" v-slot="{ hasError }">
              <vSelect
                  v-model="form.id_vehicle_type"
                  :options="lookups?.vehicle_types || []"
                  :reduce="o => o.id"
                  :get-option-label="o => (o?.name ?? '')"
                  :clearable="true"
                  placeholder="Select type"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>

            <RowField label="VIN:" :err="errors?.vehicle_vin" v-slot="{ hasError }">
              <input
                  v-model="form.vehicle_vin"
                  type="text"
                  :class="['input', 'vin-input', hasError && 'input-error']"
                  placeholder="VIN"
              />
            </RowField>

            <RowField label="Year:" :err="errors?.vehicle_year" v-slot="{ hasError }">
              <input
                  v-model="form.vehicle_year"
                  type="number"
                  :class="['input', hasError && 'input-error']"
                  placeholder="Year"
              />
            </RowField>

            <RowField label="Make:" :err="errors?.id_vehicle_make" v-slot="{ hasError }">
              <vSelect
                  v-model="form.id_vehicle_make"
                  :options="lookups?.makes || []"
                  :reduce="o => (o.id_vehicle_make ?? o.id)"
                  :get-option-label="o => (o?.vehicle_make ?? o?.name ?? '')"
                  :clearable="true"
                  placeholder="Select make"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>

            <RowField label="Model:" :err="errors?.id_vehicle_model" v-slot="{ hasError }">
              <vSelect
                  v-model="form.id_vehicle_model"
                  :options="lookups?.models || []"
                  :reduce="o => (o.id_vehicle_model ?? o.id)"
                  :get-option-label="o => (o?.vehicle_model ?? o?.name ?? '')"
                  :clearable="true"
                  placeholder="Select model"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>

            <RowField label="Color:" :err="errors?.id_color" v-slot="{ hasError }">
              <vSelect
                  v-model="form.id_color"
                  :options="lookups?.colors || []"
                  :reduce="o => (o.id_color ?? o.id)"
                  :get-option-label="o => (o?.color ?? o?.name ?? '')"
                  :clearable="true"
                  placeholder="Select color"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>
          </div>

          <!-- RIGHT COL -->
          <div class="space-y-2">
            <RowField :label="numberLabel" :err="errors?.vehicle_number" v-slot="{ hasError }">
              <input
                  v-model="form.vehicle_number"
                  type="text"
                  :class="['input', hasError && 'input-error']"
                  placeholder="Number"
              />
            </RowField>

            <RowField label="License Plate/State:" :err="errors?.license_plate" v-slot="{ hasError }">
              <input
                  v-model="form.license_plate"
                  type="text"
                  :class="['input', hasError && 'input-error']"
                  placeholder="Plate"
              />
            </RowField>

            <RowField label="Registration State:" :err="errors?.registration_state_id" v-slot="{ hasError }">
              <vSelect
                  v-model="form.registration_state_id"
                  :options="lookups?.states || []"
                  :reduce="o => o.id"
                  :get-option-label="o => o.name"
                  :clearable="true"
                  placeholder="Select state"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>

            <RowField label="Owner:" :err="errors?.owner" v-slot="{ hasError }">
              <input
                  v-model="form.owner"
                  type="text"
                  :class="['input', hasError && 'input-error']"
                  placeholder="Owner"
              />
            </RowField>

            <RowField label="Insurance Provided By:" :err="errors?.insurance_provider" v-slot="{ hasError }">
              <vSelect
                  v-model="form.insurance_provider"
                  :options="insuranceProviderOptions"
                  :reduce="o => o.id"
                  :get-option-label="o => o.name"
                  :clearable="true"
                  placeholder="Select One"
                  class="w-full"
                  :class="hasError ? 'v-select-error' : ''"
              />
            </RowField>

            <RowField label="Process Rental and Billing:" :err="errors?.billing_active" v-slot="{ hasError }">
              <label class="flex items-center">
                <input
                    :checked="Number(form.billing_active) === 1"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300"
                    :class="hasError ? 'checkbox-error' : ''"
                    @change="form.billing_active = $event.target.checked ? 1 : 0"
                />
              </label>
            </RowField>
          </div>
        </div>

        <!-- TABS -->
        <div class="mt-4 border-b border-gray-200">
          <nav class="-mb-px flex items-center gap-6">
            <!-- Assignment -->
            <button
                type="button"
                class="whitespace-nowrap border-b-2 px-1 pb-2 text-sm font-medium"
                :disabled="!canAssignmentsDocs"
                :class="tabClass('assignment', canAssignmentsDocs)"
                @click="canAssignmentsDocs && onTabClick('assignment')"
            >
              Assignment History
            </button>

            <!-- Documents -->
            <button
                type="button"
                class="whitespace-nowrap border-b-2 px-1 pb-2 text-sm font-medium"
                :disabled="!canAssignmentsDocs"
                :class="tabClass('documents', canAssignmentsDocs)"
                @click="canAssignmentsDocs && onTabClick('documents')"
            >
              Documents
            </button>

            <!-- Rental -->
            <button
                type="button"
                class="whitespace-nowrap border-b-2 px-1 pb-2 text-sm font-medium"
                :disabled="!canRental"
                :class="tabClass('rental', canRental)"
                @click="canRental && onTabClick('rental')"
            >
              Rental
            </button>

            <button
                type="button"
                class="whitespace-nowrap border-b-2 border-transparent px-1 pb-2 text-sm font-medium text-gray-300 cursor-not-allowed"
                disabled
            >
              Billing History <span class="text-red-500">(future)</span>
            </button>

            <button
                type="button"
                class="whitespace-nowrap border-b-2 border-transparent px-1 pb-2 text-sm font-medium text-gray-300 cursor-not-allowed"
                disabled
            >
              Load History <span class="text-red-500">(future)</span>
            </button>
          </nav>
        </div>

        <!-- ASSIGNMENTS TAB -->
        <div v-if="activeTab === 'assignment'" class="mt-4 rounded-xl border-gray-200 bg-white">
          <div v-if="!canAssignmentsDocs" class="p-3 text-sm text-gray-500">
            Save the vehicle first to view assignment history.
          </div>

          <template v-else>
            <div v-if="assignmentsLoading" class="text-sm text-gray-500">Loading...</div>
            <div v-else class="overflow-hidden rounded-lg">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                <tr class="bg-gray-200">
                  <th class="px-3 py-2 text-left font-medium">Date</th>
                  <th class="px-3 py-2 text-left font-medium">Action</th>
                  <th class="px-3 py-2 text-left font-medium">Driver</th>
                  <th class="px-3 py-2 text-left font-medium">Current</th>
                </tr>
                </thead>
                <tbody class="divide-y bg-white">
                <tr v-for="r in assignments" :key="r.id">
                  <td class="px-3 py-2">{{ r.date_action }}</td>
                  <td class="px-3 py-2">{{ r.action }}</td>
                  <td class="px-3 py-2">{{ r.driver_name }}</td>
                  <td class="px-3 py-2">
                    <span
                        v-if="Number(r.is_current) === 1"
                        class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800"
                    >
                      CURRENT
                    </span>
                  </td>
                </tr>

                <tr v-if="!assignments.length">
                  <td class="px-3 py-3 text-sm text-gray-500" colspan="4">No records</td>
                </tr>
                </tbody>
              </table>
            </div>
          </template>
        </div>

        <!-- DOCUMENTS TAB -->
        <div v-else-if="activeTab === 'documents'" class="mt-4 rounded-xl border-gray-200 bg-white">
          <div v-if="!canAssignmentsDocs" class="p-3 text-sm text-gray-500">
            Save the vehicle first to view documents.
          </div>

          <template v-else>
            <div v-if="documentsLoading" class="text-sm text-gray-500 p-3">Loading...</div>

            <div v-else class="grid grid-cols-1 gap-3 p-3 md:grid-cols-4">
              <!-- LEFT list -->
              <div class="md:col-span-1">
                <div class="rounded-lg bg-gray-50 p-1">
                  <button
                      v-for="(d, i) in pagedDocuments"
                      :key="d.id_document"
                      type="button"
                      class="mb-0.5 w-full rounded-md px-2 py-1 text-left text-[11px] leading-4"
                      :class="selectedDocIndex === absoluteIndex(i)
                      ? 'bg-gray-900 text-white'
                      : 'bg-transparent text-gray-700 hover:bg-gray-200'"
                      @click="selectedDocIndex = absoluteIndex(i)"
                  >
                    <div class="font-medium truncate">{{ d.title }}</div>
                    <div class="text-[10px] opacity-70 truncate">
                      {{ d.doc_expiration ? ('Exp: ' + d.doc_expiration) : '' }}
                    </div>
                  </button>

                  <div v-if="!documents.length" class="p-2 text-xs text-gray-500">
                    No documents
                  </div>

                  <div v-if="documents.length" class="mt-2 flex items-center justify-between px-1 pb-1">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-2 py-1 text-[11px] hover:bg-gray-50 disabled:opacity-50"
                        :disabled="docPage === 1"
                        @click="docPage = Math.max(1, docPage - 1)"
                    >
                      Prev
                    </button>

                    <div class="text-[11px] text-gray-600">
                      {{ docPage }} / {{ docTotalPages }}
                    </div>

                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-2 py-1 text-[11px] hover:bg-gray-50 disabled:opacity-50"
                        :disabled="docPage >= docTotalPages"
                        @click="docPage = Math.min(docTotalPages, docPage + 1)"
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>

              <!-- RIGHT viewer -->
              <div class="md:col-span-3">
                <div class="rounded-lg bg-white p-2">
                  <template v-if="selectedDoc">
                    <div class="flex items-start justify-between gap-3">
                      <a
                          v-if="selectedDoc.file_url"
                          :href="selectedDoc.file_url"
                          target="_blank"
                          class="shrink-0 rounded-lg border-gray-300 border bg-white px-3 py-2 text-sm hover:bg-gray-50"
                      >
                        Open
                      </a>
                    </div>

                    <!-- viewer -->
                    <div
                        ref="viewerEl"
                        class="mt-2 rounded-lg border bg-white overflow-auto"
                        style="resize: both; height: 520px; min-height: 320px; min-width: 320px;"
                    >
                      <div v-if="!selectedDoc.file_url" class="p-3 text-sm text-gray-500">
                        No file URL for this document
                      </div>

                      <iframe
                          v-else
                          :src="selectedDoc.file_url"
                          class="w-full h-full"
                          frameborder="0"
                      />
                    </div>
                  </template>

                  <div v-else class="p-3 text-sm text-gray-500">
                    Select a document on the left
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- RENTAL TAB -->
        <div v-else-if="activeTab === 'rental'" class="mt-4 rounded-xl border border-gray-200 bg-white">
          <div v-if="!canRental" class="p-4 text-sm text-gray-500">
            Enable “Process Rental and Billing” to edit Rental settings.
          </div>

          <template v-else>
            <div class="rounded-t-xl bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-800">
              Rental
            </div>

            <div class="grid grid-cols-1 gap-3 px-4 py-3 lg:grid-cols-2">
              <div class="space-y-2">
                <RowField label="Billing Option:" :err="errors?.billing_option" v-slot="{ hasError }">
                  <vSelect
                      v-model="form.billing_option"
                      :options="lookups?.billing_options || []"
                      :reduce="o => (o.id ?? o.value ?? o)"
                      :get-option-label="o => (o?.name ?? String(o ?? ''))"
                      :clearable="true"
                      placeholder="Select option"
                      class="w-full"
                      :class="hasError ? 'v-select-error' : ''"
                  />
                </RowField>

                <RowField label="Daily Rent Rate:" :err="errors?.daily_rental_rate" v-slot="{ hasError }">
                  <input
                      v-model="form.daily_rental_rate"
                      type="number"
                      step="0.01"
                      :class="['input', 'input-xs', hasError && 'input-error']"
                      placeholder="0.00"
                  />
                </RowField>

                <RowField label="Weekly Rent Rate:" :err="errors?.weekly_rental_rate" v-slot="{ hasError }">
                  <input
                      v-model="form.weekly_rental_rate"
                      type="number"
                      step="0.01"
                      :class="['input', 'input-xs', hasError && 'input-error']"
                      placeholder="0.00"
                  />
                </RowField>
              </div>

              <div class="space-y-2">
                <RowField label="Estimated Monthly Expense:" :err="errors?.monthly_cost" v-slot="{ hasError }">
                  <input
                      v-model="form.monthly_cost"
                      type="number"
                      step="0.01"
                      :class="['input', 'input-xs', hasError && 'input-error']"
                      placeholder="0.00"
                  />
                </RowField>

                <RowField label="Day of Month for Payment:" :err="errors?.payment_dom" v-slot="{ hasError }">
                  <input
                      v-model="form.payment_dom"
                      type="number"
                      :class="['input', 'input-xs', hasError && 'input-error']"
                      placeholder="1-31"
                  />
                </RowField>

                <RowField label="Monthly Rent Rate:" :err="errors?.monthly_cost" v-slot="{ hasError }">
                  <input
                      v-model="form.monthly_cost"
                      type="number"
                      step="0.01"
                      :class="['input', 'input-xs', hasError && 'input-error']"
                      placeholder="0.00"
                  />
                </RowField>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex w-full items-center justify-end gap-3">
        <button
            type="button"
            class="rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm hover:bg-gray-50"
            :disabled="saving"
            @click="$emit('close')"
        >
          Cancel
        </button>

        <button
            type="button"
            class="rounded-xl bg-gray-900 px-8 py-3 text-sm font-semibold text-white hover:bg-gray-800 disabled:opacity-50"
            :disabled="saving"
            @click="$emit('save')"
        >
          Save
        </button>
      </div>
    </template>
  </Modal>
</template>

<script setup>
import { computed, ref, watch, nextTick, onBeforeUnmount } from 'vue'
import Modal from '@/components/ui/Modal.vue'
import RowField from '@/components/vehicleManager/parts/RowField.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { apiVmAssignments, apiVmDocuments } from '@/api/vehicle-manager'

const activeTab = ref('assignment')

const assignments = ref([])
const documents = ref([])
const assignmentsLoading = ref(false)
const documentsLoading = ref(false)

const props = defineProps({
  open: { type: Boolean, default: false },
  mode: { type: String, default: 'create' },
  saving: { type: Boolean, default: false },
  billingOn: { type: Boolean, default: false },
  insuranceProviderOptions: { type: Array, default: () => [] },
  errors: { type: Object, default: () => ({}) },
  form: { type: Object, required: true },
  lookups: { type: Object, default: () => ({}) },
})

defineEmits(['close', 'save'])

const form = props.form

const hasId = computed(() => Number(form?.id || 0) > 0)

// ✅ create: disable these until saved (id exists)
const canAssignmentsDocs = computed(() => hasId.value)

// ✅ rental becomes enabled when billing is checked (create/edit)
const billingEnabled = computed(() => Number(form.billing_active) === 1)
const canRental = computed(() => billingEnabled.value)

function tabClass(tab, enabled) {
  const active = activeTab.value === tab
  if (!enabled) return 'border-transparent text-gray-300 cursor-not-allowed'
  return active
      ? 'border-gray-900 text-gray-900'
      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
}

const numberLabel = computed(() => {
  const list = props.lookups?.vehicle_types || []
  const id = Number(form.id_vehicle_type || 0)
  const row = list.find(x => Number(x?.id ?? 0) === id)
  const typeName = String(row?.name ?? '').toLowerCase()

  if (typeName.includes('truck')) return 'Truck Number:'
  if (typeName.includes('trailer')) return 'Trailer Number:'
  return 'Vehicle Number:'
})

async function loadAssignments() {
  const id = Number(form?.id)
  if (!id) return
  assignmentsLoading.value = true
  try {
    const r = await apiVmAssignments(id)
    assignments.value = r?.rows || []
  } finally {
    assignmentsLoading.value = false
  }
}

async function loadDocuments() {
  const id = Number(form?.id)
  if (!id) return
  documentsLoading.value = true
  try {
    const r = await apiVmDocuments(id)
    documents.value = r?.rows || []
    docPage.value = 1
    selectedDocIndex.value = 0
  } finally {
    documentsLoading.value = false
  }
}

function onTabClick(tab) {
  if (tab === 'assignment' && !canAssignmentsDocs.value) return
  if (tab === 'documents' && !canAssignmentsDocs.value) return
  if (tab === 'rental' && !canRental.value) return

  activeTab.value = tab
  if (tab === 'assignment') loadAssignments()
  if (tab === 'documents') loadDocuments()
}

const selectedDocIndex = ref(0)

/** Pagination for left list (no scrolling) */
const docPage = ref(1)
const docPageSize = ref(8)

const docTotalPages = computed(() => {
  const n = documents.value?.length || 0
  return Math.max(1, Math.ceil(n / docPageSize.value))
})

const pagedDocuments = computed(() => {
  const n = documents.value?.length || 0
  if (!n) return []
  const page = Math.min(Math.max(1, docPage.value), docTotalPages.value)
  const start = (page - 1) * docPageSize.value
  return documents.value.slice(start, start + docPageSize.value)
})

function absoluteIndex(iInPage) {
  const page = Math.min(Math.max(1, docPage.value), docTotalPages.value)
  const start = (page - 1) * docPageSize.value
  return start + iInPage
}

const selectedDoc = computed(() => {
  if (!documents.value?.length) return null
  const i = Number(selectedDocIndex.value || 0)
  return documents.value[i] || documents.value[0] || null
})

/**
 * ✅ Make modal follow viewer resize
 */
const modalRef = ref(null)
const viewerEl = ref(null)

let ro = null
let lastViewerW = 0
let lastViewerH = 0
let raf = 0

function teardownViewerObserver() {
  if (ro) {
    try { ro.disconnect() } catch (e) {}
    ro = null
  }
  if (raf) {
    cancelAnimationFrame(raf)
    raf = 0
  }
  lastViewerW = 0
  lastViewerH = 0
}

async function setupViewerObserver() {
  teardownViewerObserver()
  await nextTick()

  const el = viewerEl.value
  const modal = modalRef.value
  if (!el || !modal?.getSize || !modal?.setSize) return

  const r0 = el.getBoundingClientRect()
  lastViewerW = r0.width
  lastViewerH = r0.height

  ro = new ResizeObserver(() => {
    if (raf) cancelAnimationFrame(raf)
    raf = requestAnimationFrame(() => {
      const r = el.getBoundingClientRect()
      const newW = r.width
      const newH = r.height

      const dw = newW - lastViewerW
      const dh = newH - lastViewerH

      if (Math.abs(dw) < 1 && Math.abs(dh) < 1) return

      const ms = modal.getSize()
      modal.setSize(ms.width + dw, ms.height + dh)

      lastViewerW = newW
      lastViewerH = newH
    })
  })

  ro.observe(el)
}

watch(
    () => props.open,
    (v) => {
      if (!v) {
        teardownViewerObserver()
        return
      }

      // default tab on open:
      // - if billing is enabled -> Rental
      // - else -> Assignment (will be disabled in create, but still visually consistent)
      activeTab.value = billingEnabled.value ? 'rental' : 'assignment'

      // only load data if id exists
      if (activeTab.value === 'assignment' && hasId.value) loadAssignments()
      if (activeTab.value === 'documents' && hasId.value) loadDocuments()
    }
)

watch(
    () => Number(form.billing_active),
    (v, prev) => {
      // when turned on -> go to Rental
      if (v === 1 && prev !== 1) activeTab.value = 'rental'
      // when turned off and currently on rental -> back to assignment
      if (v !== 1 && activeTab.value === 'rental') activeTab.value = 'assignment'
    }
)

watch(
    () => activeTab.value,
    async (tab) => {
      if (tab === 'documents' && hasId.value) await setupViewerObserver()
      else teardownViewerObserver()
    }
)

watch(
    () => selectedDoc.value?.id_document,
    async () => {
      if (activeTab.value === 'documents' && hasId.value) await setupViewerObserver()
    }
)

onBeforeUnmount(() => teardownViewerObserver())
</script>

<style scoped>
.input{
  width: 100%;
  height: 48px;
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  padding: 0 1rem;
  font-size: 0.95rem;
  outline: none;
}

.input:focus{
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px #3b82f6;
}

.input-xs{ width: 120px; text-align: center; }

.vin-input{
  font-family: monospace;
  letter-spacing: 0.5px;
  overflow-x: auto;
  white-space: nowrap;
}

.input-error{
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 1px #ef4444 !important;
}

:deep(.v-select) { width: 100%; }
:deep(.vs__dropdown-toggle){
  border: 1px solid #d1d5db;
  border-radius: 0.75rem;
  height: 48px;
  min-height: 48px;
  padding: 0 0.75rem;
  display: flex;
  align-items: center;
}

:deep(.v-select-error .vs__dropdown-toggle){
  border-color: #ef4444 !important;
  box-shadow: 0 0 0 1px #ef4444 !important;
}

.checkbox-error{
  outline: 2px solid #ef4444;
  outline-offset: 2px;
}

:deep(.vs__dropdown-menu){
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  overflow: auto;
  z-index: 60;
}
:deep(.vs__dropdown-menu){ position: absolute; }


</style>