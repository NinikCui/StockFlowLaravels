@php
    use Illuminate\Support\Str;

    $username = session('user.username', 'User');
    $roleCode = session('role.code', 'USER');

    $role  = session('role');
    $scope = $role['scope'];
    $code  = strtolower(
        $scope === 'COMPANY'
            ? $role['company']['code']
            : $role['branch']['code']
    );

    $items = menuItems();


    // ============================
    // tenantHref()
    // ============================
    if (!function_exists('tenantHref')) {
        function tenantHref($href)
{
    $role  = session('role');
    $scope = $role['scope'];
    $code  = strtolower(
        $scope === 'COMPANY'
            ? $role['company']['code']
            : $role['branch']['code']
    );

    $prefix = $scope === 'COMPANY'
        ? "company/$code"
        : "branch/$code";


    // ========== ANTIDUP PREFIX ==========
    if (\Illuminate\Support\Str::startsWith($href, $prefix)) {
        
        return '/' . trim($href, '/');
    }

    if (\Illuminate\Support\Str::startsWith($href, '/'.$prefix)) {
        
        return '/' . trim($href, '/');
    }

    // ========== DASHBOARD ==========
    if ($href === 'dashboard') {
        $res = $scope === 'COMPANY'
            ? "/company/$code/dashboard"
            : "/branch/$code/dashboard";

        return $res;
    }

    // ========== NORMAL CASE ==========
    $final = "/$prefix/" . trim($href, '/');



    return $final;
}

    }


    // ============================
    // isActive
    // ============================
    if (!function_exists('isActive')) {
        function isActive($href)
{
    $final = tenantHref($href);

   

    return request()->is(ltrim($final, '/'))
        || request()->is(ltrim($final, '/').'/*');
}

    }

    // ============================
    // anyChildActive
    // ============================
    if (!function_exists('anyChildActive')) {
        function anyChildActive($children)
{
    foreach ($children as $c) {

       

        if (isActive($c['href'])) return true;
    }
    return false;
}

    }
@endphp





<button onclick="toggleSidebar()" class="fixed left-4 top-4 z-50 md:hidden h-10 w-10 rounded-lg bg-white border shadow-sm">
    <svg class="h-5 w-5 mx-auto text-gray-700" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<aside id="sidebar"
    class="fixed left-0 top-0 z-40 h-full w-64 bg-white border-r flex flex-col
           transition-transform duration-300 -translate-x-full md:translate-x-0">

    {{-- HEADER --}}
    <div class="px-5 py-5 border-b flex items-center gap-3">
        <div class="h-10 w-10 bg-emerald-600 text-white rounded-xl grid place-items-center font-bold">R</div>

        <div>
            <div class="font-bold text-gray-900">{{ strtoupper($role['company']['code']) }}</div>
            <div class="text-xs text-gray-500">{{ strtoupper($roleCode) }}</div>
        </div>
    </div>

    {{-- NAV MENU --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        @foreach ($items as $item)
            @php
                $hasChildren = isset($item['children']);
                $isOpen = $hasChildren
                    ? anyChildActive($item['children'])
                    : (!($item['parent_only'] ?? false) && isActive($item['href'] ?? ''));
            @endphp

            {{-- MENU TANPA CHILDREN --}}
            @if (!$hasChildren)
                <a href="{{ tenantHref($item['href']) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                    {{ isActive($item['href']) ? 'bg-emerald-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    @include('components.sidebar-icon', ['name' => $item['icon']])
                    <span>{{ $item['label'] }}</span>
                </a>

            @else

                {{-- MENU CHILDREN --}}
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

                    <div x-show="open" x-collapse class="ml-8 mt-1 pl-3 border-l space-y-1 border-emerald-300">
                        @foreach ($item['children'] as $child)
                            <a href="{{ tenantHref($child['href']) }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                               {{ isActive($child['href'])
                                   ? 'bg-emerald-50 text-emerald-700 font-medium'
                                   : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="h-1 w-1 rounded-full
                                    {{ isActive($child['href']) ? 'bg-emerald-600' : 'bg-gray-400' }}"></span>
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
