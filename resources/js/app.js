/**
 * Creative Agency — app.js
 * HTMX + AlpineJS + Vanilla utilities
 * No heavy frameworks, no bloat.
 */

import Alpine from 'alpinejs'
import htmx from 'htmx.org'

// ─── AlpineJS Global Setup ───────────────────────────────────────────────────

window.Alpine = Alpine

// Dark mode store
Alpine.store('theme', {
    dark: localStorage.getItem('theme') === 'dark', // default: light mode

    toggle() {
        this.dark = !this.dark
        localStorage.setItem('theme', this.dark ? 'dark' : 'light')
        document.documentElement.classList.toggle('dark', this.dark)
    },

    init() {
        document.documentElement.classList.toggle('dark', this.dark)
    }
})

// Navbar store
Alpine.store('navbar', {
    open:      false,
    scrolled:  false,
    lastScroll: 0,
    hidden:    false,

    init() {
        window.addEventListener('scroll', () => {
            const current = window.scrollY
            this.scrolled = current > 20
            this.hidden   = current > this.lastScroll && current > 80
            this.lastScroll = current
        }, { passive: true })
    }
})

// Toast notification store
Alpine.store('toast', {
    items: [],

    show(message, type = 'success', duration = 4000) {
        const id = Date.now()
        this.items.push({ id, message, type })
        setTimeout(() => this.remove(id), duration)
    },

    remove(id) {
        this.items = this.items.filter(i => i.id !== id)
    }
})

// ─── Alpine Components ────────────────────────────────────────────────────────

Alpine.data('faq', () => ({
    open: null,
    toggle(idx) {
        this.open = this.open === idx ? null : idx
    }
}))

Alpine.data('portfolioFilter', () => ({
    active: 'all',
    setFilter(cat) {
        this.active = cat
    }
}))

Alpine.data('contactForm', () => ({
    submitting: false,
    success:    false,
    error:      null,

    async submit(e) {
        this.submitting = true
        this.error = null

        const form = e.target
        const data = new FormData(form)

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            const json = await res.json()

            if (json.success) {
                this.success = true
                form.reset()
            } else {
                this.error = json.message || 'Something went wrong.'
            }
        } catch {
            this.error = 'Network error. Please try again.'
        } finally {
            this.submitting = false
        }
    }
}))

Alpine.data('imagePreview', () => ({
    current: 0,
    images:  [],

    init() {
        this.images = JSON.parse(this.$el.dataset.images || '[]')
    },

    prev() {
        this.current = (this.current - 1 + this.images.length) % this.images.length
    },

    next() {
        this.current = (this.current + 1) % this.images.length
    }
}))

Alpine.data('copyButton', () => ({
    copied: false,

    async copy(text) {
        await navigator.clipboard.writeText(text)
        this.copied = true
        setTimeout(() => { this.copied = false }, 2000)
    }
}))

Alpine.start()

// ─── HTMX Configuration ───────────────────────────────────────────────────────

window.htmx = htmx

htmx.config.defaultSwapStyle     = 'outerHTML'
htmx.config.defaultSwapDelay     = 0
htmx.config.defaultSettleDelay   = 100
htmx.config.historyCacheSize     = 10
htmx.config.includeIndicatorStyles = false // We handle our own

// Global HTMX events
document.addEventListener('htmx:beforeRequest', () => {
    // Reset scroll position for page changes
})

document.addEventListener('htmx:afterSwap', (e) => {
    // Re-init reveal animations after HTMX swaps content
    initRevealObserver()
    // Re-apply syntax highlighting if any
    if (window.Prism) window.Prism.highlightAll()
})

document.addEventListener('htmx:responseError', (e) => {
    Alpine.store('toast').show('Something went wrong. Please try again.', 'error')
})

// ─── Intersection Observer — Reveal Animation ─────────────────────────────────

function initRevealObserver() {
    const elements = document.querySelectorAll('.reveal:not(.visible)')
    if (!elements.length) return

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible')
                    observer.unobserve(entry.target)
                }, i * 80)
            }
        })
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' })

    elements.forEach(el => observer.observe(el))
}

document.addEventListener('DOMContentLoaded', initRevealObserver)

// ─── Smooth Scroll for anchor links ──────────────────────────────────────────

document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href^="#"]')
    if (!link) return
    const target = document.querySelector(link.getAttribute('href'))
    if (!target) return
    e.preventDefault()
    target.scrollIntoView({ behavior: 'smooth', block: 'start' })
})

// ─── Reading Progress Bar ─────────────────────────────────────────────────────

const progressBar = document.getElementById('reading-progress')
if (progressBar) {
    window.addEventListener('scroll', () => {
        const scrollTop    = window.scrollY
        const docHeight    = document.documentElement.scrollHeight - window.innerHeight
        const progress     = Math.round((scrollTop / docHeight) * 100)
        progressBar.style.width = `${progress}%`
    }, { passive: true })
}

// ─── Lazy Load Images ─────────────────────────────────────────────────────────

if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target
                if (img.dataset.src) {
                    img.src = img.dataset.src
                    img.removeAttribute('data-src')
                }
                imageObserver.unobserve(img)
            }
        })
    }, { rootMargin: '200px 0px' })

    document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img))
}

// ─── CSRF Token Helper ────────────────────────────────────────────────────────

window.csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? ''

// ─── Utility: Debounce ────────────────────────────────────────────────────────

window.debounce = (fn, delay = 300) => {
    let timer
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout(() => fn(...args), delay)
    }
}
