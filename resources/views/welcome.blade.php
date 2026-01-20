<x-guest-layout>
    <div class="cursor-default bg-white text-gray-900 overflow-x-hidden">

        {{-- ================= HERO ================= --}}
        <section class="relative isolate overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-indigo-50 min-h-[90vh] flex items-center">
            {{-- Decorative Elements --}}
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-20 left-10 w-72 h-72 bg-emerald-400/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8 relative z-10">
                <div class="grid items-center gap-16 lg:grid-cols-2">

                    {{-- Text Content --}}
                    <div data-aos="fade-up" class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-6">
                            <span class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse"></span>
                            <p class="text-xs font-semibold text-emerald-700 tracking-wide uppercase">
                                Cobain Gratis Sekarang
                            </p>
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight mb-6">
                            Kelola Restoran Anda,
                            <span class="block mt-2 bg-gradient-to-r from-emerald-600 via-emerald-500 to-indigo-600 bg-clip-text text-transparent">
                                Mudah & Efisien
                            </span>
                        </h1>

                        <p class="text-lg text-gray-600 leading-relaxed mb-8 max-w-xl">
                            Semua urusan restoran Anda — mulai dari inventaris hingga laporan keuangan — kini dalam satu aplikasi terpadu berbasis cloud.
                        </p>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-10">
                            <a href="/register"
                               class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-4 text-base font-semibold text-white shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:scale-105 transition-all duration-200">
                                Mulai Sekarang
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </a>

                            <a href="#features"
                               class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-gray-300 px-8 py-4 text-base font-semibold text-gray-700 hover:border-emerald-600 hover:text-emerald-600 transition-all duration-200">
                                Lihat Fitur
                            </a>
                        </div>

                        {{-- Stats/Rating --}}
                        <div class="flex flex-wrap items-center gap-8 pt-8 border-t border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="flex -space-x-2">
                                    @for($i=0; $i<5; $i++)
                                        <svg class="h-5 w-5 fill-amber-400 text-amber-400 drop-shadow-sm"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 20 20">
                                            <polygon points="10 1.5 12.85 7.26 19.14 8.27 14.57 12.97 15.71 19.23 10 16.09 4.29 19.23 5.43 12.97 0.86 8.27 7.15 7.26 10 1.5"/>
                                        </svg>
                                    @endfor
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">4.9/5</div>
                                    <div class="text-xs text-gray-500">1000+ Ulasan</div>
                                </div>
                            </div>

                            <div class="h-12 w-px bg-gray-300"></div>

                            <div>
                                <div class="font-bold text-2xl text-gray-900">500+</div>
                                <div class="text-xs text-gray-500">Restoran Terdaftar</div>
                            </div>

                            <div>
                                <div class="font-bold text-2xl text-gray-900">50K+</div>
                                <div class="text-xs text-gray-500">Transaksi/Bulan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Hero Images --}}
                    <div data-aos="fade-up" data-aos-delay="100" class="relative lg:h-[600px]">
                        <div class="relative h-full">
                            {{-- Main Image --}}
                            <div class="absolute top-0 right-0 w-[280px] sm:w-[340px] lg:w-[400px] h-[350px] lg:h-[420px] overflow-hidden rounded-3xl shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80"
                                     class="object-cover w-full h-full" alt="Restaurant Interior"/>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            </div>

                            {{-- Secondary Image --}}
                            <div class="absolute bottom-0 left-0 w-[240px] sm:w-[280px] lg:w-[320px] h-[300px] lg:h-[360px] overflow-hidden rounded-3xl shadow-2xl transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                                <img src="https://images.unsplash.com/photo-1556767576-5ec41e3239ff?auto=format&fit=crop&w=1200&q=80"
                                     class="object-cover w-full h-full" alt="Restaurant Food"/>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            </div>

                            {{-- Floating Stats Card --}}
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl p-6 backdrop-blur-sm bg-white/95 border border-gray-100 z-10">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">Real-time Sync</div>
                                        <div class="text-sm text-gray-500">Data selalu update</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FEATURES ================= --}}
        <section id="features" class="relative bg-white py-24">
            <div class="mx-auto max-w-7xl px-6">
                
                <div class="text-center mb-16" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 rounded-full mb-4">
                        <span class="text-xs font-semibold text-indigo-700 tracking-wide uppercase">
                            Fitur Unggulan
                        </span>
                    </div>
                    <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-4">
                        Semua yang Anda Butuhkan
                    </h2>
                    <p class="max-w-2xl mx-auto text-lg text-gray-600">
                        Fitur lengkap untuk mengelola restoran modern dengan efisien
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @php
                        $features = [
                            [
                                "icon" => "☁️",
                                "title" => "Platform Berbasis Cloud",
                                "desc" => "Akses dari mana saja dan kapan saja dengan keamanan data terjamin",
                                "color" => "from-blue-500 to-cyan-500"
                            ],
                            [
                                "icon" => "💬",
                                "title" => "Dukungan Pelanggan 24/7",
                                "desc" => "Tim kami siap membantu kapan pun Anda membutuhkan bantuan",
                                "color" => "from-purple-500 to-pink-500"
                            ],
                            [
                                "icon" => "📊",
                                "title" => "Laporan Analitik Mendalam",
                                "desc" => "Lihat laporan real-time untuk keputusan bisnis yang lebih baik",
                                "color" => "from-emerald-500 to-teal-500"
                            ],
                            [
                                "icon" => "🔗",
                                "title" => "Integrasi Mudah",
                                "desc" => "Hubungkan dengan aplikasi favorit Anda tanpa ribet",
                                "color" => "from-orange-500 to-red-500"
                            ],
                        ];
                    @endphp

                    @foreach($features as $f)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}"
                         class="group relative bg-white border-2 border-gray-100 rounded-2xl p-8 hover:border-transparent hover:shadow-2xl transition-all duration-300">
                        
                        {{-- Gradient Border on Hover --}}
                        <div class="absolute inset-0 bg-gradient-to-br {{ $f['color'] }} opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300 -z-10"></div>
                        <div class="absolute inset-[2px] bg-white rounded-2xl -z-10"></div>
                        
                        <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br {{ $f['color'] }} shadow-lg text-3xl transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                            {{ $f["icon"] }}
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-transparent group-hover:bg-gradient-to-br group-hover:{{ $f['color'] }} group-hover:bg-clip-text transition-all duration-300">
                            {{ $f["title"] }}
                        </h3>
                        
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $f["desc"] }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= ABOUT ================= --}}
        <section class="py-24 relative bg-gradient-to-b from-gray-50 to-white">
            <div class="mx-auto max-w-7xl px-6">

                <div class="text-center mb-16" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-full mb-4">
                        <span class="text-xs font-semibold text-emerald-700 tracking-wide uppercase">
                            Tentang Kami
                        </span>
                    </div>
                    <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-4">
                        Satu Aplikasi, Semua Solusi
                    </h2>
                    <p class="max-w-2xl mx-auto text-lg text-gray-600 leading-relaxed">
                        Kami membantu pemilik restoran mengelola operasional harian dengan lebih efisien — agar Anda dapat fokus pada cita rasa dan pelayanan terbaik.
                    </p>
                </div>

                {{-- Image Grid --}}
                <div class="grid sm:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <div data-aos="fade-right"
                         class="relative group overflow-hidden rounded-3xl shadow-2xl aspect-[3/4] transform hover:scale-[1.02] transition-transform duration-500">
                        <img src="https://images.unsplash.com/photo-1576618148400-f54bed99fc71?auto=format&fit=crop&w=1200&q=80"
                             class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700" 
                             alt="Restaurant Interior"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <h3 class="text-2xl font-bold mb-2">Manajemen Modern</h3>
                            <p class="text-sm text-white/90">Teknologi terkini untuk restoran Anda</p>
                        </div>
                    </div>

                    <div data-aos="fade-left"
                         class="relative group overflow-hidden rounded-3xl shadow-2xl aspect-[3/4] transform hover:scale-[1.02] transition-transform duration-500">
                        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=1200&q=80"
                             class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700" 
                             alt="Restaurant Team"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <h3 class="text-2xl font-bold mb-2">Tim Profesional</h3>
                            <p class="text-sm text-white/90">Dukungan penuh untuk kesuksesan Anda</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ================= CTA ================= --}}
        <section class="py-24 relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-indigo-700">
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-4xl px-6 text-center relative z-10" data-aos="fade-up">
                <h2 class="text-4xl sm:text-5xl font-black text-white mb-6">
                    Siap Mengubah Cara Anda Mengelola Restoran?
                </h2>
                <p class="text-xl text-emerald-50 mb-10 max-w-2xl mx-auto">
                    Bergabunglah dengan ratusan restoran yang telah merasakan kemudahan mengelola bisnis dengan platform kami.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/register"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-semibold text-emerald-700 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-200">
                        Mulai Gratis Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#features"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-white px-8 py-4 text-base font-semibold text-white hover:bg-white hover:text-emerald-700 transition-all duration-200">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
        </section>

    </div>

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
</x-guest-layout>