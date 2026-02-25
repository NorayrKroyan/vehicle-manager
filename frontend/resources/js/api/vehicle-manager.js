import { fetchJson } from './fetchJson'

export function apiVmLookups() {
    return fetchJson('/api/vehicle-manager/lookups')
}

export function apiVmVehicles({ q = '', type = 'all', page = 1, limit = 50 } = {}) {
    return fetchJson('/api/vehicle-manager/vehicles', {
        method: 'GET',
        params: { q, type, page, limit },
    })
}

export function apiVmVehicle(id) {
    return fetchJson(`/api/vehicle-manager/vehicles/${id}`, { method: 'GET' })
}

export function apiVmCreateVehicle(payload) {
    return fetchJson('/api/vehicle-manager/vehicles', { method: 'POST', data: payload })
}

export function apiVmUpdateVehicle(id, payload) {
    return fetchJson(`/api/vehicle-manager/vehicles/${id}`, { method: 'PUT', data: payload })
}

// ✅ NEW
export function apiVmDeleteVehicle(id) {
    return fetchJson(`/api/vehicle-manager/vehicles/${id}`, { method: 'DELETE' })
}

export function apiVmAssignments(vehicleId) {
    return fetchJson(`/api/vehicle-manager/vehicles/${vehicleId}/assignments`, { method: 'GET' })
}

export function apiVmDocuments(vehicleId) {
    return fetchJson(`/api/vehicle-manager/vehicles/${vehicleId}/documents`, { method: 'GET' })
}