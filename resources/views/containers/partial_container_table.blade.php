@forelse ($containers as $container)
    <tr>
        <td></td>    
        <td class="expand-button" data-target="#details{{ $container->id }}">+</td>    
        <td>{{ $container->container_id }}</td>
        <td>{{ $container->product_quantity }}</td>
        <td>{{ $container->location->parent->location_name ?? 'null' }}</td>
        <td>{{ $container->location->location_name ?? 'null' }}</td>
        <td>{{ $container->productapi->name }}</td>
    </tr>

    <tr id="details{{ $container->id }}" class="details-row">
        <td></td>
        <td colspan="100%">
            <div><strong>Cập nhật:</strong> {{ $container->updated_at }}</div>
            <div><strong>SKU:</strong> {{ $container->productapi->sku }}</div>
            <div><strong>Đơn vị:</strong> {{ $container->unit }}</div>
            <div><strong>Chi nhánh:</strong> {{ $container->branch->name }}</div>
            <div><strong>Ghi chú:</strong> {{ $container->notes }}</div>
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="3">Không tìm thấy thùng hàng nào.</td>
        </tr>
@endforelse