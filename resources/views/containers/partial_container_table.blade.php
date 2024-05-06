@forelse ($containers as $container)
    <tr class="bg-white hover:bg-gray-100 border-b" data-container-id="{{ $container->container_id }}">
        <td class="p-2 w-1/24 text-center mt-1 hidden sm:block"> <!-- Reduced padding and set a fixed width -->
            <input type="checkbox" class="checkItem">
        </td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="expand-button text-xs md:text-lg sm:text-base xl:w-1/2 w-full h-full text-center text-gray-200 bg-cyan-500 rounded" data-target="#details{{ $container->id }}">
                +
            </button>
        </td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->container_id }}</td>
        <td class="w-2/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">
            @php
                $quantity = floatval($container->product_quantity);
                if (intval($quantity) == $quantity) {
                    echo intval($quantity);
                } else {
                    echo $quantity;
                }
            @endphp
        </td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->location->parent->location_name ?? '_' }}</td>
        <td class="w-2/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->location->location_name ?? '_' }}</td>
        <td class="w-12/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->productapi->name }}</td>
    </tr>
    <tr id="details{{ $container->id }}" class="bg-gray-50 details-row hidden" data-related-container-id="{{ $container->container_id }}">
        <td></td>
        <td colspan="6" class="px-2 sm:px-4 py-2 text-xs md:text-base">
            <div><strong>Cập nhật:</strong> {{ $container->updated_at }}</div>
            <div><strong>SKU:</strong> {{ $container->productapi->sku }}</div>
            <div><strong>Đơn vị:</strong> {{ $container->unit }}</div>
            <div><strong>Chi nhánh:</strong> {{ $container->branch->name }}</div>
            <div><strong>Ghi chú:</strong> {{ $container->notes }}</div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center py-3 text-xs md:text-lg sm:text-base">Không tìm thấy thùng hàng nào.</td>
    </tr>
@endforelse
