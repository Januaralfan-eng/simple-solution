import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Sora', ...defaultTheme.fontFamily.sans],
                display: ['Clash Display', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                'agency': {
                    50:  '#f7f7f7',
                    100: '#efefef',
                    200: '#d9d9d9',
                    300: '#b8b8b8',
                    400: '#888888',
                    500: '#666666',
                    600: '#444444',
                    700: '#2d2d2d',
                    800: '#1a1a1a',
                    900: '#111111',
                    950: '#0a0a0a',
                },
            },
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
                '88': '22rem',
                '104': '26rem',
                '120': '30rem',
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            animation: {
                'fade-in':       'fadeIn 0.7s ease-out forwards',
                'fade-up':       'fadeUp 0.7s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.6s ease-out forwards',
                'marquee':       'marquee 40s linear infinite',
                'marquee-slow':  'marquee 80s linear infinite',
                'pulse-slow':    'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'glow':          'glow 3s ease-in-out infinite alternate',
            },
            keyframes: {
                fadeIn: {
                    '0%':   { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeUp: {
                    '0%':   { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInLeft: {
                    '0%':   { opacity: '0', transform: 'translateX(-24px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                marquee: {
                    '0%':   { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                glow: {
                    '0%':   { boxShadow: '0 0 20px rgba(255,255,255,0.05)' },
                    '100%': { boxShadow: '0 0 60px rgba(255,255,255,0.15)' },
                },
            },
            backgroundImage: {
                'grid-pattern':  "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
                'noise':         "url(\"data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E\")",
            },
            backdropBlur: {
                'xs': '2px',
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                        color: theme('colors.agency.700'),
                        a: { color: theme('colors.agency.900'), textDecoration: 'none' },
                        strong: { color: theme('colors.agency.900') },
                        h1: { fontFamily: theme('fontFamily.display').join(', ') },
                        h2: { fontFamily: theme('fontFamily.display').join(', ') },
                        h3: { fontFamily: theme('fontFamily.display').join(', ') },
                    },
                },
                invert: {
                    css: {
                        color: theme('colors.agency.300'),
                        a: { color: theme('colors.agency.100') },
                        strong: { color: theme('colors.white') },
                    },
                },
            }),
        },
    },
    plugins: [forms, typography],
}
