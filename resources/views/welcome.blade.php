<x-guest-layout>
    <div class="cursor-default bg-gradient-to-b from-white via-gray-50 to-gray-100 text-gray-900">

        {{-- ================= HERO ================= --}}
        <section class="relative isolate overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 via-transparent to-indigo-50 opacity-70"></div>

            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8 relative">
                <div class="grid items-center gap-12 md:grid-cols-2">

                    {{-- Text --}}
                    <div data-aos="fade-up" class="max-w-xl">
                        <p class="text-xs tracking-[0.25em] uppercase text-emerald-600 mb-4 font-semibold">
                            Cobain Gratis Sekarang
                        </p>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight">
                            Kelola Restoran Anda, <br>
                            <span class="bg-gradient-to-r from-emerald-600 to-indigo-600 bg-clip-text text-transparent">
                                Mudah dan Efisien
                            </span>
                        </h1>

                        <p class="mt-5 text-base text-gray-600 max-w-prose">
                            Semua urusan restoran Anda — mulai dari inventaris hingga laporan keuangan —
                            kini dalam satu aplikasi terpadu berbasis cloud.
                        </p>

                        <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-4">
                            <a href="/register"
                            class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700 transition">
                                Mulai Sekarang
                            </a>

                            {{-- Rating --}}
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <div class="flex">
                                    @for($i=0; $i<5; $i++)
                                        <svg class="h-4 w-4 fill-yellow-400 text-yellow-400"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <polygon points="10 1.5 12.85 7.26 19.14 8.27 14.57 12.97 15.71 19.23 10 16.09 4.29 19.23 5.43 12.97 0.86 8.27 7.15 7.26 10 1.5"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="font-medium text-gray-800">4.9</span>
                                <span>/ 5 (1k+ ulasan)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Images --}}
                    <div data-aos="fade-up" class="relative flex justify-center">
                        <div class="relative grid grid-cols-2 gap-5 w-[320px] sm:w-[400px] md:w-[460px]">
                            <div class="overflow-hidden rounded-2xl bg-emerald-500/20 float-up-down">
                                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=60"
                                    class="object-cover w-full h-full" />
                            </div>
                            <div class="overflow-hidden rounded-2xl bg-indigo-500/20 float-down-up">
                                <img src="https://images.unsplash.com/photo-1556767576-5ec41e3239ff?auto=format&fit=crop&w=1200&q=60"
                                    class="object-cover w-full h-full" />
                            </div>
                        </div>

                        {{-- Glow --}}
                        <div class="absolute -inset-16 bg-gradient-to-t from-emerald-200 to-transparent blur-3xl opacity-40"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= ABOUT ================= --}}
        <section class="py-24 relative">
            <div class="mx-auto max-w-6xl px-6 text-center">

                <p data-aos="fade-up"
                class="text-xs tracking-[0.25em] uppercase text-emerald-600 font-semibold mb-3">
                Tentang Kami
                </p>

                <h2 data-aos="fade-up"
                    class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Satu Aplikasi, Semua Solusi Restoran
                </h2>

                <p data-aos="fade-up"
                class="max-w-2xl mx-auto text-gray-600 text-sm sm:text-base leading-relaxed">
                    Kami membantu pemilik restoran mengelola operasional harian
                    dengan lebih efisien — agar Anda dapat fokus pada cita rasa.
                </p>

                {{-- Image grid --}}
                <div class="mt-12 grid sm:grid-cols-2 gap-6 justify-center">
                    <div data-aos="fade-up"
                        class="relative aspect-[4/5] overflow-hidden rounded-2xl shadow-lg">
                        <img src="https://images.unsplash.com/photo-1576618148400-f54bed99fc71?auto=format&fit=crop&w=1200&q=60"
                            class="object-cover w-full h-full" />
                    </div>

                    <div data-aos="fade-up"
                        class="relative aspect-[4/5] overflow-hidden rounded-2xl rounded-bl-[140px] shadow-lg">
                        <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=1200&q=60"
                            class="object-cover w-full h-full" />
                    </div>
                </div>

            </div>
        </section>

        {{-- ================= FEATURES ================= --}}
        <section class="relative bg-white py-24">
            <div class="mx-auto max-w-6xl px-6 text-center">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

                    @php
                        $features = [
                            ["icon" => "☁️", "title" => "Platform Berbasis Cloud", "desc" => "Akses dari mana saja dan kapan saja..."],
                            ["icon" => "💬", "title" => "Dukungan Pelanggan 24/7", "desc" => "Tim kami siap membantu kapan pun..."],
                            ["icon" => "📊", "title" => "Laporan Analitik Mendalam", "desc" => "Lihat laporan real-time..."],
                            ["icon" => "🔗", "title" => "Integrasi Mudah", "desc" => "Hubungkan aplikasi Anda..."],
                        ];
                    @endphp

                    @foreach($features as $f)
                    <div data-aos="fade-up"
                        class="group bg-gradient-to-b from-gray-50 to-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-indigo-100 shadow-inner text-4xl">
                            {{ $f["icon"] }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $f["title"] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $f["desc"] }}</p>
                    </div>
                    @endforeach

                </div>
            </div>
        </section>

    </div>
</x-guest-layout>
