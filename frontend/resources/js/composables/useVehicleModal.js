// resources/js/composables/useVehicleModal.js
import { reactive, ref, computed } from 'vue'
import {
    apiVmCreateVehicle,
    apiVmUpdateVehicle,
    apiVmDeleteVehicle,
    apiVmAssignments,
    apiVmDocuments,
} from '@/api/vehicle-manager'

const activeTab = ref('assignment')

const assignments = ref([])
const documents = ref([])

const assignmentsLoading = ref(false)
const documentsLoading = ref(false)

async function loadAssignments(vehicleId) {
    if (!vehicleId) return
    assignmentsLoading.value = true
    try {
        const r = await apiVmAssignments(vehicleId)
        assignments.value = r?.rows || []
    } finally {
        assignmentsLoading.value = false
    }
}

async function loadDocuments(vehicleId) {
    if (!vehicleId) return
    documentsLoading.value = true
    try {
        const r = await apiVmDocuments(vehicleId)
        documents.value = r?.rows || []
    } finally {
        documentsLoading.value = false
    }
}

function normalizeErrorsFromException(e) {
    const data = e?.response?.data || e?.data || null
    const out = { _error: 'Save failed.', fields: {} }

    if (!data) {
        if (e?.message) out._error = e.message
        return out
    }

    if (typeof data === 'string') {
        out._error = data
        return out
    }

    if (data.message) out._error = data.message
    if (data.error && !data.message) out._error = data.error

    if (data.errors && typeof data.errors === 'object') {
        out.fields = data.errors
        const firstKey = Object.keys(out.fields)[0]
        if (firstKey) {
            const v = out.fields[firstKey]
            out._error = Array.isArray(v) ? (v[0] || out._error) : String(v)
        }
    }

    return out
}

function normalizeErrorsFromRes(res) {
    const out = { _error: 'Save failed.', fields: {} }
    if (!res) return out

    if (res.message) out._error = res.message
    if (res.error && !res.message) out._error = res.error

    if (res.errors && typeof res.errors === 'object') {
        out.fields = res.errors
        const firstKey = Object.keys(out.fields)[0]
        if (firstKey) {
            const v = out.fields[firstKey]
            out._error = Array.isArray(v) ? (v[0] || out._error) : String(v)
        }
    }

    return out
}

function toNullIfEmpty(v) {
    const s = String(v ?? '').trim()
    return s === '' ? null : s
}

export function useVehicleModal() {
    const open = ref(false)
    const mode = ref('create') // create|edit
    const saving = ref(false)
    const deleting = ref(false)
    const errors = ref({})

    const insuranceProviderOptions = [
        { id: 1, name: 'Carrier' },
        { id: 2, name: 'Client' },
        { id: 3, name: 'Our Company' },
        { id: 4, name: 'Rental Agency' },
    ]

    const form = reactive({
        id: null,

        id_vehicle_type: null,

        // ✅ Truck/Trailer Number in DB
        vehicle_name: '',

        // ✅ License Plate in DB
        vehicle_number: '',

        vehicle_vin: '',
        vehicle_year: null,

        id_vehicle_make: null,
        id_vehicle_model: null,
        id_color: null,

        owner: '',
        insurance_provider: null,

        registration_state_id: null,

        billing_active: 0,
        billing_option: 'No Billing',
        monthly_cost: null,
        daily_rental_rate: null,
        weekly_rental_rate: null,
        payment_dom: null,
    })

    const billingOn = computed(() => Number(form.billing_active) === 1)
    const canDelete = computed(() => mode.value === 'edit' && Number(form.id || 0) > 0)

    function reset() {
        errors.value = {}
        form.id = null

        form.id_vehicle_type = null
        form.vehicle_name = ''
        form.vehicle_number = ''
        form.vehicle_vin = ''
        form.vehicle_year = null

        form.id_vehicle_make = null
        form.id_vehicle_model = null
        form.id_color = null

        form.owner = ''
        form.insurance_provider = 4

        form.registration_state_id = null

        form.billing_active = 0
        form.billing_option = 'No Billing'
        form.monthly_cost = null
        form.daily_rental_rate = null
        form.weekly_rental_rate = null
        form.payment_dom = null

        activeTab.value = 'assignment'
        assignments.value = []
        documents.value = []
    }

    function openCreate() {
        reset()
        mode.value = 'create'
        open.value = true
    }

    function openEdit(v) {
        reset()
        mode.value = 'edit'
        open.value = true

        form.id = v.id
        form.id_vehicle_type = v.id_vehicle_type

        // ✅ Truck/Trailer Number
        form.vehicle_name = v.vehicle_name || ''

        // ✅ License Plate
        form.vehicle_number = v.vehicle_number || ''

        form.vehicle_vin = v.vehicle_vin || ''
        form.vehicle_year = v.vehicle_year ?? null

        form.id_vehicle_make = v.id_vehicle_make
        form.id_vehicle_model = v.id_vehicle_model ?? null
        form.id_color = v.id_color
        form.owner = v.owner || ''
        form.insurance_provider = (v.insurance_provider == null ? null : Number(v.insurance_provider))

        form.registration_state_id = (v.registration_state_id == null ? null : Number(v.registration_state_id))

        form.billing_active = Number(v.billing_active || 0)
        form.billing_option = v.billing_option || 'No Billing'
        form.monthly_cost = v.monthly_cost ?? null
        form.daily_rental_rate = v.daily_rental_rate ?? null
        form.weekly_rental_rate = v.weekly_rental_rate ?? null
        form.payment_dom = v.payment_dom ?? null
    }

    function close() {
        open.value = false
    }

    function payload() {
        const on = Number(form.billing_active) === 1

        return {
            id_vehicle_type: form.id_vehicle_type,

            // ✅ Truck/Trailer Number
            vehicle_name: toNullIfEmpty(form.vehicle_name),

            // ✅ License Plate
            vehicle_number: toNullIfEmpty(form.vehicle_number),

            vehicle_vin: toNullIfEmpty(form.vehicle_vin),
            vehicle_year: (form.vehicle_year == null || form.vehicle_year === '') ? null : Number(form.vehicle_year),

            id_vehicle_make: form.id_vehicle_make,
            id_vehicle_model: form.id_vehicle_model || null,
            id_color: form.id_color,

            owner: toNullIfEmpty(form.owner),
            insurance_provider: (form.insurance_provider == null ? null : Number(form.insurance_provider)),

            registration_state_id: (form.registration_state_id == null || form.registration_state_id === '')
                ? null
                : Number(form.registration_state_id),

            billing_active: on ? 1 : 0,
            billing_option: on ? (form.billing_option || 'No Billing') : 'No Billing',
            monthly_cost: on ? (form.monthly_cost ?? null) : null,
            daily_rental_rate: on ? (form.daily_rental_rate ?? null) : null,
            weekly_rental_rate: on ? (form.weekly_rental_rate ?? null) : null,
            payment_dom: on ? (form.payment_dom ?? null) : null,
        }
    }

    async function save() {
        saving.value = true
        errors.value = {}

        try {
            const p = payload()

            const res = (mode.value === 'create')
                ? await apiVmCreateVehicle(p)
                : await apiVmUpdateVehicle(form.id, p)

            if (res && res.ok === false) {
                const n = normalizeErrorsFromRes(res)
                errors.value = { _error: n._error, ...n.fields }
                return { ok: false }
            }

            open.value = false
            return { ok: true, id: res?.id || form.id }
        } catch (e) {
            const n = normalizeErrorsFromException(e)
            errors.value = { _error: n._error, ...n.fields }
            return { ok: false }
        } finally {
            saving.value = false
        }
    }

    // ✅ Delete without popup
    async function del() {
        if (!canDelete.value) return { ok: false }
        if (saving.value || deleting.value) return { ok: false }

        deleting.value = true
        errors.value = {}

        try {
            const res = await apiVmDeleteVehicle(form.id)

            if (res && res.ok === false) {
                const n = normalizeErrorsFromRes(res)
                errors.value = { _error: n._error, ...n.fields }
                return { ok: false }
            }

            open.value = false
            return { ok: true }
        } catch (e) {
            const n = normalizeErrorsFromException(e)
            errors.value = { _error: n._error, ...n.fields }
            return { ok: false }
        } finally {
            deleting.value = false
        }
    }

    function onTabClick(tab, vehicleId) {
        activeTab.value = tab
        if (tab === 'assignment') loadAssignments(vehicleId)
        if (tab === 'documents') loadDocuments(vehicleId)
    }

    return {
        open,
        mode,
        saving,
        deleting,
        errors,
        form,
        billingOn,
        canDelete,
        insuranceProviderOptions,
        openCreate,
        openEdit,
        close,
        save,
        del,

        activeTab,
        assignments,
        documents,
        assignmentsLoading,
        documentsLoading,
        onTabClick,
    }
}