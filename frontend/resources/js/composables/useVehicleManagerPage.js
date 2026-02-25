// resources/js/composables/useVehicleManagerPage.js
import { ref, computed } from 'vue'
import {
    apiVmLookups,
    apiVmVehicles,
    apiVmVehicle,
    apiVmAssignments,
    apiVmDocuments,
} from '@/api/vehicle-manager'

export function useVehicleManagerPage() {
    const loading = ref(false)
    const err = ref('')

    const q = ref('')
    const type = ref('all') // 'all' | 1 | 2
    const list = ref([])

    // pagination
    const page = ref(1)
    const limit = ref(50)
    const total = ref(null) // optional if backend returns total

    const totalPages = computed(() => {
        if (!total.value) return null
        return Math.max(1, Math.ceil(Number(total.value) / Number(limit.value || 1)))
    })

    const canNext = computed(() => {
        // If backend provides total -> use it
        if (totalPages.value) return page.value < totalPages.value
        // fallback: if we got a full page, assume there might be next
        return Array.isArray(list.value) && list.value.length === Number(limit.value)
    })

    const lookups = ref({
        vehicle_types: [],
        makes: [],
        models: [],
        states: [],
        colors: [],
        billing_options: [],
    })

    const selected = ref(null)
    const activeTab = ref('assignments')

    const assignments = ref([])
    const documents = ref([])
    const activeDocId = ref(null)

    const activeDoc = computed(() => {
        return documents.value.find(d => String(d.id_document) === String(activeDocId.value)) || null
    })

    async function boot() {
        loading.value = true
        err.value = ''
        try {
            const r1 = await apiVmLookups()
            if (!r1.ok) throw new Error('Lookups failed')
            lookups.value = r1.lookups

            await reloadList()
        } catch (e) {
            err.value = e?.message || 'Error'
        } finally {
            loading.value = false
        }
    }

    async function reloadList() {
        loading.value = true
        err.value = ''
        try {
            const r = await apiVmVehicles({
                q: q.value,
                type: type.value,     // ✅ add
                page: page.value,
                limit: limit.value,
            })
            if (!r.ok) throw new Error(r.error || 'Vehicles load failed')

            // support both response shapes:
            // A) { ok:true, rows:[...], total:123 }
            // B) { ok:true, rows:[...] }
            list.value = r.rows || []
            total.value = (r.total != null) ? Number(r.total) : null
        } catch (e) {
            err.value = e?.message || 'Error'
        } finally {
            loading.value = false
        }
    }

    async function selectVehicle(id) {
        loading.value = true
        err.value = ''
        try {
            const r = await apiVmVehicle(id)
            if (!r.ok) throw new Error(r.error || 'Vehicle not found')
            selected.value = r.vehicle

            const [a, d] = await Promise.all([apiVmAssignments(id), apiVmDocuments(id)])
            assignments.value = a.ok ? a.rows : []
            documents.value = d.ok ? d.rows : []
            activeDocId.value = documents.value[0]?.id_document ?? null
        } catch (e) {
            err.value = e?.message || 'Error'
        } finally {
            loading.value = false
        }
    }

    return {
      loading, err,
      q, type, list,  // ✅ include type
      page, limit, total, totalPages, canNext,
      lookups,
      selected, activeTab,
      assignments, documents, activeDocId, activeDoc,
      boot, reloadList, selectVehicle,
    }
}