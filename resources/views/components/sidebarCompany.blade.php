@php
    // ========== SESSION LOGIN ========== //
    $username    = session('user.username', 'User');
    $companyCode = strtolower(session('role.company.code'));
    $branchCode  = session('role.branch.code');
    $roleCode    = session('role.code', 'USER');

    // Tentukan tenant prefix
    $prefix = strtolower($branchCode ?? $companyCode);

    // ========== MENU ITEMS ========== //
    $items = [
        [
            'label' => 'Dashboard',
            'href'  => "/$prefix/dashboard",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
        ],
        [
            'label' => 'Cabang Restoran',
            'href'  => "/$prefix/cabang",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
            'children' => [
                ['label' => 'Daftar Cabang', 'href' => "/$prefix/cabang"],
                ['label' => 'Gudang', 'href' => "/$prefix/gudang"],
            ]
        ],
        [
            'label' => 'Pegawai',
            'href'  => "/$prefix/pegawai",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'children' => [
                ['label' => 'Daftar Pegawai', 'href' => "/$prefix/pegawai"],
                ['label' => 'Role & Akses', 'href' => "/$prefix/pegawai/roles"],
            ]
        ],
        [
            'label' => 'Produk',
            'href'  => "/$prefix/product",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
            'children' => [ 
                ['label' => 'Barang Baku', 'href' => "/$prefix/items"],
            ]
        ],
        [
            'label' => 'Pembelian',
            'href'  => "/$prefix/pembelian",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'children' => [ 
                ['label' => 'Purchase Order', 'href' => "/$prefix/purchase-order"],
                ['label' => 'Supplier', 'href' => "/$prefix/supplier"],
            ]
        ],
        [
            'label' => 'Stok & Mutasi',
            'href'  => "/$prefix/stock",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h18v4H3v-4z" />
                    </svg>',
            'children' => [
                ['label' => 'Request Cabang',    'href' => "/$prefix/request-cabang"],  
            ]
        ],
        [
            'label' => 'Pengaturan',
            'href'  => "/$companyCode/settings/general",
            'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'children' => [ 
                ['label' => 'Umum', 'href' => "/$prefix/settings/general"],
                ['label' => 'Masalah', 'href' => "/$prefix/settings/masalah"],
            ]
        ]
    ];

    // ===== ACTIVE CHECK FUNCTIONS =====
    function isActiveExact($href) {
        return trim(request()->path(), '/') === trim($href, '/');
    }

    function isActiveStartWith($href) {
        return str_starts_with(trim(request()->path(), '/'), trim($href, '/'));
    }

    function isParentOpen($item) {
        if (!isset($item['children'])) return false;
        foreach ($item['children'] as $child) {
            if (isActiveExact($child['href']) || isActiveStartWith($child['href'])) {
                return true;
            }
        }
        return false;
    }
@endphp

{{-- MOBILE TOGGLE --}}
<button
    onclick="toggleSidebar()"
    class="fixed left-4 top-4 z-50 h-10 w-10 rounded-lg bg-white border shadow-sm hover:bg-gray-50 md:hidden"
>
    <svg class="h-5 w-5 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- BACKDROP --}}
<div id="sidebar-backdrop"
     onclick="toggleSidebar()"
     class="fixed inset-0 z-40 bg-black/50 hidden md:hidden"></div>

{{-- SIDEBAR --}}
<aside id="sidebar"
    class="fixed left-0 top-0 z-40 h-full w-64 flex flex-col bg-white border-r
           transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b">
        <div class="h-10 w-10 rounded-xl bg-emerald-600 grid place-items-center text-white font-bold">
            R
        </div>
        <div class="flex-1">
            <div class="font-bold text-gray-900">RestoApp</div>
            <div class="text-xs text-gray-500">{{ strtoupper($companyCode) }}</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isActiveItem = isActiveExact($item['href']) || isActiveStartWith($item['href']);
                $isOpen = $isActiveItem || isParentOpen($item);
            @endphp

            {{-- MENU TANPA CHILDREN --}}
            @if (!$hasChildren)
                <a href="{{ $item['href'] }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                    {{ $isActiveItem 
                        ? 'bg-emerald-600 text-white' 
                        : 'text-gray-700 hover:bg-gray-100' }}">
                    {!! $item['icon'] ?? '' !!}
                    <span>{{ $item['label'] }}</span>
                </a>

            @else
            {{-- MENU DENGAN CHILDREN --}}
                <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
                    
                    <button @click="open = !open"
                            class="flex w-full items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                            {{ $isOpen 
                                ? 'bg-emerald-600 text-white' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                        
                        <div class="flex items-center gap-3">
                            {!! $item['icon'] ?? '' !!}
                            <span>{{ $item['label'] }}</span>
                        </div>
                        
                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform">
                            <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    {{-- SUBMENU --}}
                    <div x-show="open" x-collapse class="ml-8 mt-1 space-y-0.5 border-l-2 pl-3"
                         :class="open ? 'border-emerald-300' : 'border-gray-200'">

                        @foreach ($item['children'] as $child)
                            @php
                                if ($child['href'] === "/$prefix/pegawai") {
                                    $childActive = isActiveExact($child['href']);
                                } else {
                                    $childActive = isActiveStartWith($child['href']);
                                }
                            @endphp

                            <a href="{{ $child['href'] }}"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                                {{ $childActive 
                                    ? 'bg-emerald-50 text-emerald-700 font-medium' 
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                
                                <span class="h-1 w-1 rounded-full {{ $childActive ? 'bg-emerald-600' : 'bg-gray-400' }}"></span>
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach

                    </div>
                </div>
            @endif

        @endforeach

    </nav>

    {{-- FOOTER --}}
    <div class="border-t px-4 py-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-9 w-9 rounded-full bg-gray-200 grid place-items-center text-gray-700 font-semibold">
                {{ strtoupper(substr($username, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $username }}</div>
                <div class="text-xs text-gray-500">{{ $roleCode }}</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg border text-sm text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- JAVASCRIPT --}}
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        sidebar.classList.toggle('-translate-x-full', !isHidden);
        backdrop.classList.toggle('hidden', !isHidden);
        
        if (window.innerWidth < 768) {
            document.body.style.overflow = isHidden ? 'hidden' : '';
        }
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            document.body.style.overflow = '';
            document.getElementById('sidebar-backdrop').classList.add('hidden');
        }
    });
</script>