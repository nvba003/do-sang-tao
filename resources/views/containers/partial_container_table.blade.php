@forelse ($containers as $container)
    <tr class="bg-white hover:bg-gray-100 border-b" data-container-id="{{ $container->container_code }}" data-id="{{ $container->id }}">
        <td class="p-2 w-1/24 text-center mt-1 hidden sm:block"> <!-- Reduced padding and set a fixed width -->
            <input type="checkbox" class="checkItem">
        </td>
        <td class="text-center w-1/24 cursor-pointer">
            <button class="expand-button text-xs md:text-lg sm:text-base w-6 h-6 text-center text-gray-200 bg-cyan-500 rounded" data-target="#details{{ $container->id }}">
                +
            </button>
        </td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->container_code }}</td>
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
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">
            <span class="location-parent">{{ $container->location_parent_id ? $container->locationParent->location_name : '_' }}</span>
            <input type="text" class="text-xs md:text-lg sm:text-base location-parent-input hidden ml-2 border rounded p-1 w-6 sm:w-16" value="{{ $container->location_parent_id ? $container->locationParent->location_name : '' }}" maxlength="4">
        </td>
        <td class="w-2/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">
            <span class="location-name">{{ $container->location->location_name ?? '_' }}</span>
            <select class="text-xs md:text-lg sm:text-base location-name-input hidden ml-2 border rounded p-1 w-6 sm:w-16">
                @foreach($locations as $location)
                    @if($location->isChild() && $location->parent_id == $container->location_parent_id)
                        <option value="{{ $location->location_name }}" {{ $location->id == $container->location_id ? 'selected' : '' }}>{{ $location->location_name }}</option>
                    @endif
                @endforeach
            </select>
            <!-- <input type="text" class="text-xs md:text-lg sm:text-base location-name-input hidden ml-2 border rounded p-1 w-6 sm:w-16" value="{{ $container->location->location_name ?? '' }}"> -->
        </td>
        <td class="w-9/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->productapi->name }}</td>
        <td class="w-3/24 p-2 text-xs md:text-lg sm:text-base overflow-hidden text-ellipsis">{{ $container->notes }}</td>
    </tr>
    <tr id="details{{ $container->id }}" class="bg-gray-50 details-row hidden" data-related-container-id="{{ $container->container_code }}">
        <td colspan="100%" class="px-2 sm:px-6 pt-2 sm:pt-4 pb-4 text-xs md:text-base">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-1 space-y-2">
                <div class="mt-2"><strong>Cập nhật:</strong> {{ $container->updated_at }}</div>
                <div class="mt-2"><strong>SKU:</strong> {{ $container->productapi->sku }}</div>
                <div class="flex items-center"><strong>Đơn vị:</strong>
                    <span class="unit-display ml-2">{{ $container->unit }}</span>
                    <input type="text" class="text-xs sm:text-base unit-input hidden ml-2 border rounded p-1 w-28" value="{{ $container->unit }}">
                </div>
                <div class="flex items-center"><strong>Chi nhánh:</strong>
                    <span class="branch-display ml-2">{{ $container->branch->name }}</span>
                    <select class="branch-select hidden ml-2 border rounded px-2 py-1 text-xs sm:text-base">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branch->id == $container->branch_id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center"><strong>Ghi chú:</strong>
                    <span class="notes-display ml-2">{{ $container->notes }}</span>
                    <textarea class="notes-input hidden ml-2 border rounded p-1 text-xs sm:text-base" rows="1">{{ $container->notes }}</textarea>
                </div>
                <div class="flex py-2">
                    <button class="save-button hidden bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-10 ml-4 rounded">Lưu lại</button>
                </div>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center py-3 text-xs md:text-lg sm:text-base">Không tìm thấy thùng hàng nào.</td>
    </tr>
@endforelse
