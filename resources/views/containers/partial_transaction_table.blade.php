@forelse ($transactions as $transaction)
    <tr class="bg-white hover:bg-gray-100 border-b" data-transaction-id="{{ $transaction->id }}">
        <td class="p-2 w-1/24 text-center mt-1 hidden sm:block">
            <input type="checkbox" class="checkItem">
        </td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="expand-button text-xs md:text-sm sm:text-base xl:w-1/2 w-full h-full text-center text-gray-200 bg-cyan-500 rounded" data-target="#details{{ $transaction->id }}">
                +
            </button>
        </td>
        <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $transaction->created_at }}</td>
        <td class="w-2/24 p-2 text-xs text-center sm:text-base overflow-hidden text-ellipsis">{{ $transaction->containers->container_code }}</td>
        <td class="w-2/24 p-2 text-center text-xs sm:text-base overflow-hidden text-ellipsis">
            @php
                $quantity = floatval($transaction->quantity);
                if (intval($quantity) == $quantity) {
                    echo intval($quantity);
                } else {
                    echo $quantity;
                }
            @endphp
        </td>
        <td class="w-3/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $transaction->transactionType->type_name }}</td>
        <td class="w-2/24 p-2 text-xs text-center sm:text-base overflow-hidden text-ellipsis">
            @php
                $quantity = floatval($transaction->inventoryHistory->quantity_after);
                if (intval($quantity) == $quantity) {
                    echo intval($quantity);
                } else {
                    echo $quantity;
                }
            @endphp
        </td>
        <td class="w-10/24 p-2 text-xs sm:text-base overflow-hidden text-ellipsis">{{ $transaction->productApi->name }}</td>
    </tr>
    <tr id="details{{ $transaction->id }}" class="bg-gray-50 details-row hidden" data-related-transaction-id="{{ $transaction->id }}">
        <td></td>
        <td colspan="100%" class="px-2 sm:px-4 py-2 text-xs md:text-base">
            <div><strong>Cập nhật:</strong> {{ $transaction->updated_at }}</div>
            <div><strong>SKU:</strong> {{ $transaction->productApi->sku }}</div>
            <div><strong>Loại:</strong> {{ $transaction->transactionType->type_name }}</div>
            <div><strong>User:</strong> {{ $transaction->user->name }}</div> 
            <div><strong>Ghi chú:</strong> {{ $transaction->inventoryHistory->notes }}</div> 
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center py-3 text-xs md:text-lg sm:text-base">Không tìm thấy thông tin nào.</td>
    </tr>
@endforelse
