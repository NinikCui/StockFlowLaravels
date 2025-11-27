@php
    use Illuminate\Support\Facades\Auth;

    // SESSION VALUES
    $user        = Auth::user();
    $username    = session('user.username');
    $roleCode    = session('role.code');
    $companyCode = strtolower(session('role.company.code'));
    $branchCode  = strtolower(session('role.branch.code'));

    // prefix mengikuti middleware EnsureUserHasCorrectPath
    $prefix = $branchCode ?: $companyCode;

    // Permission checker
    function allow($perm) {
        return Auth::user()?->can($perm);
    }

    // ROUTE ACTIVE CHECK
    function activeBranch($href) {
        return trim(request()->path(), '/') === trim($href, '/')
            || str_starts_with(trim(request()->path(), '/'), trim($href, '/'));
    }

    function groupActive($children) {
        foreach ($children as $c) {
            if (activeBranch($c['href'])) return true;
        }
        return false;
    }

    // ================================================
    // MENUS KHUSUS BRANCH (scope = BRANCH)
    // ================================================
    $items = [

        [
            'label' => 'Dashboard Cabang',
            'href'  => "/$prefix/dashboard",
            'icon'  => 'home',
            'perm'  => 'branch.dashboard',
        ],

        [
            'label' => 'Gudang',
            'icon'  => 'layers',
            'children' => [
                ['label' => 'Stok Barang',   'href' => "/$prefix/gudang",                  'perm' => 'warehouse.view'],
                ['label' => 'Mutasi Stok',   'href' => "/$prefix/request-cabang",          'perm' => 'inventory.transfer'],
                ['label' => 'Analytics',     'href' => "/$prefix/request-cabang/analytics/cabang", 'perm' => 'analytics.inventory'],
            ]
        ],

        [
            'label' => 'Pegawai Cabang',
            'icon'  => 'users',
            'children' => [
                ['label' => 'Daftar Pegawai', 'href' => "/$prefix/pegawai", 'perm' => 'employee.view'],
            ]
        ],

        [
            'label' => 'Pembelian',
            'icon'  => 'cart',
            'children' => [
                ['label' => 'Purchase Order', 'href' => "/$prefix/purchase-order", 'perm' => 'purchase.view'],
                ['label' => 'Supplier',       'href' => "/$prefix/supplier",       'perm' => 'supplier.view'],
            ]
        ],
    ];

    // FILTER MENU TANPA IZIN
    $items = array_filter($items, function ($item) {
        if (isset($item['perm']) && !allow($item['perm'])) return false;
        return true;
    });
@endphp


{{-- MOBILE TOGGLE --}}
<button onclick="toggleSidebarBranch()"
        class="fixed left-4 top-4 z-50 md:hidden h-10 w-10 rounded-lg bg-white border shadow-sm">
    <svg class="h-5 w-5 mx-auto text-gray-700" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

{{-- BACKDROP --}}
<div id="sidebar-branch-backdrop"
     onclick="toggleSidebarBranch()"
     class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

{{-- SIDEBAR BRANCH --}}
<aside id="sidebar-branch"
    class="fixed left-0 top-0 z-40 h-full w-64 bg-white border-r flex flex-col
           transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="px-5 py-5 border-b flex items-center gap-3">
        <div class="h-10 w-10 bg-blue-600 text-white rounded-xl grid place-items-center font-bold">
            B
        </div>
        <div>
            <div class="font-bold text-gray-900">CABANG {{ strtoupper($branchCode) }}</div>
            <div class="text-xs text-gray-500">{{ strtoupper($roleCode) }}</div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isOpen = $hasChildren ? groupActive($item['children']) : activeBranch($item['href'] ?? '');
            @endphp

            {{-- SINGLE LINK --}}
            @if (!$hasChildren)
                @if (!isset($item['perm']) || allow($item['perm']))
                    <a href="{{ $item['href'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                       {{ activeBranch($item['href']) ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        @include('components.sidebar-icon', ['name' => $item['icon']])
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif

            @else
            {{-- MENU WITH CHILDREN --}}
                <div x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">

                    <button @click="open = !open"
                        class="flex items-center justify-between w-full
                               px-3 py-2.5 rounded-lg text-sm font-medium
                               {{ $isOpen ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">

                        <div class="flex items-center gap-3">
                            @include('components.sidebar-icon', ['name' => $item['icon']])
                            <span>{{ $item['label'] }}</span>
                        </div>

                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform">
                            <path fill="currentColor" d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    {{-- CHILDREN --}}
                    <div x-show="open" x-collapse
                         class="ml-8 mt-1 pl-3 border-l space-y-1 border-blue-300">

                        @foreach ($item['children'] as $child)
                            @if (!isset($child['perm']) || allow($child['perm']))
                                <a href="{{ $child['href'] }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                                   {{ activeBranch($child['href'])
                                        ? 'bg-blue-50 text-blue-700 font-medium'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                    <span class="h-1 w-1 rounded-full
                                        {{ activeBranch($child['href']) ? 'bg-blue-600' : 'bg-gray-400' }}"></span>
                                    {{ $child['label'] }}
                                </a>
                            @endif
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
                Logout
            </button>
        </form>
    </div>

</aside>


<script>
    function toggleSidebarBranch() {
        const side = document.getElementById('sidebar-branch');
        const back = document.getElementById('sidebar-branch-backdrop');
        const hidden = side.classList.contains('-translate-x-full');

        side.classList.toggle('-translate-x-full', !hidden);
        back.classList.toggle('hidden', !hidden);

        document.body.style.overflow = hidden ? 'hidden' : '';
    }
</script>
