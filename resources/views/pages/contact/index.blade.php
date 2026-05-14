<x-layouts.app :seo="$seo">

    <section class="py-24 md:py-32">
        <div class="container-agency">

            <div class="max-w-2xl mx-auto text-center mb-16">
                <div class="section-label justify-center mb-4">
                    <span class="w-4 h-px bg-current inline-block"></span>
                    Contact
                    <span class="w-4 h-px bg-current inline-block"></span>
                </div>
                <h1 class="section-title mb-4 reveal">Let's talk</h1>
                <p class="section-subtitle mx-auto reveal">
                    Have a project in mind? Fill out the form below and we'll get back to you within 24 hours.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 max-w-5xl mx-auto">

                {{-- Contact Form --}}
                <div class="lg:col-span-2">
                    <div class="card p-8 reveal"
                         x-data="contactForm()">

                        {{-- Success state --}}
                        <div x-show="success" x-transition class="text-center py-12">
                            <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </div>
                            <h3 class="font-display font-semibold text-xl text-black dark:text-white mb-2">Message sent!</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">We'll get back to you within 24 hours.</p>
                        </div>

                        {{-- Form --}}
                        <form x-show="!success"
                              @submit.prevent="submit($event)"
                              action="{{ route('contact.store') }}"
                              method="POST"
                              class="space-y-5">
                            @csrf

                            {{-- Honeypot --}}
                            <input type="text" name="_website" class="sr-only" tabindex="-1" autocomplete="off">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-medium text-black dark:text-white mb-1.5">Name *</label>
                                    <input type="text" name="name" required
                                           class="input-field"
                                           placeholder="John Doe">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-black dark:text-white mb-1.5">Email *</label>
                                    <input type="email" name="email" required
                                           class="input-field"
                                           placeholder="john@example.com">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-medium text-black dark:text-white mb-1.5">Phone</label>
                                    <input type="tel" name="phone"
                                           class="input-field"
                                           placeholder="+62 812 3456 7890">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-black dark:text-white mb-1.5">Subject</label>
                                    <input type="text" name="subject"
                                           class="input-field"
                                           placeholder="New project inquiry">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-black dark:text-white mb-1.5">Message *</label>
                                <textarea name="message" required rows="5"
                                          class="input-field resize-none"
                                          placeholder="Tell us about your project, goals, and timeline..."></textarea>
                            </div>

                            {{-- Error --}}
                            <div x-show="error" class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                <p class="text-sm text-red-600 dark:text-red-400" x-text="error"></p>
                            </div>

                            <button type="submit"
                                    :disabled="submitting"
                                    class="btn-primary w-full justify-center py-3.5 disabled:opacity-60 disabled:scale-100">
                                <span x-show="!submitting">Send Message</span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="space-y-4">
                    @foreach([
                        ['icon' => 'mail',  'label' => 'Email',    'value' => config('agency.email'),    'href' => 'mailto:'.config('agency.email')],
                        ['icon' => 'phone', 'label' => 'Phone',    'value' => config('agency.phone'),    'href' => 'tel:'.str_replace(' ', '', config('agency.phone'))],
                        ['icon' => 'map',   'label' => 'Location', 'value' => config('agency.address'),  'href' => null],
                    ] as $info)
                    <div class="card p-6 reveal">
                        <div class="w-9 h-9 rounded-lg bg-black/5 dark:bg-white/5 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4 text-black dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                @if($info['icon'] === 'mail')
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                @elseif($info['icon'] === 'phone')
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.58 4.4 2 2 0 0 1 3.56 2.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.16 6.16l1.27-.85a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                @else
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                                @endif
                            </svg>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-600 mb-1">{{ $info['label'] }}</div>
                        @if($info['href'])
                            <a href="{{ $info['href'] }}" class="text-sm font-medium text-black dark:text-white hover:underline">{{ $info['value'] }}</a>
                        @else
                            <span class="text-sm font-medium text-black dark:text-white">{{ $info['value'] }}</span>
                        @endif
                    </div>
                    @endforeach

                    {{-- WhatsApp CTA --}}
                    <a href="https://wa.me/{{ config('agency.whatsapp') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="card p-6 reveal block hover:border-green-400/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 mb-0.5">Prefer chat?</div>
                                <div class="text-sm font-medium text-black dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                    WhatsApp us directly →
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </section>

</x-layouts.app>
