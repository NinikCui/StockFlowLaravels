<x-app-layout :branchCode="$branchCode">


<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 pb-10">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- HEADER --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-3xl shadow-2xl p-8 mb-8">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 h-40 w-40 rounded-full bg-white opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white opacity-10 blur-2xl"></div>
            
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border-2 border-white/30">
                        <svg class="h-9 w-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Point of Sale</h1>
                        <p class="text-emerald-100 text-sm sm:text-base mt-1 font-medium">{{ $branch->name }}</p>
                    </div>
                </div>

                <a href="{{ route('branch.pos.shift.index', $branchCode) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-semibold rounded-xl border-2 border-white/30 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Shift</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN: BUNDLES + PRODUCTS --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- BUNDLES SECTION --}}
                @if($bundles->count())
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                            <span class="text-xl">🎁</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Paket Promo</h2>
                            <p class="text-xs text-gray-500">Paket spesial dengan harga menarik</p>
                        </div>
                        <span class="ml-auto px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                            {{ $bundles->count() }} Paket
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($bundles as $b)
                        <form method="POST" action="{{ route('branch.pos.order.addBundle', $branchCode) }}">
                            @csrf
                            <input type="hidden" name="bundle_id" value="{{ $b->id }}">

                            <button type="submit" class="group w-full rounded-xl p-4 border-2 border-purple-200 bg-gradient-to-br from-purple-50 to-pink-50 hover:border-purple-400 hover:shadow-lg transition-all duration-200 hover:scale-105 active:scale-100">
                                
                                <div class="h-12 w-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-purple-100 to-pink-100 group-hover:from-purple-200 group-hover:to-pink-200 flex items-center justify-center text-2xl transition-colors">
                                    🎁
                                </div>

                                <div class="font-bold text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem] text-sm">
                                    {{ $b->name }}
                                </div>
{{-- ISI BUNDLE --}}
@if($b->items->count())
    <div class="mb-2 space-y-1 text-xs text-gray-600">
        @foreach($b->items as $bi)
            <div class="flex items-center gap-1">
                <span class="text-gray-400">•</span>
                <span class="truncate">
                    {{ $bi->product->name }}
                </span>
                <span class="text-gray-400">
                    ({{ $bi->qty }}x)
                </span>
            </div>
        @endforeach
    </div>
@endif
                                <div class="text-lg font-bold text-purple-600 mb-2">
                                    Rp {{ number_format($b->bundle_price, 0, ',', '.') }}
                                </div>

                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 group-hover:from-purple-600 group-hover:to-pink-600 text-white text-xs px-3 py-1.5 rounded-full font-semibold flex items-center justify-center gap-1 transition-all">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Tambah Paket</span>
                                </div>
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- PRODUCTS SECTION --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Menu Produk</h2>
                                <p class="text-xs text-gray-500">Pilih produk untuk ditambahkan ke keranjang</p>
                            </div>
                        </div>
                        <span class="px-4 py-2 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-full shadow-sm">
                            {{ count($products) }} Items
                        </span>
                    </div>

                    {{-- Product Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($products as $p)
                            @php
                               $disabled = ! $p->is_available;
                               $isRecommended = $p->is_recommended ?? false;
                            @endphp

                            <form 
                                action="{{ $disabled ? '#' : route('branch.pos.order.add', $branchCode) }}" 
                                method="POST"
                                class="{{ $disabled ? 'pointer-events-none' : '' }} relative"
                            >
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $p->id }}">

                                {{-- RECOMMENDED BADGE --}}
                                @if($isRecommended && ! $disabled)
                                <div class="absolute -top-2 -right-2 z-10">
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg border-2 border-white animate-pulse">
                                        ⭐ Hot
                                    </span>
                                </div>
                                @endif

                                <button type="{{ $disabled ? 'button' : 'submit' }}"
                                    class="group w-full rounded-2xl transition-all duration-200 p-5 text-left shadow-md border-2
                                        {{ $disabled 
                                            ? 'bg-gray-50 border-gray-200 opacity-60 cursor-not-allowed' 
                                            : ($isRecommended 
                                                ? 'bg-gradient-to-br from-amber-50 to-orange-50 border-amber-200 hover:border-orange-400 hover:shadow-xl hover:scale-105 active:scale-100'
                                                : 'bg-white border-gray-200 hover:border-emerald-400 hover:shadow-xl hover:scale-105 active:scale-100')
                                        }}"
                                >
                                    {{-- Product Icon --}}
                                    <div class="mb-4 h-16 w-16 mx-auto rounded-2xl flex items-center justify-center text-4xl
                                        {{ $disabled 
                                            ? 'bg-gray-100' 
                                            : ($isRecommended 
                                                ? 'bg-gradient-to-br from-amber-100 to-orange-100 group-hover:from-amber-200 group-hover:to-orange-200'
                                                : 'bg-gradient-to-br from-emerald-100 to-teal-100 group-hover:from-emerald-200 group-hover:to-teal-200')
                                        }}
                                        transition-all duration-200 shadow-sm group-hover:shadow-md">
                                        🍽️
                                    </div>

                                    {{-- Product Name --}}
                                    <div class="text-sm font-bold text-gray-900 mb-3 line-clamp-2 text-center min-h-[2.5rem]">
                                        {{ $p->name }}
                                    </div>

                                    {{-- Price --}}
                                    <div class="text-center mb-3">
                                        <span class="text-lg font-bold {{ $isRecommended ? 'text-orange-600' : 'text-emerald-600' }}">
                                            Rp {{ number_format($p->base_price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- Action Button --}}
                                    @if($disabled)
                                        <div class="bg-red-100 text-red-700 text-xs px-3 py-2 rounded-lg font-semibold text-center flex items-center justify-center gap-1.5 border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Stok Habis</span>
                                        </div>
                                    @else
                                        <div class="bg-gradient-to-r {{ $isRecommended ? 'from-orange-500 to-amber-500 group-hover:from-orange-600 group-hover:to-amber-600' : 'from-emerald-500 to-teal-500 group-hover:from-emerald-600 group-hover:to-teal-600' }} text-white text-xs px-3 py-2 rounded-lg font-semibold text-center flex items-center justify-center gap-1.5 transition-all duration-200 shadow-sm group-hover:shadow-md">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Tambah</span>
                                        </div>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- RIGHT COLUMN: CART --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 sticky top-6 overflow-hidden">
                    
                    {{-- Cart Header --}}
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-6">
                        <div class="flex items-center gap-3 text-white">
                            <div class="h-14 w-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Keranjang Belanja</h2>
                                <p class="text-emerald-100 text-sm mt-0.5">
                                    {{ empty($cart) ? 'Belum ada item' : count($cart) . ' Item dipilih' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- EMPTY CART --}}
                        @if(empty($cart))
                            <div class="text-center py-16">
                                <div class="w-28 h-28 mx-auto mb-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                                    <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 text-base font-semibold mb-1">Keranjang Kosong</p>
                                <p class="text-gray-400 text-sm">Tambahkan produk dari menu di sebelah kiri</p>
                            </div>
                        @else
                            {{-- CART LIST --}}
                            <div class="space-y-4 max-h-[420px] overflow-y-auto custom-scrollbar pr-2">
                               @foreach($cart as $cartKey => $item)


                                <div class="group bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border-2 border-gray-200 hover:border-emerald-300 transition-all duration-200 hover:shadow-md">
                                    
                                    {{-- Item Header --}}
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1 pr-3">
                                            <p class="font-bold text-gray-900 mb-1.5 leading-snug text-sm">{{ $item['name'] }}</p>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg font-bold shadow-sm">
                                                    {{ $item['qty'] }}x
                                                </span>
                                                <span class="font-medium">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <p class="font-bold text-base text-emerald-600 whitespace-nowrap">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Note Input --}}
                                    <div class="mb-3">
                                        <textarea
                                            class="w-full text-xs border-2 border-gray-200 rounded-lg p-3 focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all duration-200 resize-none placeholder-gray-400"
                                            placeholder="💬 Catatan khusus (opsional)..."
                                            rows="2"
                                            oninput="updateNote('{{ $cartKey }}', this.value)"
                                        >{{ $item['note'] ?? '' }}</textarea>
                                    </div>

                                    {{-- Remove Button --}}
                                    <form method="POST" action="{{ route('branch.pos.order.remove', $branchCode) }}">
    @csrf
    <input type="hidden" name="cart_key" value="{{ $cartKey }}">
                                        <button type="submit" class="flex items-center gap-1.5 text-red-600 text-xs font-bold hover:text-red-700 hover:bg-red-50 transition-all px-2.5 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Hapus Item</span>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>

                            {{-- TOTAL SECTION --}}
                            <div class="mt-6 pt-6 border-t-2 border-gray-200">
                                <div class="space-y-2.5 mb-5">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span class="font-medium">Subtotal</span>
                                        <span class="font-bold">Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center p-5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl border-2 border-emerald-300 shadow-sm">
                                    <span class="text-lg font-bold text-gray-900">Total Bayar</span>
                                    <span class="text-2xl font-bold text-emerald-600">
                                        Rp {{ number_format(collect($cart)->sum('subtotal'), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- PAY BUTTON --}}
                            <button type="button"
                                onclick="openPaymentModal()"
                                class="group w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-200 mt-6 flex items-center justify-center gap-2.5 active:scale-95">
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="text-lg">Proses Pembayaran</span>
                            </button>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

@include("branch.pos.order.modal")
<style>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Modal Animation */
#paymentModal.show #paymentModalContent,
#cashModal.show #cashModalContent {
    transform: scale(1);
    opacity: 1;
}

/* Line Clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>


</x-app-layout>