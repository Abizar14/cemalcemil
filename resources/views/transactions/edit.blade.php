@php
    $title = 'Edit Transaksi';
    $initialItems = collect(old('items', $transaction->details->map(fn ($detail) => [
        'product_id' => $detail->product_id,
        'qty' => $detail->qty,
    ])->values()->all()))
        ->filter(fn ($item) => isset($item['product_id'], $item['qty']))
        ->map(fn ($item) => [
            'product_id' => (int) $item['product_id'],
            'qty' => (int) $item['qty'],
        ])
        ->values()
        ->all();
    $productData = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->price,
        'category_id' => $product->category_id,
    ])->values()->all();
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Edit Transaksi')
@section('panel-title', 'Update ' . $transaction->invoice_number)
@section('panel-description', 'Perbarui item, metode bayar, atau catatan transaksi. Untuk QRIS, perubahan transaksi akan mengembalikan status ke pending agar bisa dikonfirmasi ulang.')

@section('panel-actions')
    <a href="{{ route('transactions.show', $transaction) }}" class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Kembali ke Struk</a>
@endsection

@section('panel-content')
    @if ($errors->has('items') || $errors->has('paid_amount'))
        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first('items') ?: $errors->first('paid_amount') }}
        </div>
    @endif

    <form method="POST" action="{{ route('transactions.update', $transaction) }}" id="cashier-form" class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        @csrf
        @method('PUT')

        <section class="space-y-6">
            <div class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Katalog Produk</p>
                        <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Edit isi transaksi</h2>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <input id="product-search" type="text" placeholder="Cari produk..." class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                        <select id="category-filter" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="product-grid" class="mt-5 grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                    @foreach ($products as $product)
                        <button type="button" data-product-card data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ number_format((float) $product->price, 2, '.', '') }}" data-category-id="{{ $product->category_id }}" class="group rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4 text-left transition hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-display text-lg font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="mt-2 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $product->category->name }}</p>
                                </div>
                                <span class="rounded-2xl bg-orange-500 px-3 py-2 text-xs font-semibold text-white">Tambah</span>
                            </div>
                            <p class="font-display mt-4 text-2xl font-semibold text-orange-600">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.05s;">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Keranjang</p>
                        <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Ringkasan belanja</h2>
                    </div>
                    <div id="cart-count" class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">0 item</div>
                </div>

                <div id="cart-empty" class="mt-5 rounded-[1.5rem] border border-dashed border-slate-300 bg-white/70 px-5 py-10 text-center text-sm text-slate-500">Belum ada produk di keranjang.</div>
                <div id="cart-items" class="mt-5 space-y-3"></div>

                <div class="mt-5 rounded-[1.5rem] border border-slate-200/80 bg-slate-900 p-5 text-white">
                    <div class="flex items-center justify-between text-sm text-slate-300">
                        <span>Total transaksi</span>
                        <span id="summary-qty">0 item</span>
                    </div>
                    <p id="summary-total" class="font-display mt-3 text-3xl font-semibold">Rp0</p>
                    <p class="mt-2 text-sm text-slate-300">Perhitungan total dilakukan otomatis dari item yang dipilih.</p>
                </div>

                <div id="hidden-items"></div>
            </section>

            <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.1s;">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Pembayaran</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Metode dan catatan</h2>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <label class="cursor-pointer rounded-[1.5rem] border border-slate-200 bg-white/80 p-4 transition has-[:checked]:border-orange-300 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="cash" class="sr-only" {{ old('payment_method', $transaction->payment_method) === 'cash' ? 'checked' : '' }}>
                        <span class="font-display text-lg font-semibold text-slate-900">Cash</span>
                        <span class="mt-2 block text-sm text-slate-600">Hitung bayar dan kembalian otomatis.</span>
                    </label>

                    <label class="cursor-pointer rounded-[1.5rem] border border-slate-200 bg-white/80 p-4 transition has-[:checked]:border-orange-300 has-[:checked]:bg-orange-50">
                        <input type="radio" name="payment_method" value="qris" class="sr-only" {{ old('payment_method', $transaction->payment_method) === 'qris' ? 'checked' : '' }}>
                        <span class="font-display text-lg font-semibold text-slate-900">QRIS</span>
                        <span class="mt-2 block text-sm text-slate-600">Perubahan akan perlu konfirmasi ulang.</span>
                    </label>
                </div>

                <div id="cash-payment-box" class="mt-5">
                    <label for="paid_amount" class="mb-2 block text-sm font-medium text-slate-700">Nominal dibayar</label>
                    <input id="paid_amount" type="number" min="0" step="0.01" name="paid_amount" value="{{ old('paid_amount', number_format((float) ($transaction->paid_amount ?? 0), 2, '.', '')) }}" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" placeholder="Masukkan nominal bayar">
                    <p id="change-preview" class="mt-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">Kembalian: Rp0</p>
                </div>

                <div id="qris-payment-box" class="mt-5 hidden rounded-[1.5rem] border border-sky-200 bg-sky-50 p-4 text-sm text-sky-700">
                    Setelah diupdate, transaksi QRIS akan kembali dicek kasir sebelum dianggap lunas.
                </div>

                <div class="mt-5">
                    <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100" placeholder="Catatan tambahan untuk transaksi ini">{{ old('notes', $transaction->notes) }}</textarea>
                </div>

                <button type="submit" class="font-display mt-5 w-full rounded-2xl bg-slate-900 px-5 py-4 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600">Update Transaksi</button>
            </section>
        </aside>
    </form>

    <script>
        (() => {
            const productData = @json($productData);
            const initialItems = @json($initialItems);
            const cart = new Map();
            const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Math.max(Number(value) || 0, 0));
            const productGrid = document.getElementById('product-grid');
            const productSearch = document.getElementById('product-search');
            const categoryFilter = document.getElementById('category-filter');
            const cartItems = document.getElementById('cart-items');
            const cartEmpty = document.getElementById('cart-empty');
            const cartCount = document.getElementById('cart-count');
            const summaryQty = document.getElementById('summary-qty');
            const summaryTotal = document.getElementById('summary-total');
            const hiddenItems = document.getElementById('hidden-items');
            const paidAmountInput = document.getElementById('paid_amount');
            const changePreview = document.getElementById('change-preview');
            const cashPaymentBox = document.getElementById('cash-payment-box');
            const qrisPaymentBox = document.getElementById('qris-payment-box');
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const findProduct = (productId) => productData.find((product) => product.id === productId);

            const renderCart = () => {
                cartItems.innerHTML = '';
                hiddenItems.innerHTML = '';
                let totalQty = 0;
                let totalAmount = 0;

                cartEmpty.classList.toggle('hidden', cart.size !== 0);

                Array.from(cart.values()).forEach((item, index) => {
                    totalQty += item.qty;
                    totalAmount += item.qty * item.price;
                    const row = document.createElement('div');
                    row.className = 'rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4';
                    row.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-display text-base font-semibold text-slate-900">${item.name}</p>
                                <p class="mt-1 text-sm text-slate-500">Rp${formatRupiah(item.price)} per item</p>
                            </div>
                            <button type="button" data-remove="${item.product_id}" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Hapus</button>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <button type="button" data-dec="${item.product_id}" class="h-10 w-10 rounded-2xl border border-slate-200 bg-white text-lg font-semibold text-slate-700">-</button>
                                <span class="min-w-10 text-center text-sm font-semibold text-slate-700">${item.qty}</span>
                                <button type="button" data-inc="${item.product_id}" class="h-10 w-10 rounded-2xl border border-slate-200 bg-white text-lg font-semibold text-slate-700">+</button>
                            </div>
                            <p class="font-display text-lg font-semibold text-orange-600">Rp${formatRupiah(item.qty * item.price)}</p>
                        </div>`;
                    cartItems.appendChild(row);

                    const productInput = document.createElement('input');
                    productInput.type = 'hidden';
                    productInput.name = `items[${index}][product_id]`;
                    productInput.value = item.product_id;
                    hiddenItems.appendChild(productInput);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${index}][qty]`;
                    qtyInput.value = item.qty;
                    hiddenItems.appendChild(qtyInput);
                });

                cartCount.textContent = `${totalQty} item`;
                summaryQty.textContent = `${totalQty} item`;
                summaryTotal.textContent = `Rp${formatRupiah(totalAmount)}`;
                updateChangePreview(totalAmount);
            };

            const addToCart = (productId) => {
                const product = findProduct(productId);
                if (!product) return;
                const current = cart.get(productId) || { product_id: product.id, name: product.name, price: product.price, qty: 0 };
                current.qty += 1;
                cart.set(productId, current);
                renderCart();
            };

            const adjustQty = (productId, delta) => {
                const item = cart.get(productId);
                if (!item) return;
                item.qty += delta;
                if (item.qty <= 0) cart.delete(productId); else cart.set(productId, item);
                renderCart();
            };

            const removeFromCart = (productId) => {
                cart.delete(productId);
                renderCart();
            };

            const updateChangePreview = (totalAmount) => {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                const paidAmount = Number(paidAmountInput.value || 0);
                changePreview.textContent = selectedMethod === 'cash'
                    ? `Kembalian: Rp${formatRupiah(Math.max(paidAmount - totalAmount, 0))}`
                    : 'Kembalian: Rp0';
            };

            const syncPaymentMode = () => {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                const totalAmount = Array.from(cart.values()).reduce((sum, item) => sum + (item.qty * item.price), 0);
                cashPaymentBox.classList.toggle('hidden', selectedMethod !== 'cash');
                qrisPaymentBox.classList.toggle('hidden', selectedMethod === 'cash');
                if (selectedMethod !== 'cash') paidAmountInput.value = '';
                updateChangePreview(totalAmount);
            };

            const filterProducts = () => {
                const searchTerm = productSearch.value.trim().toLowerCase();
                const selectedCategory = categoryFilter.value;
                productGrid.querySelectorAll('[data-product-card]').forEach((card) => {
                    const name = card.dataset.productName.toLowerCase();
                    const categoryId = card.dataset.categoryId;
                    card.classList.toggle('hidden', !(name.includes(searchTerm) && (selectedCategory === '' || categoryId === selectedCategory)));
                });
            };

            productGrid.addEventListener('click', (event) => {
                const button = event.target.closest('[data-product-card]');
                if (button) addToCart(Number(button.dataset.productId));
            });

            cartItems.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-remove]');
                const incButton = event.target.closest('[data-inc]');
                const decButton = event.target.closest('[data-dec]');
                if (removeButton) removeFromCart(Number(removeButton.dataset.remove));
                if (incButton) adjustQty(Number(incButton.dataset.inc), 1);
                if (decButton) adjustQty(Number(decButton.dataset.dec), -1);
            });

            productSearch.addEventListener('input', filterProducts);
            categoryFilter.addEventListener('change', filterProducts);
            paidAmountInput.addEventListener('input', () => {
                const totalAmount = Array.from(cart.values()).reduce((sum, item) => sum + (item.qty * item.price), 0);
                updateChangePreview(totalAmount);
            });
            paymentRadios.forEach((radio) => radio.addEventListener('change', syncPaymentMode));

            initialItems.forEach((item) => {
                const product = findProduct(item.product_id);
                if (!product) return;
                cart.set(item.product_id, { product_id: product.id, name: product.name, price: product.price, qty: item.qty });
            });

            renderCart();
            syncPaymentMode();
            filterProducts();
        })();
    </script>
@endsection
