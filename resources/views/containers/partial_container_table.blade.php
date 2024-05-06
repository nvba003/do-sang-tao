@forelse ($containers as $container)
    <tr class="bg-white hover:bg-gray-100">
        <td class="p-2 w-12"> <!-- Reduced padding and set a fixed width -->
            <input type="checkbox">
        </td>
        <td class="text-center p-2 w-12 font-semibold cursor-pointer expand-button" data-target="#details{{ $container->id }}">
            + <!-- Adjusted padding -->
        </td>
        <td class="p-2 text-xs md:text-sm overflow-hidden text-ellipsis" data-container-id="{{ $container->container_id }}">{{ $container->container_id }}</td>
        <td class="p-2 text-xs md:text-sm overflow-hidden text-ellipsis">{{ $container->product_quantity }}</td>
        <td class="p-2 text-xs md:text-sm overflow-hidden text-ellipsis">{{ $container->location->parent->location_name ?? '_' }}</td>
        <td class="p-2 text-xs md:text-sm overflow-hidden text-ellipsis">{{ $container->location->location_name ?? '_' }}</td>
        <td class="p-2 text-xs md:text-sm overflow-hidden text-ellipsis">{{ $container->productapi->name }}</td>
    </tr>
    <tr id="details{{ $container->id }}" class="bg-gray-50 details-row hidden" data-related-container-id="{{ $container->container_id }}">
        <td></td>
        <td colspan="6" class="px-4 py-3 text-xs md:text-sm">
            <div><strong>Cập nhật:</strong> {{ $container->updated_at }}</div>
            <div><strong>SKU:</strong> {{ $container->productapi->sku }}</div>
            <div><strong>Đơn vị:</strong> {{ $container->unit }}</div>
            <div><strong>Chi nhánh:</strong> {{ $container->branch->name }}</div>
            <div><strong>Ghi chú:</strong> {{ $container->notes }}</div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center py-3 text-xs md:text-sm">Không tìm thấy thùng hàng nào.</td>
    </tr>
@endforelse
