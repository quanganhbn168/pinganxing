@csrf

{{-- PHẦN 1: THÔNG TIN KHÁCH HÀNG VÀ ĐƠN HÀNG --}}

<div class="row">

    <div class="col-md-4">

        <h5>1. Thông tin khách hàng</h5>

        <div class="mb-3">

            <label for="user_id" class="form-label">Chọn khách hàng</label>

            <select class="form-control" id="user_id" name="user_id">

                <option value="">-- Chọn khách hàng có sẵn --</option>

                @foreach($users as $user)

                    <option value="{{ $user->id }}" @if(old('user_id', $order->user_id ?? '') == $user->id) selected @endif

                        data-name="{{$user->name}}" data-email="{{$user->email}}" data-phone="{{$user->phone}}" data-address="{{$user->address}}">

                        {{ $user->name }} - {{ $user->phone }}

                    </option>

                @endforeach

            </select>

            <div class="form-text">Hoặc điền thông tin khách vãng lai ở dưới.</div>

        </div>

        <div id="guest-info">

            <div class="mb-3">

                <label for="customer_name" class="form-label">Tên khách vãng lai</label>

                <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ old('customer_name', $order->customer_name ?? '') }}">

            </div>

            <div class="mb-3">

                <label for="customer_phone" class="form-label">SĐT khách vãng lai</label>

                <input type="text" class="form-control" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $order->customer_phone ?? '') }}">

            </div>

             <div class="mb-3">

                <label for="customer_email" class="form-label">Email khách vãng lai</label>

                <input type="email" class="form-control" name="customer_email" id="customer_email" value="{{ old('customer_email', $order->customer_email ?? '') }}">

            </div>

            <div class="mb-3">

                <label for="customer_address" class="form-label">Địa chỉ khách vãng lai</label>

                <input type="text" class="form-control" name="customer_address" id="customer_address" value="{{ old('customer_address', $order->customer_address ?? '') }}">

            </div>

        </div>

    </div>

    <div class="col-md-8">

        <h5>2. Thông tin đơn hàng</h5>

        <div class="row">

            <div class="col-md-12 mb-3">

                <label for="status" class="form-label">Trạng thái</label>

                <select class="form-control form-control" id="status" name="status">

                    <option value="pending" @if(old('status', $order->status ?? 'pending') == 'pending') selected @endif>Chờ xử lý</option>

                    <option value="processing" @if(old('status', $order->status ?? '') == 'processing') selected @endif>Đang xử lý</option>

                    <option value="completed" @if(old('status', $order->status ?? '') == 'completed') selected @endif>Hoàn thành</option>

                    <option value="cancelled" @if(old('status', $order->status ?? '') == 'cancelled') selected @endif>Đã hủy</option>

                </select>

            </div>


        </div>

        <div class="mb-3">

            <label for="note" class="form-label">Ghi chú</label>

            <textarea class="form-control" id="note" name="note" rows="4">{{ old('note', $order->note ?? '') }}</textarea>

        </div>

    </div>

</div>

<hr>



{{-- PHẦN 2: CHI TIẾT SẢN PHẨM --}}

<h5>3. Chi tiết sản phẩm</h5>

<div class="row align-items-end mb-3">

    <div class="col-md-6">

        <label for="product-selector" class="form-label">Chọn sản phẩm để thêm vào đơn</label>

        <select id="product-selector" class="form-control">

            <option value="">-- Chọn sản phẩm --</option>

            @foreach($products as $product)

                {{-- Giả sử biến thể đầu tiên là mặc định để lấy giá, nếu không thì lấy giá gốc của sản phẩm --}}

                @php

                    $variantPrice = $product->variants->first()->price ?? $product->price_discount ?? $product->price;

                @endphp

                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $variantPrice }}">

                    {{ $product->name }} ({{ number_format($variantPrice, 0, ',', '.') }} đ)

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-2">

        <button type="button" class="btn btn-success" id="add-item-btn">Thêm sản phẩm</button>

    </div>

</div>



<div class="table-responsive">

    <table class="table table-bordered">

        <thead class="table-light">

            <tr>

                <th>Sản phẩm</th>


                <th style="width: 15%;">Số lượng</th>

                <th style="width: 20%;">Đơn giá (Tùy chỉnh)</th>

                <th style="width: 20%;" class="text-end">Thành tiền</th>

                <th style="width: 5%;">Xóa</th>

            </tr>

        </thead>

        <tbody id="order-items-table">

            @if(isset($order) && $order->orderItems)

                @foreach($order->orderItems as $index => $item)

                <tr data-product-id="{{ $item->product_id }}">

                    <td>

                        {{ $item->product_name }}

                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">

                        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}">

                    </td>

                    <td><input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1"></td>

                    <td><input type="number" class="form-control item-price" name="items[{{ $index }}][price]" value="{{ $item->product_price }}" min="0"></td>

                    <td class="text-end item-subtotal">{{ number_format($item->subtotal, 0, ',', '.') }} đ</td>

                    <td><button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="bi bi-trash"></i></button></td>

                </tr>

                @endforeach

            @endif

        </tbody>

        <tfoot>

            <tr>

                <th colspan="3" class="text-end">Tổng cộng:</th>

                <th class="text-end" id="grand-total">{{ number_format($order->total_price ?? 0, 0, ',', '.') }} đ</th>

                <th></th>

            </tr>

        </tfoot>

    </table>

</div>



<div class="mt-4">

    <button type="submit" class="btn btn-primary btn-lg">{{ isset($order) ? 'Cập nhật đơn hàng' : 'Tạo đơn hàng' }}</button>

</div>





{{-- SCRIPT XỬ LÝ ĐỘNG --}}

@push('js') {{-- Dùng đúng tên stack đã thống nhất --}}

<script>

document.addEventListener('DOMContentLoaded', function() {

    const userSelect = document.getElementById('user_id');

    const guestInfoDiv = document.getElementById('guest-info');

    const addBtn = document.getElementById('add-item-btn');

    const productSelect = document.getElementById('product-selector');

    const itemsTableBody = document.getElementById('order-items-table');



    function toggleGuestInfo() {

        if (userSelect.value) {

            guestInfoDiv.style.display = 'none';

        } else {

            guestInfoDiv.style.display = 'block';

        }

    }



    userSelect.addEventListener('change', function() {

        const selectedOption = this.options[this.selectedIndex];

        if (this.value) {

            document.getElementById('customer_name').value = selectedOption.dataset.name;

            document.getElementById('customer_phone').value = selectedOption.dataset.phone;

            document.getElementById('customer_address').value = selectedOption.dataset.address;

            document.getElementById('customer_email').value = selectedOption.dataset.email;

        } else {

            document.getElementById('customer_name').value = '';

            document.getElementById('customer_phone').value = '';

            document.getElementById('customer_address').value = '';

            document.getElementById('customer_email').value = '';

        }

        toggleGuestInfo();

    });



    addBtn.addEventListener('click', function() {

        const selectedOption = productSelect.options[productSelect.selectedIndex];

        if (!selectedOption.value) { return; }



        const productId = selectedOption.value;

        const productName = selectedOption.dataset.name;

        const productPrice = selectedOption.dataset.price;



        // KIỂM TRA SẢN PHẨM ĐÃ TỒN TẠI CHƯA

        const existingRow = itemsTableBody.querySelector(`tr[data-product-id="${productId}"]`);



        if (existingRow) {

            // NẾU ĐÃ TỒN TẠI -> TĂNG SỐ LƯỢNG

            const quantityInput = existingRow.querySelector('.item-quantity');

            quantityInput.value = parseInt(quantityInput.value) + 1;

            // Kích hoạt sự kiện 'input' để tự động cập nhật lại thành tiền

            quantityInput.dispatchEvent(new Event('input'));

        } else {

            // NẾU CHƯA TỒN TẠI -> THÊM DÒNG MỚI

            const itemIndex = Date.now();

            const newRowHTML = `

                            <tr data-product-id="${productId}">

                                <td>

                                    ${productName}

                                    <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">

                                    <input type="hidden" name="items[${itemIndex}][product_name]" value="${productName}">

                                </td>


                                <td><input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" value="1" min="1"></td>

                                <td><input type="number" class="form-control item-price" name="items[${itemIndex}][price]" value="${productPrice}" min="0"></td>

                                <td class="text-end item-subtotal">${formatCurrency(productPrice)}</td>

                                <td><button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="bi bi-trash"></i></button></td>

                            </tr>

            `;

            itemsTableBody.insertAdjacentHTML('beforeend', newRowHTML);

        }

        

        productSelect.value = ''; // Reset ô chọn sản phẩm

        updateGrandTotal();

    });



    itemsTableBody.addEventListener('input', function(e) {

        if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-price')) {

            updateRowSubtotal(e.target.closest('tr'));

        }

    });



    itemsTableBody.addEventListener('click', function(e) {

        const removeBtn = e.target.closest('.remove-item-btn');

        if (removeBtn) {

            removeBtn.closest('tr').remove();

            updateGrandTotal();

        }

    });



    function updateRowSubtotal(row) {

        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;

        const price = parseFloat(row.querySelector('.item-price').value) || 0;

        const subtotal = quantity * price;

        row.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);

        updateGrandTotal();

    }



    function updateGrandTotal() {

        let grandTotal = 0;

        document.querySelectorAll('#order-items-table tr').forEach(row => {

            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;

            const price = parseFloat(row.querySelector('.item-price').value) || 0;

            grandTotal += quantity * price;

        });

        document.getElementById('grand-total').textContent = formatCurrency(grandTotal);

    }



    function formatCurrency(number) {

        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number);

    }



    // Initial setup

    toggleGuestInfo();

});

</script>

@endpush