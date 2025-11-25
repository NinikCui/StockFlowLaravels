<div id="pegawaiDeleteModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden justify-center items-center z-50 p-4"
    onclick="if(event.target === this) closeDeletePegawai()">

    <div class="bg-white p-8 rounded-2xl max-w-md w-full shadow-2xl transform transition-all"
        onclick="event.stopPropagation()">
        
        {{-- Icon Warning --}}
        <div class="h-16 w-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5">
            <span class="text-4xl">⚠️</span>
        </div>

        {{-- Content --}}
        <h2 class="font-bold text-2xl mb-3 text-center text-gray-900">Hapus Pegawai?</h2>
        <p class="text-sm text-gray-500 mb-8 text-center leading-relaxed">
            Aksi ini akan menghapus pegawai secara permanen dan tidak dapat dibatalkan. Apakah Anda yakin?
        </p>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button onclick="closeDeletePegawai()"
                class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors duration-200">
                Batal
            </button>

            <form id="pegawaiDeleteForm" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                    Hapus Sekarang
                </button>
            </form>
        </div>
    </div>

</div>