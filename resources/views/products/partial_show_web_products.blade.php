@foreach ($products as $product)
    <tr>
        <td>
            <div class="checkbox-container">
                <input type="checkbox" class="order-checkbox checkItem" value="{{ $product->id }}" data-id="{{ $product->id }}" data-staff-name="{{ $product->staff }}">
            </div>
        </td>
        <td>
            <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $product->id }}" data-summary-order="{{ $product }}">+</button>
        </td>
        <td>{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y') }}</td>
        <td>{{ $product->notes }}</td>
        <td>
            <button class="btn btn-primary btn-sm btn-edit" data-order="{{ $product }}">Sửa</button>
            <!-- @if($product->is_entered == false)
                <button class="btn btn-success btn-sm btn-enter" data-id="{{ $product->id }}" data-entered="{{ $product->is_entered }}">Nhập</button>
            @else
                <span>Đã nhập</span>
            @endif -->
        </td>
    </tr>
    <!-- Chi tiết đơn hàng -->
    <tr id="details{{ $product->id }}" class="details-row" style="display: none;">
        <td colspan="11">
            <div id="productDetails{{ $product->id }}" class="product-details-container">
            </div>
        </td>
    </tr>

@endforeach
