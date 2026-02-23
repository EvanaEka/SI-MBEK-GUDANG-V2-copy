<x-admin-app-layout>
    <div class="p-6 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.purchase-orders.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Purchase Order</h1>
                <p class="text-sm text-gray-500 mt-0.5">Isi detail pemesanan bahan baku atau produk</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-red-700 mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="po-form">
            @csrf

            <div class="space-y-6">

                {{-- Informasi Umum --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-700 mb-5 flex items-center gap-2">
                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-md flex items-center justify-center text-xs font-bold">1</span>
                        Informasi Umum
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Supplier --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Supplier <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tipe PO --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe PO <span class="text-red-500">*</span></label>
                            <select name="type" id="po-type" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="material" {{ old('type') === 'material' ? 'selected' : '' }}>Material (Bahan Baku)</option>
                                <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Produk Jadi</option>
                            </select>
                        </div>

                        {{-- Tanggal Pesan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pesan <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_pesan" value="{{ old('tanggal_pesan', date('Y-m-d')) }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition">
                        </div>

                       {{-- Dipesan Oleh (Admin Only) --}}
@if(auth()->guard('admin')->check())
<div class="col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Dipesan Atas Nama <span class="text-red-500">*</span></label>
        <div class="flex gap-4 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="radio" name="dipesan_oleh_type" value="Admin" class="text-orange-500 focus:ring-orange-400" checked onchange="toggleOwnerSelect(false)">
                <span class="text-sm text-gray-700 group-hover:text-gray-900">Admin (Saya)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="radio" name="dipesan_oleh_type" value="Owner" class="text-orange-500 focus:ring-orange-400" {{ old('dipesan_oleh_type') === 'Owner' ? 'checked' : '' }} onchange="toggleOwnerSelect(true)">
                <span class="text-sm text-gray-700 group-hover:text-gray-900">Owner</span>
            </label>
        </div>
    </div>

    {{-- Dropdown Pilih Owner (Muncul hanya jika radio Owner dipilih) --}}
    <div id="owner-select-wrap" class="{{ old('dipesan_oleh_type') === 'Owner' ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Nama Owner <span class="text-red-500">*</span></label>
        <select name="dipesan_oleh_id" id="dipesan_oleh_id" 
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition">
            <option value="">-- Pilih Owner --</option>
            @foreach(App\Models\Owner::all() as $owner)
                <option value="{{ $owner->id }}" {{ old('dipesan_oleh_id') == $owner->id ? 'selected' : '' }}>
                    {{ $owner->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
@endif
                    </div>

                    {{-- Catatan --}}
                    <div class="mt-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea name="catatan_owner" rows="3" placeholder="Tambahkan catatan opsional..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition resize-none">{{ old('catatan_owner') }}</textarea>
                    </div>
                </div>

                {{-- Item PO --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-md flex items-center justify-center text-xs font-bold">2</span>
                            Item Pesanan
                        </h2>
                        <button type="button" id="add-item"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-orange-600 hover:text-orange-800 border border-orange-300 hover:border-orange-500 px-3 py-1.5 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item
                        </button>
                    </div>

                    <div id="items-container" class="space-y-3">
                        {{-- Item rows will be added here by JS --}}
                    </div>

                    {{-- Total --}}
                    <div class="mt-5 pt-4 border-t border-gray-100 flex justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Estimasi</p>
                            <p class="text-2xl font-bold text-gray-800 mt-0.5" id="grand-total">Rp 0</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.purchase-orders.index') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg shadow transition-colors">
                        Simpan Purchase Order
                    </button>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // ─── Data ───────────────────────────────────────────
        const materials = @json($materials->map(fn($m) => ['id' => $m->id, 'nama' => $m->nama_bahan, 'satuan' => $m->satuan]));

        // ─── Item counter ────────────────────────────────────
        let itemIndex = 0;

        // ─── Add Item Button ─────────────────────────────────
        document.getElementById('add-item').addEventListener('click', () => addItem());

        // ─── Owner radio toggle ──────────────────────────────
        document.querySelectorAll('input[name="dipesan_oleh_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const wrap = document.getElementById('owner-select-wrap');
                if (wrap) wrap.classList.toggle('hidden', e.target.value !== 'Owner');
            });
        });

        // ─── Add first item on load ──────────────────────────
        addItem();

        function addItem() {
            const container = document.getElementById('items-container');
            const idx = itemIndex++;

            const materialOptions = materials.map(m =>
                `<option value="${m.id}">${m.nama} (${m.satuan})</option>`
            ).join('');

            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-12 gap-3 items-start bg-gray-50 rounded-lg p-4 border border-gray-200';
            row.dataset.index = idx;
            row.innerHTML = `
                <div class="col-span-12 sm:col-span-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bahan / Material</label>
                    <select name="items[${idx}][material_id]" required onchange="updateSatuan(this, ${idx})"
                        class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                        <option value="">-- Pilih Material --</option>
                        ${materialOptions}
                    </select>
                </div>
                <div class="col-span-4 sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah</label>
                    <input type="number" name="items[${idx}][jumlah]" min="1" placeholder="0" required
                        oninput="calcRow(${idx})"
                        class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="col-span-8 sm:col-span-4">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="items[${idx}][harga_satuan]" min="0" placeholder="0" required
                        oninput="calcRow(${idx})"
                        class="w-full border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
                <div class="col-span-12 sm:col-span-1 flex sm:flex-col items-end gap-2 sm:pt-5">
                    <button type="button" onclick="removeItem(this)" 
                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div class="col-span-12">
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span id="satuan-label-${idx}"></span>
                        <span>Subtotal: <strong class="text-gray-700" id="subtotal-${idx}">Rp 0</strong></span>
                    </div>
                </div>
            `;
            container.appendChild(row);
        }

        function removeItem(btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('Minimal harus ada 1 item.');
                return;
            }
            btn.closest('.item-row').remove();
            updateGrandTotal();
        }

        function updateSatuan(select, idx) {
            const mat = materials.find(m => m.id == select.value);
            const label = document.getElementById(`satuan-label-${idx}`);
            if (label) label.textContent = mat ? `Satuan: ${mat.satuan}` : '';
        }

        function calcRow(idx) {
            const row = document.querySelector(`[data-index="${idx}"]`);
            const qty = parseFloat(row.querySelector(`[name="items[${idx}][jumlah]"]`).value) || 0;
            const price = parseFloat(row.querySelector(`[name="items[${idx}][harga_satuan]"]`).value) || 0;
            const subtotal = qty * price;
            const el = document.getElementById(`subtotal-${idx}`);
            if (el) el.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const idx = row.dataset.index;
                const qty = parseFloat(row.querySelector(`[name="items[${idx}][jumlah]"]`)?.value) || 0;
                const price = parseFloat(row.querySelector(`[name="items[${idx}][harga_satuan]"]`)?.value) || 0;
                total += qty * price;
            });
            document.getElementById('grand-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // ─── Form validation before submit ──────────────────
        document.getElementById('po-form').addEventListener('submit', (e) => {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Tambahkan minimal 1 item pesanan.');
            }
        });
    </script>
    @endpush
</x-admin-app-layout>