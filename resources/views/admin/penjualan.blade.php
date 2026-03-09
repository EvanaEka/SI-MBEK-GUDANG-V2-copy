<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - List Penjualan') }}
        </h2>
    </x-slot>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Penjualan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            /* === FONT & ROOT === */
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

            :root {
                --brand-orange: #e58609;
                --brand-orange-light: #ffedd5;
                --brand-orange-dark: #d97b08;
                --chart-primary: #3b82f6;
                --chart-secondary: #10b981;
            }

            /* === BODY & CARD === */
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f9fafb;
            }

            .header-gradient {
                background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .card {
                background-color: white;
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                transition: box-shadow 0.3s ease;
            }

            .card:hover {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            /* === TABLE === */
            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 1200px;
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table th {
                background-color: #f3f4f6;
                font-weight: 600;
                text-align: left;
                padding: 12px 16px;
                color: #374151;
                border-bottom: 2px solid #e5e7eb;
            }

            .table td {
                padding: 12px 16px;
                border-bottom: 1px solid #e5e7eb;
                color: #4b5563;
                vertical-align: top;
            }

            .table tr:hover td {
                background-color: #fffbeb;
            }

            /* === BUTTONS === */
            .btn {
                background-color: var(--brand-orange);
                color: white;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                border: none;
                cursor: pointer;
            }

            .btn:hover {
                background-color: var(--brand-orange-dark);
                transform: translateY(-2px);
            }

            .btn-view {
                background-color: #3b82f6;
            }

            .btn-view:hover {
                background-color: #2563eb;
            }

            .btn-success {
                background-color: #10b981;
            }

            .btn-success:hover {
                background-color: #059669;
            }

            .btn-danger {
                background-color: #ef4444;
            }

            .btn-danger:hover {
                background-color: #dc2626;
            }

            .btn-warning {
                background-color: #f59e0b;
            }

            .btn-warning:hover {
                background-color: #d97706;
            }

            .btn-sm {
                padding: 4px 8px;
                font-size: 12px;
                margin: 2px;
            }

            /* === STATUS BADGES === */
            .status-manual {
                background-color: #e0f2fe;
                color: #0277bd;
                border: 1px solid #81d4fa;
            }

            .status-midtrans {
                background-color: #f3e8ff;
                color: #7c3aed;
                border: 1px solid #c4b5fd;
            }

            .disabled-product {
                opacity: 0.5;
                background-color: #f3f4f6 !important;
            }

            .disabled-product td {
                color: #9ca3af !important;
            }

            /* === PAGINATION & STAT CARD === */
            .pagination {
                display: flex;
                justify-content: center;
                margin-top: 24px;
            }

            .stat-card {
                background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
                border-left: 4px solid var(--brand-orange);
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 24px;
            }

            /* === FILTER BAR (RESPONSIVE) === */
            .filter-bar {
                background-color: white;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 24px;
                display: flex;
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .filter-bar label {
                font-size: 14px;
                font-weight: 500;
            }

            .filter-bar select,
            .filter-bar input[type="date"],
            .filter-bar input[type="text"] {
                padding: 8px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                min-width: 0;
                width: 100%;
                box-sizing: border-box;
            }

            .filter-bar button {
                background-color: var(--brand-orange);
                color: white;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 500;
                width: 100%;
            }

            /* === CHART CONTAINER === */
            .chart-container {
                position: relative;
                height: 400px;
                margin-bottom: 24px;
            }

            .chart-tabs {
                display: flex;
                margin-bottom: 16px;
                border-bottom: 1px solid #e5e7eb;
            }

            .chart-tab {
                padding: 8px 16px;
                cursor: pointer;
                border-bottom: 2px solid transparent;
                margin-right: 8px;
            }

            .chart-tab.active {
                border-bottom-color: var(--brand-orange);
                color: var(--brand-orange);
                font-weight: 500;
            }

            @media (min-width: 768px) {
                .filter-bar {
                    flex-direction: row;
                    flex-wrap: wrap;
                    align-items: center;
                }

                .filter-bar>div {
                    width: auto;
                }

                .filter-bar select,
                .filter-bar input[type="date"],
                .filter-bar input[type="text"],
                .filter-bar button {
                    width: auto;
                    min-width: 180px;
                }
            }

            /* === MODAL === */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .modal-content {
                background-color: white;
                margin: 15% auto;
                padding: 20px;
                border-radius: 12px;
                width: 400px;
                max-width: 90%;
            }

            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            .close:hover {
                color: black;
            }

            /* === MOBILE ADJUSTMENTS === */
            @media (max-width: 768px) {
                .stat-row {
                    flex-direction: column;
                }

                .stat-card {
                    width: 100%;
                }

                .filter-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .btn-group {
                    flex-direction: column;
                    gap: 4px;
                }

                .chart-container {
                    height: 300px;
                }
            }
        </style>
    </head>

    <body>
        <div class="min-h-screen flex flex-col py-12">
            <main class="max-w-7xl mx-auto p-4 w-full">
                <!-- Stat Cards -->
                <div class="stat-row flex flex-wrap gap-6 mb-6">
                    <div class="stat-card flex-1 min-w-[250px]">
                        <div class="flex items-center">
                            <div class="bg-orange-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-shopping-cart text-brand-orange text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Penjualan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $totalPenjualan }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card flex-1 min-w-[250px]">
                        <div class="flex items-center">
                            <div class="bg-orange-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-money-bill-wave text-brand-orange text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Pendapatan</p>
                                <p class="text-2xl font-bold text-gray-900">Rp
                                    {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card flex-1 min-w-[250px]">
                        <div class="flex items-center">
                            <div class="bg-orange-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-users text-brand-orange text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Pembeli Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $pembeliAktif }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Chart Trend Penjualan -->
                <div class="card p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Trend Penjualan</h3>
                    <div class="chart-container">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                    <div class="flex justify-center mt-4">
                        <div class="flex items-center mr-4">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-sm">Jumlah Transaksi</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm">Pendapatan (juta Rp)</span>
                        </div>
                    </div>
                </div>
                <!-- Filter Bar -->
                <form method="GET"
                    class="filter-bar card flex flex-col md:flex-row md:flex-wrap md:items-center gap-4 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <label class="text-gray-700 mb-1 md:mb-0 md:mr-2">Filter:</label>
                        <select name="status" onchange="this.form.submit()" class="w-full md:w-auto">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu
                                Pembayaran</option>
                            <option value="settlement" {{ request('status') == 'settlement' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="expire" {{ request('status') == 'expire' ? 'selected' : '' }}>Kadaluwarsa
                            </option>
                            <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <label class="text-gray-700 mb-1 md:mb-0 md:mr-2">Metode:</label>
                        <select name="payment_method" onchange="this.form.submit()" class="w-full md:w-auto">
                            <option value="all" {{ request('payment_method') == 'all' ? 'selected' : '' }}>Semua
                                Metode</option>
                            <option value="manual" {{ request('payment_method') == 'manual' ? 'selected' : '' }}>Manual
                                Transfer</option>
                            <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>
                                Midtrans</option>
                        </select>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <label class="text-gray-700 mb-1 md:mb-0 md:mr-2">Tanggal:</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full md:w-auto">
                        <span class="mx-2 text-gray-500 hidden md:inline">sampai</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full md:w-auto">
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..."
                            class="w-full md:w-auto min-w-[200px]">
                        <button type="submit" class="ml-0 md:ml-2">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                    </div>
                </form>

                <!-- Tabel Penjualan -->
                <div class="card p-6">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Pembeli</th>
                                    <th>Tanggal</th>
                                    <th class="whitespace-nowrap w-auto">Metode Bayar</th>
                                    <th>Produk</th>
                                    {{-- <th>Jumlah</th> --}}
                                    <th>Total</th>
                                    <th class="whitespace-nowrap w-auto">Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="{{ ($order->status === 'settlement' || $order->status === 'capture') && $order->domba ? 'disabled-product' : '' }}"
                                        id="order-row-{{ $order->id }}">
                                        <td>{{ $order->order_id }}</td>
                                        <td>
                                            <div class="font-medium">{{ $order->user->name ?? '-' }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->user->email ?? '-' }}</div>
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td class="whitespace-nowrap w-auto">
                                            @if ($order->payment_method === 'manual' || $order->payment_method === 'bank_transfer')
                                                <span class="px-2 py-1 rounded-full status-manual text-xs font-medium">
                                                    <i class="fas fa-university mr-1"></i> Manual
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full status-midtrans text-xs font-medium">
                                                    <i class="fas fa-credit-card mr-1"></i> Midtrans
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->orderable)
                                                {{ class_basename($order->orderable_type) }} - {{ $order->orderable->name }}
                                            @else
                                                Produk tidak ditemukan
                                            @endif
                                        </td>
                                        {{-- <td>{{ $order->qty }}</td> --}}
                                        <td class="font-medium">Rp
                                            {{ number_format($order->gross_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="whitespace-nowrap w-auto">
                                            <span id="status-badge-{{ $order->id }}">
                                                @if ($order->status === 'settlement' || $order->status === 'capture')
                                                    <span
                                                        class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">
                                                        <i class="fas fa-check-circle mr-1"></i> Selesai
                                                    </span>
                                                @elseif($order->status === 'pending')
                                                    <span
                                                        class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                                    </span>
                                                @elseif($order->status === 'expire')
                                                    <span
                                                        class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-medium">
                                                        <i class="fas fa-times-circle mr-1"></i> Kadaluwarsa
                                                    </span>
                                                @elseif($order->status === 'cancel')
                                                    <span
                                                        class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-medium">
                                                        <i class="fas fa-ban mr-1"></i> Dibatalkan
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group flex flex-wrap gap-2">
                                                <!-- Detail Button -->
                                                @if ($order->payment_method === 'manual' || $order->payment_method === 'bank_transfer')
                                                    <a href="{{ route('admin.penjualan.manual-invoice', $order->order_id) }}"
                                                        class="btn btn-view btn-sm">
                                                        <i class="fas fa-file-invoice mr-1"></i> Invoice
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.penjualan.invoice', $order->order_id) }}"
                                                        class="btn btn-view btn-sm">
                                                        <i class="fas fa-file-invoice mr-1"></i> Invoice
                                                    </a>
                                                @endif

                                                <!-- Status Change Buttons (only for manual transfer) -->
                                                @if ($order->payment_method === 'manual' || $order->payment_method === 'bank_transfer')
                                                    @if ($order->status !== 'settlement')
                                                        <button onclick="changeOrderStatus({{ $order->id }}, 'settlement')"
                                                            class="btn btn-success btn-sm">
                                                            <i class="fas fa-check mr-1"></i> Terima
                                                        </button>
                                                    @endif

                                                    @if ($order->status === 'cancel')
                                                        <button
                                                            onclick="editNotes({{ $order->id }}, '{{ addslashes($order->admin_notes) }}')"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit mr-1"></i> Edit Catatan
                                                        </button>
                                                    @elseif($order->status !== 'settlement')
                                                        <button onclick="changeOrderStatus({{ $order->id }}, 'cancel')"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times mr-1"></i> Tolak
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-gray-500 py-8">
                                            <i class="fas fa-inbox text-4xl mb-4"></i>
                                            <p>Belum ada transaksi.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($orders->hasPages())
                        <div class="pagination">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle" class="text-lg font-semibold">Konfirmasi Aksi</h3>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <div id="modalBody" class="mb-4">
                    <p>Apakah Anda yakin ingin melakukan aksi ini?</p>
                    <div id="notesContainer" class="mt-4 hidden">
                        <label for="rejectNotes" class="block text-sm font-medium text-gray-700 mb-2">Catatan
                            Penolakan:</label>
                        <textarea id="rejectNotes" rows="3"
                            class="w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm"
                            placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg">Batal</button>
                    <button id="confirmBtn" onclick="confirmAction()"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>

        <script>
            // CSRF Token Setup
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let currentAction = null;
            let currentOrderId = null;
            let currentStatus = null;

            // Modal Functions
            function openModal(title, message, orderId, status, action) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalBody').innerHTML = message;
                document.getElementById('confirmModal').style.display = 'block';
                currentAction = action;
                currentOrderId = orderId;
                currentStatus = status;
            }

            function closeModal() {
                document.getElementById('confirmModal').style.display = 'none';
                currentAction = null;
                currentOrderId = null;
                currentStatus = null;
            }

            // Order Status Change
            function changeOrderStatus(orderId, status) {
                const statusText = status === 'settlement' ? 'menerima' : 'membatalkan';
                const message = `
                <p>Apakah Anda yakin ingin <strong>${statusText}</strong> transaksi ini?</p>
                <p class="text-sm text-gray-600 mt-2">
                    Aksi ini akan ${status === 'settlement' ? 'mengkonfirmasi pembayaran' : 'membatalkan transaksi'}.
                </p>
                ${status === 'cancel' ? `
                                    <div id="notesContainer" class="mt-4">
                                        <label for="rejectNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Catatan Penolakan: <span class="text-red-500">*</span>
                                        </label>
                                        <textarea id="rejectNotes" rows="3" 
                                            class="w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm"
                                            placeholder="Masukkan alasan penolakan..."
                                            required></textarea>
                                    </div>
                                ` : ''}
            `;

                currentOrderId = orderId;
                currentStatus = status;
                currentAction = 'changeStatus';

                openModal(
                    `Konfirmasi ${statusText.charAt(0).toUpperCase() + statusText.slice(1)} Pembayaran`,
                    message,
                    orderId,
                    status,
                    'changeStatus'
                );
            }

            // Product Reactivation
            function reactivateProduct(orderId) {
                const message =
                    `<p>Apakah Anda yakin ingin <strong>mengaktifkan kembali</strong> produk ini?</p>
                           <p class="text-sm text-gray-600 mt-2">Produk akan kembali tersedia untuk dijual dan status transaksi akan diubah ke pending.</p>`;

                openModal('Konfirmasi Aktivasi Produk', message, orderId, 'pending', 'reactivateProduct');
            }

            // Edit Notes
            function editNotes(orderId, currentNotes) {
                const message = `
                <div class="mt-4">
                    <label for="editRejectNotes" class="block text-sm font-medium text-gray-700 mb-2">
                        Edit Catatan Penolakan: <span class="text-red-500">*</span>
                    </label>
                    <textarea id="editRejectNotes" rows="3" 
                        class="w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm"
                        placeholder="Masukkan alasan penolakan..."
                        required>${currentNotes || ''}</textarea>
                </div>
            `;

                currentOrderId = orderId;
                currentAction = 'editNotes';

                openModal(
                    'Edit Catatan Penolakan',
                    message,
                    orderId,
                    'cancel',
                    'editNotes'
                );
            }

            // Confirm Action
            function confirmAction() {
                if (currentAction === 'editNotes') {
                    const notes = document.getElementById('editRejectNotes').value;

                    if (!notes.trim()) {
                        showNotification('Catatan penolakan harus diisi', 'error');
                        return;
                    }

                    fetch(`/admin/orders/${currentOrderId}/notes`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            notes: notes
                        })
                    })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Terjadi kesalahan saat memperbarui catatan');
                            }
                            return data;
                        })
                        .then(data => {
                            showNotification('Catatan berhasil diperbarui!', 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification(error.message, 'error');
                        })
                        .finally(() => {
                            closeModal();
                        });
                } else if (currentAction === 'changeStatus') {
                    const notes = currentStatus === 'cancel' ? document.getElementById('rejectNotes')?.value : '';

                    // Validasi notes hanya jika status cancel
                    if (currentStatus === 'cancel' && !notes?.trim()) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Harap isi catatan penolakan',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }

                    fetch(`/admin/orders/${currentOrderId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            status: currentStatus,
                            notes: notes || ''
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateStatusBadge(currentOrderId, currentStatus);
                                showNotification(
                                    currentStatus === 'settlement' ? 'Pembayaran berhasil diterima!' :
                                        'Pembayaran ditolak!',
                                    'success'
                                );
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                throw new Error(data.message || 'Terjadi kesalahan');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Terjadi kesalahan saat mengupdate status: ' + error.message, 'error');
                        })
                        .finally(() => {
                            closeModal();
                        });
                } else if (currentAction === 'reactivateProduct') {
                    reactivateProductStatus();
                }
            }

            // Update Status Badge
            function updateStatusBadge(orderId, status) {
                const statusBadge = document.getElementById(`status-badge-${orderId}`);
                let badgeHTML = '';

                switch (status) {
                    case 'settlement':
                        badgeHTML =
                            '<span class="px-2 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium"><i class="fas fa-check-circle mr-1"></i> Selesai</span>';
                        break;
                    case 'pending':
                        badgeHTML =
                            '<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium"><i class="fas fa-clock mr-1"></i> Menunggu</span>';
                        break;
                    case 'cancel':
                        badgeHTML =
                            '<span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-xs font-medium"><i class="fas fa-ban mr-1"></i> Dibatalkan</span>';
                        break;
                    default:
                        badgeHTML =
                            `<span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium">${status}</span>`;
                }

                statusBadge.innerHTML = badgeHTML;
            }

            // Show Notification
            function showNotification(message, type = 'success') {
                Swal.fire({
                    title: type === 'success' ? 'Berhasil!' : 'Error!',
                    text: message,
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
            // Sales Trend Chart
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('salesTrendChart').getContext('2d');

                // Data dari controller (akan kita buat nanti)
                const salesData = {
                    labels: {!! json_encode($salesTrend['labels']) !!},
                    transactionCounts: {!! json_encode($salesTrend['counts']) !!},
                    revenueData: {!! json_encode($salesTrend['revenues']) !!}
                };

                // Konversi revenue ke juta rupiah agar skalanya lebih sesuai
                const revenueInMillions = salesData.revenueData.map(amount => amount / 1000000);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: salesData.labels,
                        datasets: [
                            {
                                label: 'Jumlah Transaksi',
                                data: salesData.transactionCounts,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                yAxisID: 'y',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Total Pendapatan (juta Rp)',
                                data: revenueInMillions,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                yAxisID: 'y1',
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Jumlah Transaksi'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Pendapatan (juta Rp)'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                // Mengatasi perbedaan skala
                                suggestedMin: 0,
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.datasetIndex === 0) {
                                            label += context.parsed.y + ' transaksi';
                                        } else {
                                            label += 'Rp ' + (context.parsed.y * 1000000).toLocaleString('id-ID');
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });     
        </script>
    </body>
</x-admin-app-layout>