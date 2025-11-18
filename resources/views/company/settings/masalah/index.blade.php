<x-app-layout>
<main class="max-w-4xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Kategori Masalah Stok</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola kategori penyebab penyesuaian stok.</p>
        </div>

        <button onclick="openAddIssueModal()"
            class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-sm shadow-sm transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kategori
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white border rounded-2xl shadow-sm p-6">

        @if (session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-700 border-b">
                    <th class="p-3 font-semibold text-left">Nama</th>
                    <th class="p-3 font-semibold text-left">Deskripsi</th>
                    <th class="p-3 font-semibold text-center w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($issues as $issue)
                    <tr class="hover:bg-gray-50 transition">

                        <td class="p-3 border-b font-medium text-gray-900">
                            {{ $issue->name }}
                        </td>

                        <td class="p-3 border-b text-gray-600">
                            {{ $issue->desc ?? '-' }}
                        </td>

                        <td class="p-3 border-b">
                            <div class="flex items-center justify-center gap-2">

                                {{-- EDIT --}}
                                <button onclick="openEditIssueModal(this)"
                                    data-id="{{ $issue->id }}"
                                    data-name="{{ $issue->name }}"
                                    data-desc="{{ $issue->desc }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg 
                                           bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5h2m-1-1v2m7.364 1.364l-8.485 8.485a2 2 0 01-.707.464l-3.536 1.178 
                                               1.178-3.536a2 2 0 01.464-.707l8.485-8.485a2.5 2.5 0 113.535 3.536z" />
                                    </svg>
                                    Edit
                                </button>

                                {{-- DELETE --}}
                                <button onclick="openDeleteIssueModal(this)"
                                    data-id="{{ $issue->id }}"
                                    data-name="{{ $issue->name }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg 
                                           bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.996-1.858L5 7m5 4v6m4-6v6m1-10V5a2 
                                               2 0 00-2-2h-2a2 2 0 00-2 2v2M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500 text-sm italic">
                            Belum ada kategori masalah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</main>


{{-- ===================================================== --}}
{{-- MODAL: ADD ISSUE --}}
{{-- ===================================================== --}}
<div id="addIssueModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

    <div class="bg-white rounded-2xl p-7 w-full max-w-md shadow-xl animate-fadeIn">

        <h2 class="text-xl font-bold mb-4 text-gray-900">Tambah Kategori Masalah</h2>

        <form method="POST" action="{{ route('issues.store', $companyCode) }}">
            @csrf

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Nama Kategori</label>
                <input type="text" name="name"
                       class="w-full border-gray-300 rounded-lg px-4 py-2 mt-1 focus:ring-2 focus:ring-emerald-500"
                       required>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Deskripsi</label>
                <textarea name="desc" rows="3"
                          class="w-full border-gray-300 rounded-lg px-4 py-2 mt-1 focus:ring-2 focus:ring-emerald-500"
                          placeholder="Wajib" required></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddIssueModal()"
                        class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>

                <button
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL: EDIT ISSUE --}}
{{-- ===================================================== --}}
<div id="editIssueModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

    <div class="bg-white rounded-2xl p-7 w-full max-w-md shadow-xl animate-fadeIn">

        <h2 class="text-xl font-bold mb-4 text-gray-900">Edit Kategori Masalah</h2>

        <form id="editIssueForm" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Nama Kategori</label>
                <input type="text" id="edit_issue_name" name="name"
                       class="w-full border-gray-300 rounded-lg px-4 py-2 mt-1 focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Deskripsi</label>
                <textarea id="edit_issue_desc" name="desc" rows="3"
                          class="w-full border-gray-300 rounded-lg px-4 py-2 mt-1 focus:ring-2 focus:ring-blue-500"
                          placeholder="Wajib" required></textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditIssueModal()"
                        class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>

                <button
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update
                </button>
            </div>
        </form>

    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL: DELETE ISSUE --}}
{{-- ===================================================== --}}
<div id="deleteIssueModal"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden z-50">

    <div class="bg-white rounded-2xl shadow-xl p-7 w-full max-w-md animate-fadeIn">

        <div class="flex flex-col items-center text-center">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-red-600 mb-4" 
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M12 9v3m0 4h.01M4.34 17.053 12 3l7.66 14.053A2 2 0 0 1 17.86 21H6.14a2 2 0 0 1-1.8-3.947Z" />
            </svg>

            <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Kategori</h2>

            <p class="text-gray-600 text-sm mb-4">
                Apakah yakin ingin menghapus kategori:
                <span id="del_issue_name" class="font-semibold text-gray-800"></span>?
            </p>
        </div>

        <form id="deleteIssueForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeDeleteIssueModal()"
                        class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>

                <button
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Hapus
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ===================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ===================================================== --}}
<script>

    function openAddIssueModal() {
        document.getElementById('addIssueModal').classList.remove('hidden');
    }
    function closeAddIssueModal() {
        document.getElementById('addIssueModal').classList.add('hidden');
    }

    function openEditIssueModal(button) {
        const id   = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const desc = button.getAttribute('data-desc');

        document.getElementById('edit_issue_name').value = name;
        document.getElementById('edit_issue_desc').value = desc ?? '';

        document.getElementById('editIssueForm').action =
            `/{{ $companyCode }}/settings/masalah/${id}`;

        document.getElementById('editIssueModal').classList.remove('hidden');
    }
    function closeEditIssueModal() {
        document.getElementById('editIssueModal').classList.add('hidden');
    }

    function openDeleteIssueModal(button) {
        const id   = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');

        document.getElementById('del_issue_name').innerText = name;

        document.getElementById('deleteIssueForm').action =
            `/{{ $companyCode }}/settings/masalah/${id}`;

        document.getElementById('deleteIssueModal').classList.remove('hidden');
    }
    function closeDeleteIssueModal() {
        document.getElementById('deleteIssueModal').classList.add('hidden');
    }

</script>

</x-app-layout>
