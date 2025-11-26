@php
    // ========== SESSION LOGIN ========== //
    $username    = session('user.username', 'User');
    $companyCode = strtolower(session('role.company.code'));
    $branchCode  = session('role.branch.code');
    $roleCode    = session('role.code', 'USER');

    // Tentukan prefix tenant (branch > company)
    $prefix = strtolower($branchCode ?: $companyCode);

    function active($href) {
        return trim(request()->path(), '/') === trim($href, '/')
            || str_starts_with(trim(request()->path(), '/'), trim($href, '/'));
    }

    function childActive($children) {
        foreach ($children as $c) {
            if (active($c['href'])) return true;
        }
        return false;
    }

    // ========== SIDEBAR ITEMS ========== //
    $items = [
        [
            'label' => 'Dashboard',
            'href'  => "/$prefix/dashboard",
            'icon'  => 'home'
        ],
        [
            'label' => 'Cabang Restoran',
            'icon'  => 'store',
            'children' => [
                ['label' => 'Daftar Cabang', 'href' => "/$prefix/cabang"],
                ['label' => 'Gudang',        'href' => "/$prefix/gudang"],
            ]
        ],
        [
            'label' => 'Pegawai',
            'icon'  => 'users',
            'children' => [
                ['label' => 'Daftar Pegawai', 'href' => "/$prefix/pegawai"],
                ['label' => 'Roles',          'href' => "/$prefix/pegawai/roles"],
            ]
        ],
        [
            'label' => 'Produk',
            'icon'  => 'box',
            'children' => [
                ['label' => 'Barang Baku', 'href' => "/$prefix/items"],
            ]
        ],
        [
            'label' => 'Pembelian',
            'icon'  => 'cart',
            'children' => [
                ['label' => 'Purchase Order', 'href' => "/$prefix/purchase-order"],
                ['label' => 'Supplier',       'href' => "/$prefix/supplier"],
            ]
        ],
        [
            'label' => 'Stok & Mutasi',
            'icon'  => 'layers',
            'children' => [
                ['label' => 'Request Cabang', 'href' => "/$prefix/request-cabang"],
                ['label' => 'Analytics',      'href' => "/$prefix/request-cabang/analytics/cabang"],
            ]
        ],
        [
            'label' => 'Pengaturan',
            'icon'  => 'settings',
            'children' => [
                ['label' => 'Umum',    'href' => "/$prefix/settings/general"],
                ['label' => 'Masalah', 'href' => "/$prefix/settings/masalah"],
            ]
        ],
    ];
@endphp

{{-- MOBILE TOGGLE --}}
<button onclick="toggleSidebar()"
        class="fixed left-4 top-4 z-50 md:hidden h-10 w-10 rounded-lg bg-white border shadow-sm">
    <svg class="h-5 w-5 mx-auto text-gray-700" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- BACKDROP --}}
<div id="sidebar-backdrop" onclick="toggleSidebar()"
     class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

{{-- SIDEBAR --}}
<aside id="sidebar"
    class="fixed left-0 top-0 z-40 h-full w-64 bg-white border-r flex flex-col
           transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="px-5 py-5 border-b flex items-center gap-3">
        <div class="h-10 w-10 bg-emerald-600 text-white rounded-xl grid place-items-center font-bold">R</div>
        <div>
            <div class="font-bold text-gray-900">{{ strtoupper($companyCode) }}</div>
                        <div class="text-xs text-gray-500">{{ strtoupper($roleCode) }}</div>

        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isOpen = $hasChildren ? childActive($item['children']) : active($item['href'] ?? '');
            @endphp

            {{-- WITHOUT CHILDREN --}}
            @if (!$hasChildren)
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                   {{ active($item['href']) ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    @include('components.sidebar-icon', ['name' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>

            @else
            {{-- WITH CHILDREN --}}
                <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="flex items-center justify-between w-full
                               px-3 py-2.5 rounded-lg text-sm font-medium
                               {{ $isOpen ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        
                        <div class="flex items-center gap-3">
                            @include('components.sidebar-icon', ['name' => $item['icon']])
                            <span>{{ $item['label'] }}</span>
                        </div>

                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform">
                            <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    {{-- CHILD MENU --}}
                    <div x-show="open" x-collapse class="ml-8 mt-1 pl-3 border-l space-y-1 border-emerald-300">

                        @foreach ($item['children'] as $child)
                            <a href="{{ $child['href'] }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                               {{ active($child['href'])
                                    ? 'bg-emerald-50 text-emerald-700 font-medium'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="h-1 w-1 rounded-full
                                    {{ active($child['href']) ? 'bg-emerald-600' : 'bg-gray-400' }}"></span>
                                {{ $child['label'] }}
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
            <div class="h-9 w-9 rounded-full bg-gray-200 grid place-items-center font-semibold">
                {{ strtoupper(substr($username, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold truncate">{{ $username }}</div>
                <div class="text-xs text-gray-500">{{ $roleCode }}</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full text-sm px-3 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleSidebar() {
        const side = document.getElementById('sidebar');
        const back = document.getElementById('sidebar-backdrop');
        const hidden = side.classList.contains('-translate-x-full');

        side.classList.toggle('-translate-x-full', !hidden);
        back.classList.toggle('hidden', !hidden);

        document.body.style.overflow = hidden ? 'hidden' : '';
    }
</script>
