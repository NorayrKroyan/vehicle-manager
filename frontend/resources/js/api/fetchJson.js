// frontend/resources/js/api/fetchJson.js

function buildUrl(url, params) {
    if (!params || typeof params !== 'object') return url

    const usp = new URLSearchParams()
    Object.entries(params).forEach(([k, v]) => {
        if (v === undefined || v === null) return
        usp.append(k, String(v))
    })

    const qs = usp.toString()
    if (!qs) return url
    return url.includes('?') ? `${url}&${qs}` : `${url}?${qs}`
}

export async function fetchJson(url, opts = {}) {
    const method = String(opts.method || 'GET').toUpperCase()
    const finalUrl = buildUrl(url, opts.params)

    const headers = {
        Accept: 'application/json',
        ...(opts.headers || {}),
    }

    const init = {
        method,
        headers,
        credentials: 'same-origin',
    }

    // Support JSON payload
    if (opts.data !== undefined) {
        // allow FormData passthrough if you ever need it
        if (typeof FormData !== 'undefined' && opts.data instanceof FormData) {
            init.body = opts.data
        } else {
            headers['Content-Type'] = 'application/json'
            init.body = JSON.stringify(opts.data)
        }
    }

    const res = await fetch(finalUrl, init)

    // Parse response safely
    const contentType = res.headers.get('content-type') || ''
    let data = null

    if (contentType.includes('application/json')) {
        try {
            data = await res.json()
        } catch {
            data = null
        }
    } else {
        const text = await res.text().catch(() => '')
        data = text || null
    }

    if (!res.ok) {
        // ✅ IMPORTANT: keep Laravel validation errors
        const err = new Error(
            (data && typeof data === 'object' && data.message) ? data.message : `HTTP ${res.status}`
        )
        err.status = res.status
        err.data = data
        err.errors = (data && typeof data === 'object' && data.errors) ? data.errors : null
        throw err
    }

    return data
}