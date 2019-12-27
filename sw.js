const CACHE_NAME = 'App'
const ERROR_URL = '/404'
const RESTRICTED_URLS = ['/verify', '/chat', '/update-profile','/get_assets']

self.addEventListener('install', (event) => {
    // console.log('[SW] Installed', event)
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            cache.addAll([
                '/setup',
                '/signout',
                '/404',
            ])
            fetch('/get_assets').then((res) => {
                res.json().then((data) => {
                    for (req of data) {
                        cache.add(req.trim())
                    }
                    // console.log('[SW] App Shell created!')
                })
            })
        })
    )
})

self.addEventListener('activate', (event) => {
    // console.log('[SW] Activated', event)
})

self.addEventListener('fetch', (event) => {
    // console.log('[SW] Fetch', event.request.url)

    event.respondWith(
        // CACHE FIRST THEN NETWORK STRATEGY
        caches.open(CACHE_NAME).then((cache) => {
            if (RESTRICTED_URLS.some(function(url) { return pathsMatch(event.request.url, url) })) {
                return fetch(event.request).catch(() => {
                    return cache.match(event.request).then((fallback_response) => {
                        if (!fallback_response) {
                            return cache.match(ERROR_URL).then((error_response) => {
                                return error_response
                            })
                        }
                        return fallback_response
                    })
                })
            }
            return cache.match(event.request).then((response) => {
                if (!response) {
                    return fetch(event.request).then((cache_response) => {
                        cache.add(event.request)
                        return cache_response
                    }).catch(() => {
                        return cache.match(ERROR_URL).then((fallback_response) => {
                            return fallback_response
                        })
                    })
                }
                return response
            })
        })
    )
})

const pathsMatch = (href, path) => {
    r = /[-a-zA-Z0-9@:%._\+~#=\/]+/g
    pathname = href.match(r)[0]
    pathname = pathname.endsWith('/') ? pathname.substr(0, pathname.length - 1) : pathname
    return pathname.endsWith(path);
}