@props([
    'permissions' => [],   // array group => items[]
    'selected'    => [],   // array kode yang sudah terpilih
])

<div class="space-y-3">
    @foreach($permissions as $group => $items)
        <div x-data="{ open: false }"
             class="rounded-xl border border-gray-200 bg-white overflow-hidden">

            {{-- HEADER GROUP --}}
            <div class="flex items-center justify-between px-5 py-4 cursor-pointer bg-gradient-to-r from-gray-50 to-white hover:bg-gray-100"
                 @click="open = !open">
                <div class="flex items-center gap-3">
                    <span class="h-8 w-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center font-bold">
                        {{ strtoupper(substr($group, 0, 1)) }}
                    </span>
                    <span class="font-bold text-gray-900">{{ ucfirst($group) }}</span>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                        {{ count($items) }} items
                    </span>
                </div>

                <svg class="h-5 w-5 text-gray-500 transition-transform"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </div>

            {{-- ITEMS --}}
            <div x-show="open" x-collapse class="border-t border-gray-200 bg-gray-50 p-4">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($items as $p)
                        <label class="group flex items-start gap-3 bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm hover:border-emerald-200 hover:bg-emerald-50 transition-all cursor-pointer">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $p['code'] }}"
                                   class="mt-0.5 h-5 w-5 rounded text-emerald-600"
                                   {{ in_array($p['code'], $selected) ? 'checked' : '' }}>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 leading-tight">
                                    {{ strtoupper($p['resource']) }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ ucfirst($p['action']) }}
                                </div>
                                <code class="text-xs text-gray-600 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-md inline-block mt-1">
                                    {{ $p['code'] }}
                                </code>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
